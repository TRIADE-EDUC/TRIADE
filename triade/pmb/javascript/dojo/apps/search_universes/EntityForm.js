// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntityForm.js,v 1.12 2023/09/08 09:31:56 rtigero Exp $


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
        "apps/pmb/pmbEventsHandler",
        "dojo/dom-style",
        ], 
		function(declare,parser, Button, topic, lang, on, dom, query, domAttr, request, registry, domConstruct, DialogSimple, domForm, pmbEventsHandler, domStyle ){
	return declare(null, {
		type:null,
		id:null,
		className: null,
		indexation: null,
		signals: null,
		dijits:null,
		formName:null,
		constructor: function(params){			
			lang.mixin(this, params);
			this.signals = [];
			this.signals.push(topic.subscribe('ParametersFormsReady', lang.hitch(this, this.handleEvents)));
			this.signals.push(topic.subscribe('EntityTree', lang.hitch(this, this.handleEvents)));
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
//				if(this.testForm()){
//					topic.publish('formButton', 'saveNode',dom.byId(this.formName));
//					this.destroy();
//				}
//				evt.preventDefault();
//				return false;
			})));
		
			this.signals.push(on(dom.byId('save_button'),'click', lang.hitch(this, function(evt) {
				if(this.testForm()){
//					topic.publish('formButton', 'saveNode',dom.byId(this.formName));
//					this.destroy();
					return true;
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
			    domStyle.set(cancelButton,'display','none');
			}

			var duplicateButton = dom.byId('duplicate_button');
			const universeType = "universe";
			
			if(duplicateButton) {
				this.signals.push(on(duplicateButton,'click', lang.hitch(this, function(evt) {
					//il faut savoir si on duplique un segment ou un univers
					if(this.type == universeType) {
						topic.publish('formButton', 'startDuplicateUniverse', {id : this.id, type : this.type});
					} else {
						topic.publish('formButton', 'startDuplicateSegment', {id : this.id, type : this.type});
					}
					evt.preventDefault();
					return false;
				})));
			}
		
			pmbEventsHandler.initEvents(this);
		},
//		initEvents: function(node) {
//			var data_pmb_evt = JSON.parse(domAttr.get(node, 'data-pmb-evt'));
//			if (data_pmb_evt.class == 'EntityForm') {
//				if(typeof this[data_pmb_evt.method] == "function"){
//					this.signals.push(on(node, data_pmb_evt.type, lang.hitch(this, this[data_pmb_evt.method], data_pmb_evt.parameters)));	
//				}
//			}
//		},
		confirmDelete: function() {
			if(confirm(pmbDojo.messages.getMessage('search_universes', 'search_'+this.type+'_confirm_delete'))) {
				return true;
			}
			return false;
		},
		
		testForm: function(){
			if(dom.byId(this.type+'_label').value=='') {
				alert(pmbDojo.messages.getMessage('search_universes', 'search_universes_empty_label'));
				dom.byId(this.type+'_label').focus();
				return false;
			}
			return true;
		},
		destroy:function(){
			this.signals.forEach(function(signal){
				signal.remove();
			});
			this.dijits.forEach(function(dijit){
				dijit.destroyRecursive();
			});
		},
		loadDialog : function(params, evt, path) {
			var dijitId = params.entity_type+"_"+params.entity_id+"_dialog";
			if(!this.dijits[dijitId]){
				var myDijit = registry.byId(dijitId);
				if (myDijit) {
					myDijit.destroyDescendants();
					myDijit.destroy();
				}
				this.dijits[dijitId] = new DialogSimple({title: pmbDojo.messages.getMessage('search_universes', 'search_'+params.entity_type+'_'+params.action), executeScripts:true, id : dijitId, style:{width:'85%'}});				
				this.dijits[dijitId].attr('href', path);
				this.dijits[dijitId].startup();				
				this.signals.push(on(this.dijits[dijitId],"load", lang.hitch(this, function() {
//					query('[data-pmb-evt]',dom.byId(dijitId)).forEach(lang.hitch(this, this.initEvents));
					pmbEventsHandler.initEvents(this, dom.byId(dijitId));
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
			return this.dijits[dijitId];
		},
		
		hideDialog : function(params) {
			if (!params.className) {
				params.className = this.className;
			}
			var dijitId = params.entity_type + "_" + params.entity_id + "_dialog";
			if (this.dijits[dijitId]) {
				this.dijits[dijitId].hide();
				this.dijits[dijitId].destroyRecursive();
				this.dijits = [];
			}
		},
		
		removeDialog : function(params) {
			var dijitId = params.entity_type+"_"+params.entity_id+"_dialog";
			if(this.dijits[dijitId]){
				this.dijits[dijitId].destroy();
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
					alert('Erreur lors de l\'enregistrement.');
				});
			} else {
				alert('Veuillez renseigner un libellé.');
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
	});
});