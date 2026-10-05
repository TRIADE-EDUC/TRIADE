// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TreeContainer.js,v 1.3 2019/03/29 16:16:25 tsamson Exp $


define([
        "dojo/_base/declare", 
        "dijit/layout/ContentPane", 
        "dojo/parser", 
        "apps/pmb/tree_interface/EntityTree", 
        "dijit/layout/BorderContainer",
        "dijit/form/Button",
        "apps/frbr/FormContainer",
        "dojo/topic",
        "dojo/_base/lang"], 
		function(declare,ContentPane,parser,EntityTree,BorderContainer, Button, FormContainer, topic, lang){
	return declare([BorderContainer], {
		tree : null,
		leftContentPane: null,
		constructor: function(){
			this.own(topic.subscribe('FormContainer', lang.hitch(this, this.handleEvents)),
					topic.subscribe('EntityTree', lang.hitch(this, this.handleEvents)));
		},
		postCreate:function(){
			this.inherited(arguments);
			this.leftContentPane = new ContentPane({region : 'left', splitter:true, style: {width: '25%'} });
			this.initTree();
		},
		
		initTree: function(data){
			if (!data) {
				data = this.data;
			}
			this.tree = new EntityTree(data);
		},
		
		init: function(){
			
		},
		
		parseAddedContent: function(){
			
		},
		
		cutTree: function(evtArgs){
			this.tree.destroy();
			this.tree = null;
			if (typeof evtArgs.tree_data != "object") {
				evtArgs.tree_data = JSON.parse(evtArgs.tree_data);
			}
			this.initTree(evtArgs.tree_data);
			this.leftContentPane.addChild(this.tree);
			this.selectTreeNodeById(evtArgs.status, evtArgs.type);
		},	
		
	    recursiveHunt : function(item, itemPath){
	    	itemPath.unshift(item);
	    	if (!item.root) {
	    		var parent = this.tree.memoryStore.getParent(item)[0];
	    		this.recursiveHunt(parent,itemPath);	    		
	    	}
	    	return itemPath;
	    },

	    selectTreeNodeById : function(id, type){
	    	var itemId = 0;
	    	if (type != "page") {
	    		itemId = type + '_' + id;
	    	}
	        var item = this.tree.memoryStore.query({'id': itemId})[0];
	        var itemPath = new Array();
	        if (item) {
	        	this.tree.set("path",this.recursiveHunt(item, itemPath));	        
	        }
	    },
	});
});