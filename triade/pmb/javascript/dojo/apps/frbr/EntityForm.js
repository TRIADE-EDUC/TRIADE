// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntityForm.js,v 1.16 2022/01/21 13:47:24 tsamson Exp $


define([
        "dojo/_base/declare", 
        "dojo/parser", 
        "dijit/form/Button",
        "dojo/topic",
        "dojo/_base/lang",
        "dojo/on",
        "dojo/dom",
        "dojo/query",
        "dojo/dom-attr",
        "dojo/request",
        "dijit/registry",
        "dojo/dom-construct",
        "apps/pmb/PMBDojoxDialogSimple",
        "dojo/dom-form",
        "dojo/request/xhr",
        ], 
		function(declare,parser, Button, topic, lang, on, dom, query, domAttr, request, registry, domConstruct, DialogSimple, domForm, xhr){
	return declare(null, {
		type:null,
		id:null,
		className: null,
		indexation: null,
		signals: null,
		msg:null, /** TODO **/
		dijits:null,
		formName:null,
		numPage:null,
		constructor: function(params){
			lang.mixin(this, params);
			this.signals = [];
			this.signals.push(topic.subscribe('ParametersFormsReady', lang.hitch(this, this.handleEvents)));
			this.signals.push(topic.subscribe('EntityTree', lang.hitch(this, this.handleEvents)));
			this.formName = this.className+'_form';
			this.init();
			this.dijits = [];
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'leafRootClicked':
				case 'leafClicked':
					this.destroy();
					break;
				default:
					if (typeof this[evtType] == 'function') {
						this[evtType](evtArgs);
					}
					break;
			}
		},
		
		init: function(){
			this.signals.push(on(dom.byId(this.formName),'submit', lang.hitch(this, function(evt) {
				if(this.testForm()){
					this.saveForm();
//					topic.publish('formButton', 'saveNode',dom.byId(this.formName));
					this.destroy();
				}
				evt.preventDefault();
				return false;
			})));
			this.signals.push(on(dom.byId('save_button'),'click', lang.hitch(this, function(evt) {
				if(this.testForm()){
					this.saveForm();
					//topic.publish('formButton', 'saveNode',dom.byId(this.formName));
					this.destroy();
				}
				evt.preventDefault();
				return false;
			})));
			
			var deleteButton = dom.byId('delete_button');
			if (deleteButton) {
				this.signals.push(on(deleteButton,'click', lang.hitch(this, function(evt) {
					if(this.confirmDelete()){
						topic.publish('formButton', 'checkChildrenToDelete', {id : this.id, type : this.type});						
					}
					evt.preventDefault();
					return false;
				})));
			}
			
			var cancelButton = dom.byId('cancel_button');
			if (cancelButton) {
				this.signals.push(on(cancelButton,'click', lang.hitch(this,function(evt){
					topic.publish('formButton', 'clearForm');
					topic.publish('EntityForm', 'canceled');
					this.destroy();
					evt.preventDefault();
					return false;
				})));				
			}
			query('[data-pmb-evt]').forEach(lang.hitch(this, this.initEvents));
		},
		initEvents: function(node) {
			var data_pmb_evt = JSON.parse(domAttr.get(node, 'data-pmb-evt'));			
			if (data_pmb_evt.class == 'EntityForm') {
				if(typeof this[data_pmb_evt.method] == "function"){
					this.signals.push(on(node, data_pmb_evt.type, lang.hitch(this, this[data_pmb_evt.method], data_pmb_evt.parameters)));	
				}
			}
		},
		confirmDelete: function() {
			if(confirm(this.msg['frbr_entity_common_entity_'+this.type+'_confirm_delete'])) {
				return true;
			}
			return false;
		},
		
		testForm: function(){
			if(dom.byId(this.type+'_name').value=='') {
				alert(this.msg['frbr_entity_common_entity_'+this.type+'_name_empty']);
				dom.byId(this.type+'_name').focus();
				return false;
			}
			if (dom.byId(this.type+'_datasource_choice') && (dom.byId(this.type+'_datasource_choice').value=='' || dom.byId(this.type+'_datasource_choice').value == 'frbr_entity_common_datasource')) {
				alert(this.msg['frbr_entity_common_entity_'+this.type+'_datasource_empty']);
				dom.byId(this.type+'_datasource_choice').focus();
				return false;
			}
			if (dom.byId(this.type+'_entity_type') && dom.byId(this.type+'_entity_type').value=='') {
				alert(this.msg['frbr_entity_common_entity_'+this.type+'_sub_datasource_empty']);
				return false;
			}
			if (dom.byId(this.type+'_view_choice') && dom.byId(this.type+'_view_choice').value=='') {
				alert(this.msg['frbr_entity_common_entity_'+this.type+'_view_empty']);
				return false;
			}
			return true;
		},
		frbrEntityLoadElemForm: function(params, evt){
			// params : elem,id,dom_id
			if(evt && evt.target.value){
				params.elem = evt.target.value;
			}
			//filtre sur les autorites perso
			var authperso_num = dom.byId("datanode_authperso_num");
			request.post('./ajax.php?module=cms&categ=frbr_entities&elem='+params.elem+'&action=get_form&id='+params.id, {
				data : {
					frbr_entity_class: this.className,
					dom_node_id: params.domId,
					num_page: (params.numPage ? params.numPage : ''),
					filter_refresh : (params.filterRefresh == "1" ? "1" : "0"),
					sort_refresh : (params.sortRefresh == "1" ? "1" : "0"),
					authperso_num : (authperso_num && authperso_num.value ? authperso_num.value : "0"),
				},
			}).then(lang.hitch(this, function(data){
				try{
					var data = JSON.parse(data);
					for(var key in data){
						if (dom.byId(key)) {
							var widgets = query("[widgetid]", dom.byId(key));
							widgets.forEach(function(widget){
								var widget = registry.byId(widget.getAttribute("id"));						
								if(widget){
									widget.destroy();				
								}
							});	
						}
						if(data[key]) {							
							domConstruct.place(data[key],dom.byId(key),'only');
							query('[data-pmb-evt]',dom.byId(key)).forEach(lang.hitch(this, this.initEvents));		
						} else {
							dom.byId(key).innerHTML = "";
						}										
					}
				}catch(e){
					if(registry.byId(params.domId)){
						registry.byId(params.domId).set('content',data);	
					}
					query('[data-pmb-evt]',dom.byId(params.domId)).forEach(lang.hitch(this, this.initEvents));
					
				}
				if(registry.byId(params.domId)) {
					preLoadScripts(registry.byId(params.domId).domNode);
				}
			}));
		},

		frbrEntityLoadManagedElemForm: function (params, evt){
			// params : elem,selected_index,id,dom_id
			if(evt){
				params.selectedIndex = evt.target.value;
			}
			if (params.indexation) {
				this.indexation = params.indexation; 
			}
			if (params.className) {
				this.className = params.className; 
			}
			request.post('./ajax.php?module=cms&categ=frbr_entities&elem='+params.elem+'&action=get_form&id='+params.id, {
				data : {
					frbr_entity_class: this.className,
					frbr_indexation_type: (this.indexation.type ? this.indexation.type : ''),
					frbr_indexation_path: (this.indexation.path ? this.indexation.path : ''),
					frbr_selected_manage: params.selectedIndex,
					num_page: (params.numPage ? params.numPage : ''),
				}
			}).then(lang.hitch(this, function(data) {
				registry.byId(params.domId).set('content',data);
				query('[data-pmb-evt]',dom.byId(params.domId)).forEach(lang.hitch(this, this.initEvents));
			}));
		},
		destroy:function(){
			this.signals.forEach(function(signal){
				signal.remove();
			});
			this.dijits.forEach(function(dijit){
				dijit.destroyRecursive();
			});
		},
		loadDialog : function(params, evt) {
			if (!params.className) {
				params.className = this.className;
			}
			var dijitId = params.className+"_"+params.element+"_dialog_"+params.idElement+"_"+params.manageId;
			if(!this.dijits[dijitId]){
				this.dijits[dijitId] = new DialogSimple({title: this.msg['frbr_entity_common_entity_'+this.type+'_'+params.element+'_edit'], executeScripts:true, id : params.className+"_"+params.element+"_dialog_"+params.idElement+"_"+params.manageId, style:{width:'85%'}});
				var path = './ajax.php?module=cms&categ=frbr_entities&elem='+params.className+'&action=get_manage_form&quoi='+params.quoi+'&id_element='+params.idElement+'&manage_id='+params.manageId+'&num_page='+params.numPage;
				if (params.authperso_id) {
					path += "&authperso_id="+params.authperso_id;
				}
				this.dijits[dijitId].attr('href', path);
				this.dijits[dijitId].startup();				
				this.signals.push(on(this.dijits[dijitId],"load", lang.hitch(this, function() {
					query('[data-pmb-evt]',dom.byId(dijitId)).forEach(lang.hitch(this, this.initEvents));
				})));
				this.signals.push(on(this.dijits[dijitId],"hide", lang.hitch(this, function() {
					//TODO à revoir
					this.dijits[dijitId].destroyRecursive();
//					this.dijits[dijitId].destroy();
					//this.dijits = this.dijits.splice(dijitId,1);					
					this.dijits = [];					
				})));
			}
			this.dijits[dijitId].resize();
			this.dijits[dijitId].show();
		},
		hideDialog : function(params) {
			if (!params.className) {
				params.className = this.className;
			}
			var dijitId = params.className+"_"+params.element+"_dialog_"+params.idElement+"_"+params.manageId;
			if(this.dijits[dijitId]){
				this.dijits[dijitId].hide();
//				this.dijits[dijitId].destroy();
			}
		},
		manageSaveForm : function (params) {
			if (!params.className) {
				params.className = this.className;
			}
			var myForm = dom.byId(params.className+"_"+params.element+"_"+params.manageId+"_manage_form");
			var name = dom.byId(params.element+"_name").value;
			if (name) {
				var formData = JSON.parse(domForm.toJson(myForm));
				formData.type = params.element;
				request.post(myForm.action, {
					data : formData,
					handleAs : 'json'
				}).then(lang.hitch(this, function(data) {
					if(data.status == '1'){
						this.majSelector(data, params);				
						if (params.hide) {
							this.hideDialog(params);
						}	
					}else{
						alert(data.message);
					}
				}),function(err){
					alert(this.msg['frbr_entity_common_entity_save_error']);				
				});
			} else {
				alert(this.msg['frbr_entity_common_entity_name_empty']);
			}
			
		},
		majSelector : function(data, params) {
			if(!data.status) {
				return false;
			}
			var options = dom.byId(params.type+"_"+params.element+"_choice").children;
			var nb_options = options.length;
			if(data.deleted) {
				for(var i = 0; i < nb_options; i++) {
					if(options[i].value == params.element+data.manage_id) {
						dom.byId(params.type+"_"+params.element+"_choice").removeChild(dom.byId(params.type+"_"+params.element+"_choice").options[i]);
						var newParams = {
								elem : 'frbr_entity_common_'+params.element,
								domId : params.element + "_form"
						};
						this.frbrEntityLoadManagedElemForm(newParams);
						break;
					}
				}
			} else {
				var found = false;
				for(var i = 0; i < nb_options; i++) {
					if(options[i].value == params.element+data.manage_id) {
						options[i].innerHTML = data.name;
						found = true;
						var newParams = {
								selectedIndex : params.element+data.manage_id,
								elem : 'frbr_entity_common_'+params.element,
								domId : params.element + "_form"
						};
						this.frbrEntityLoadManagedElemForm(newParams);
						break;
					}
				}
				if(!found) {
					var element = document.createElement('option');
					element.setAttribute('value', params.element+data.manage_id);
					element.setAttribute('selected', 'selected');
					element.innerHTML = data.name;
					dom.byId(params.type+"_"+params.element+"_choice").appendChild(element);
					var newParams = {
							selectedIndex : params.element+data.manage_id,
							elem : 'frbr_entity_common_'+params.element,
							domId : params.element + "_form"
					};
					this.frbrEntityLoadManagedElemForm(newParams);
				}
			}
		},
		manageDeleteForm : function(params) {
			if (!params.className) {
				params.className = this.className;
			}
			var myForm = dom.byId(params.className+"_"+params.element+"_"+params.manageId+"_manage_form");
			myForm[params.element + "_delete"].value = params.manageId;
			this.manageSaveForm(params);
		},
		loadParametersForm : function(params, evt){
			//params : id, type, page
			if(evt && evt.target.value){
				params.id = evt.target.value;
			}
			topic.publish("FormContainer","parentChange", {parentId : params.id});
			request.get("./ajax.php?module=cms&categ=frbr_entities&action=get_parameters_form&type="+params.type+"&id="+params.id+"&num_page="+params.page,{
				handleAs : "text/html",
			}).then(lang.hitch(this,function(data){	
				if (data) {
					var widgets = query("[widgetid]", dom.byId("parameters_form"));
					widgets.forEach(function(widget){
						var widget = registry.byId(widget.getAttribute("id"));
						if(widget){
							widget.destroy();				
						}
					});		
					dom.byId("parameters_form").innerHTML = data;
					preLoadScripts(dom.byId("parameters_form"));
					query('[data-pmb-evt]',dom.byId("parameters_form")).forEach(lang.hitch(this, this.initEvents));
				}
			}));
		},
		saveForm : function() {
			var form = dom.byId(this.formName);
			xhr(form.action + '&num_page=' + this.numPage,{
				data :JSON.parse(domForm.toJson(form.id)),
				handleAs: "json",
				method:'POST'
			}).then(lang.hitch(this,function(response){
				if (response.status) {
					topic.publish('EntityForm', 'saved', response);
				}				
			}));
		},
	});
});