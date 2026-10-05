// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TreeContainer.js,v 1.7 2020/10/16 13:02:33 qvarin Exp $


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
		constructor: function(){
			this.own(
				topic.subscribe('ObjectStoreModel', lang.hitch(this, this.handleEvents)),
				topic.subscribe('formButton', lang.hitch(this, this.handleEvents))
			);
		},
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
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'updateTree':
					this.cutTree(evtArgs);
					break;
				case 'leafRootClicked':			
				case 'leafClicked':
//					this.disabledButtons(evtArgs);
					break;
				case 'selectTreeNodeById':
					this.selectTreeNodeById(evtArgs.id, evtArgs.type)
					break;
				case 'selectSegementNode':
					this.selectSegementNode(evtArgs.universeId, evtArgs.segementId)
					break;
					
			}
		},
		buildDropDown: function(){
			var menu = new DropDownMenu({style: "display:none;"});
			var universeItem = new MenuItem({
				label: pmbDojo.messages.getMessage('search_universes', 'search_universes_univers'),
				onClick: lang.hitch(this, function(){
					topic.publish('TreeContainer', 'openForm', {url:this.data.creation_links.universe, link_save: this.data.save_links.universe});
				})
			});
			
			var facetItem = new MenuItem({
				label: pmbDojo.messages.getMessage('search_universes', 'search_universes_facet'),
				onClick: lang.hitch(this, function(){
					topic.publish('TreeContainer', 'openForm', {url:this.data.creation_links.facet, link_save: this.data.save_links.facet});
				})
			});
			
			/*
			var searchItem = new MenuItem({
				label: pmbDojo.messages.getMessage('search_universes', 'search_universes_search'),
				onClick: lang.hitch(this, function(){
					topic.publish('TreeContainer', 'openForm', {url:this.data.creation_links.search_perso, link_save: this.data.save_links.search_perso});
				})
			});
			
			menu.addChild(searchItem);
			*/
			menu.addChild(universeItem);
			menu.addChild(facetItem);
			
			var button = new DropDownButton({
				label: pmbDojo.messages.getMessage('search_universes', 'search_universes_add'),
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
	    selectSegementNode : function(universeId, segementId){
	        var items = this.tree.memoryStore.query({'real_id': segementId, 'parent': 'universe_'+universeId});
	        if (items.length > 0) {
				var item = items[0];
				if (item) {
					var itemPath = new Array();
					this.tree.set("path",this.recursiveHunt(item, itemPath));	        
				}
				if(item && item.link_edit){
					topic.publish("EntityTree","leafClicked", item);	
				}
			}
	    },
	});
});