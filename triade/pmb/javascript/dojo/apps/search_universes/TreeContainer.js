// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TreeContainer.js,v 1.4 2018-04-13 08:28:40 vtouchard Exp $


define([
        "dojo/_base/declare", 
        "dijit/layout/ContentPane", 
        "dojo/parser", 
        "apps/search_universes/EntityTree", 
        "apps/pmb/tree_interface/TreeContainer",
        "dijit/form/Button",
        "apps/search_universes/FormContainer",
        "dojo/topic",
        "dojo/_base/lang",
        "dijit/form/DropDownButton",
        "dijit/DropDownMenu",
        "dijit/MenuItem"], 
		function(declare,ContentPane,parser,EntityTree,TreeContainer, Button, FormContainer, topic, lang, DropDownButton, DropDownMenu, MenuItem){
	return declare([TreeContainer], {
		tree : null,
		leftContentPane: null,
		postCreate:function(){
			this.inherited(arguments);			
			
			var dropDown = this.buildDropDown();
			this.leftContentPane.addChild(dropDown);
			dropDown.startup();
			
			this.leftContentPane.addChild(this.tree);
			this.addChild(this.leftContentPane);
			
			var formContainer = new FormContainer({region:'center', splitter:true});
			this.addChild(formContainer);
		},
		
		initTree: function(){
			this.tree = new EntityTree(this.data);
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'updateTree':
					this.cutTree(evtArgs);
					break;
				case 'leafRootClicked':			
				case 'leafClicked':
//					this.disabledButtons(evtArgs);
					break;
			}
		},
		buildDropDown: function(){
			var menu = new DropDownMenu({style: "display:none;"});
			var universeItem = new MenuItem({
				label: 'Univers',
				onClick: lang.hitch(this, function(){
					topic.publish('TreeContainer', 'openForm', {url:this.data.creation_links.universe, link_save: this.data.save_links.universe});
				})
			});
			
			var facetItem = new MenuItem({
				label: 'Facette',
				onClick: lang.hitch(this, function(){
					topic.publish('TreeContainer', 'openForm', {url:this.data.creation_links.facet, link_save: this.data.save_links.facet});
				})
			});
			
			var searchItem = new MenuItem({
				label: 'Recherche',
				onClick: lang.hitch(this, function(){
					topic.publish('TreeContainer', 'openForm', {url:this.data.creation_links.search, link_save: this.data.save_links.search});
				})
			});
			menu.addChild(universeItem);
			menu.addChild(facetItem);
			menu.addChild(searchItem);
			
			var button = new DropDownButton({
				label: 'Ajouter',
				name: 'create_entity_selector',
				dropDown: menu,
				id: 'create_entity_selector'
			});
			
			return button;
		},
		initTree: function(data){
			if (!data) {
				data = this.data;
			}
			this.tree = new EntityTree(data);
		},
		
		cutTree: function(evtArgs){
			this.tree.destroy();
			this.tree = null;
			if (typeof evtArgs.tree_data != "object") {
				evtArgs.tree_data = JSON.parse(evtArgs.tree_data);
			}
			
			this.initTree(evtArgs.tree_data);
			this.leftContentPane.addChild(this.tree);
			this.selectTreeNodeById(evtArgs.entity.id, evtArgs.entity.type);
		},	
		
		 selectTreeNodeById : function(id, type){
	        var item = this.tree.memoryStore.query({'real_id': id, 'entity_type' : type})[0];
	        var itemPath = new Array();
	        if (item) {
	        	this.tree.set("path",this.recursiveHunt(item, itemPath));	        
	        }
	        if(item && item.link_edit){
	        	topic.publish("EntityTree","leafClicked",item);	
	        }
	    },
	});
});