// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchUI.js,v 1.6 2018/02/21 10:00:58 vtouchard Exp $


define(["dojo/_base/declare", 
        "dojox/layout/ContentPane", 
        "dojo/dom-construct", 
        "dojo/dom", 
        "dojo/on", 
        "dojo/topic",
        "dojo/_base/lang",
        "dijit/registry",
        "dijit/form/Button",
        "dijit/form/CheckBox",
        "dijit/form/ValidationTextBox",
        "apps/frbr/cataloging/EntitySelector",
        "apps/frbr/cataloging/FormSelectorSearch",
        "dojo/query",
        "dojo/dom-construct"], function(declare, ContentPane, domConstruct, dom, on, topic, lang, registry, Button, CheckBox, ValidationTextBox, EntitySelector, FormSelectorSearch, query, domConstruct){
	
	return declare([ContentPane, EntitySelector], {
		datanodeId:0,
		doLayout: false,
		formSelectorSearch:null,
		postCreate:function(){
			this.own(
				topic.subscribe('ItemsListUI', lang.hitch(this, this.handleEvents))
			);
		},
		handleEvents: function(evtType,evtArgs){
			//console.log('searchUI', evtType, evtArgs);
			switch(evtType){
//				case 'addItems':
//					this.displaySearchInterface(evtArgs.itemType);
//				break;
				case "itemsListRefreshed":
					this.display_raz();
					break;			
				case "showItemsList":
					this.hide();
					break;
				case "displayRaz":
					this.display_raz();
					break;
				case "datanodeDeleted":
					this.display_raz();
					break;
			}			
		},
		
		onShow: function(){
			this.inherited(arguments);
			if(!this.menuCreated){
//				this.createMenu(this.displaySearchInterfaces);
				this.createSelector(this.displaySearchInterface);
				this.menuCreated = 1;
			}
			
		},
		displaySearchInterface: function(url){
			var url = url.split('&action')[0];
//			console.log('itemType', itemType, './select.php?tab=frbr&what='+itemType);
//			console.log('URL: ', url, 'Common: ', "./ajax.php?module=selectors&what=auteur");
			if(this.formSelectorSearch){
				this.formSelectorSearch.destroyRecursive();
				this.formSelectorSearch = null;
			}
			if(query('#containerNode', this.containerNode).length == 0){
				domConstruct.create('div', {id: "containerNode"}, this.containerNode);
			}
//			domConstruct.empty(query('#containerNode', this.containerNode)[0]);
			this.formSelectorSearch = new FormSelectorSearch({doLayout: false, selectorURL:url, multicriteriaMode: "10"}, query('#containerNode', this.containerNode)[0]);
			
			topic.publish('AddUI', 'setSelectorDefaultValue', {value : url + '&action=add'});
			
			//On récupère le tab container "Graphe/Recherche";
//			this.getParent().selectChild(this, true);
		},
//		buildRendering: function(){
//			this.inherited(arguments);
//		},
//		destroy: function(){
//			this.inherited(arguments);
//		},
		
		showForm: function(data){	
			this.destroyDescendants();
		},
		display_raz: function(){		
			this.destroyDescendants();
			this.searchId = 0;
		},
		show:function(){
			if(this.domNode.style.display == "none"){
				this.domNode.style.display = "block";
			}
		},
		hide:function(){
			if(this.domNode.style.display != "none"){
				this.domNode.style.display = "none";
			}
		}
	});
});