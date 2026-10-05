// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormContainer.js,v 1.13 2023/09/08 09:31:56 rtigero Exp $


define(["dojo/_base/declare", 
        "apps/pmb/tree_interface/FormContainer", 
        "dojo/store/Memory", 
        "dijit/tree/ObjectStoreModel", 
        "dojo/_base/lang",
        "dijit/form/Button",
        "dojo/dom-construct",
        "dojo/request/xhr",
        "dojo/_base/lang",
        "dojo/topic",
        "dojo/dom-form",
        "dojo/query",
        "dijit/registry",
        "dojo/on",
        "dojo/dom-attr",
        "apps/pmb/pmbEventsHandler",
        "dojo/dom-style",
        ], 
        function(declare, FormContainer, Memory, ObjectStoreModel, lang, Button, domConstruct, xhr, lang, topic, domForm, query, registry, on, domAttr, pmbEventsHandler, domStyle){
	return declare([FormContainer], {
		currentItem: null,
		addNewEntity : null,
		dijits : [],
		signals : [],
		postCreate:function(){
			this.inherited(arguments);			
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'leafClicked':			
					this.requestContent(evtArgs);
					break;
				case 'addNode':
					this.addNode(evtArgs);
					break;
				case 'saveNode':
					this.saveNode(evtArgs);
					break;
				case 'deleteNode':
					this.deleteNode(evtArgs);
					break;
				case 'clearForm': 
					this.clearContentPane();
					break;
				case 'leafRootClicked':			
					this.requestRootContent(evtArgs);
					break;
				case 'openForm':
					this.currentItem = {link_save: evtArgs.link_save};
					this.set('href', evtArgs.url);
					break;
				case 'loadNewContent':
					if (evtArgs.addNewEntity) {
						this.addNewEntity = true;
					}
					this.loadContent(evtArgs.html);
					break;
				case 'duplicateSegment':
				case 'duplicateUniverse':
					this.duplicateItem(evtArgs);
					break;
			}			
		},
		requestContent: function(item){
//			var num_parent_from_leaf = 0;
//			if (item.parent) {
//				num_parent_from_leaf = item.parent.split('_')[1];
//			}
//			var id_from_leaf = item.id.split('_')[1];
			this.addNewEntity = false;
			this.currentItem = item;
			this.showPatience();
			xhr(item.link_edit, {
				handleAs: "text"
			}).then(lang.hitch(this,this.loadContent));
		},
		requestRootContent: function(item){
			xhr("./ajax.php?module=cms&categ=frbr_entities&action=get_form&type=page&num_page="+item.page, {
				handleAs: "text"
			}).then(lang.hitch(this,this.loadContent));
		},
		
		addNode: function(arg){
			var num_parent_from_leaf = 0; 
			if (!arg.selectedItem.root) {
				num_parent_from_leaf = arg.selectedItem.id.split('_')[1];
			}
			xhr("./ajax.php?module=cms&categ=frbr_entities&action=get_form&type="+arg.type+"&num_page="+arg.selectedItem.page+"&num_parent="+num_parent_from_leaf, {
				handleAs: "text"
			}).then(lang.hitch(this,this.loadContent));
		},
		
		saveNode : function(form) {
			xhr(form.action + '&num_page=' + this.numPage,{
				data :JSON.parse(domForm.toJson(form.id)),
				handleAs: "json",
				method:'POST'
			}).then(lang.hitch(this,function(response){
				if (response.status) {
					topic.publish('FormContainer', 'updateTree', response);
					var item = {id : response.type+'_'+response.status, page : this.numPage, type : response.type};
					this.requestContent(item);
				}				
			}));
		},
		
		deleteNode : function(params) {
			xhr("./ajax.php?module=admin&categ=search_universes&sub="+params.type+"&action=delete&id="+params.id,{
				handleAs: "json",
				method:'GET'
			}).then(lang.hitch(this,function(response){
				if (response.status) {
					topic.publish('FormContainer', 'updateTree', response);
					this.set('content', '');
				}	
			}));
		},
		
		loadContent:function(data){ //Called by the xhr promise (then)
			var widgets = query('[widgetid]', this.domNode);
			widgets.forEach(function(widget){
				var widget = registry.byId(widget.getAttribute('id'));
				if(widget){
					widget.destroy();
				}
			});
			this.set("content", data);
			preLoadScripts(this.domNode);
			pmbEventsHandler.initEvents(this, this.domNode);
			topic.publish('FormContainer', 'formLoaded');
			this.hidePatience();
		},
		onLoad: function(){
			var form = query('form', this.containerNode)[0];
			if (typeof form != 'undefined'){
				on(form, 'submit', lang.hitch(this, this.postForm));
			}
//			Array.prototype.slice.call(query('input[onclick]', this.containerNode)).forEach(button => {
//				domConstruct.destroy(button);
//			});
			Array.prototype.slice.call(query('input', this.containerNode)).forEach(button => {
				if((domAttr.get(button, 'id') == 'cancel_button') || (domAttr.get(button, 'id') == 'btexit')) {
					domStyle.set(button,'display','none');
				}
				if (domAttr.get(form, 'id') == 'facette_form') {
					if (domAttr.get(button, 'id') == 'delete_button') {
						domStyle.set(button,'display','none');
					}
				}
			});
		},
		postForm: function(e){
			e.preventDefault();
			this.showPatience();
			var form = query('form', this.containerNode)[0];
			if(!domAttr.get(form, 'id')){
				domAttr.set(form, 'id', domAttr.get(form, 'name'));
			}
			
			if(this.currentItem.link_save.indexOf('!!id!!') != -1){
				this.currentItem.link_save = this.currentItem.link_save.replace('!!id!!', '0');
			}			
			
			var action = this.currentItem.link_save;
			if (this.addNewEntity) {
				action = domAttr.get(form, 'action');
			}
			xhr(action,{
				data :JSON.parse(domForm.toJson(form.id)),
				handleAs: "json",
				method:'POST',
			}).then(lang.hitch(this,function(response){
				if (response) {
					topic.publish('FormContainer', 'updateTree', response);
//					var item = {id : response.type+'_'+response.status, page : this.numPage, type : response.type};
//					this.requestContent(item);
				}
				this.hidePatience();
			}));
			
		},

		loadDialog : function(params, evt, path) {
			var dijitId = params.entity_type+"_"+params.entity_id+"_dialog";
			if(!this.dijits[dijitId]){				
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
			var dijitId = params.entity_type+"_"+params.entity_id+"_dialog";
			if(this.dijits[dijitId]){
				this.dijits[dijitId].hide();
//				this.dijits[dijitId].destroy();
			}
		},
		duplicateItem : function(params){
			this.showPatience();
			xhr(params.action,{
				handleAs: "json",
				method:'GET'
			}).then(lang.hitch(this,function(response){
				this.hidePatience();
				if (response.status) {
					topic.publish('FormContainer', 'updateTree', response);
					this.set('content', '');
				}	
			}));
		}
	});
});