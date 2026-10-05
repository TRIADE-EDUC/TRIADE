// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TreeContainer.js,v 1.10 2019/03/29 16:16:25 tsamson Exp $


define([
        "dojo/_base/declare", 
        "dijit/layout/ContentPane", 
        "dojo/parser", 
        "apps/frbr/EntityTree", 
        "apps/pmb/tree_interface/TreeContainer",
        "dijit/form/Button",
        "apps/frbr/FormContainer",
        "dojo/topic",
        "dojo/_base/lang"], 
		function(declare,ContentPane,parser,EntityTree,TreeContainer, Button, FormContainer, topic, lang){
	return declare([TreeContainer], {
		tree : null,
		leftContentPane: null,
		postCreate:function(){
			this.inherited(arguments);
			this.addDatanode = new Button(
					{
						label : pmbDojo.messages.getMessage('frbr', 'frbr_add_datanode'),
						disabled : true,
						onClick: lang.hitch(this, function(){
							topic.publish('TreeContainer', 'addNode', {selectedItem : this.tree.selectedItem, type : 'datanode'});
						})
					}
			);
			
			this.addFrame = new Button(
					{
						label : pmbDojo.messages.getMessage('frbr', 'frbr_add_frame'),	
						disabled : true,
						onClick: lang.hitch(this, function(){
							topic.publish('TreeContainer', 'addNode', {selectedItem : this.tree.selectedItem, type : 'cadre'});
						})
					}
			);
			
			this.expandAllButton = new Button(
					{
						label : "+",
						onClick: lang.hitch(this, function(){
							topic.publish('TreeContainer', 'expandAll', {});
						})
					}
			);
			
			this.collapseAllButton = new Button(
					{
						label : "-",
						onClick: lang.hitch(this, function(){
							topic.publish('TreeContainer', 'collapseAll', {});
						})
					}
			);
			
			this.leftContentPane.addChild(this.addDatanode);
			this.leftContentPane.addChild(this.addFrame);
			this.leftContentPane.addChild(this.collapseAllButton);
			this.leftContentPane.addChild(this.expandAllButton);
			this.leftContentPane.addChild(this.tree);
			this.addChild(this.leftContentPane);
			var formContainer = new FormContainer({region:'center', splitter:true, numPage : this.data.num_page});
			this.addChild(formContainer);
		},
		
		initTree: function(data){
			if (!data) {
				data = this.data;
			}
			this.tree = new EntityTree(data);
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'updateTree':
					this.cutTree(evtArgs);
					break;
				case 'leafRootClicked':			
				case 'leafClicked':
					this.disabledButtons(evtArgs);
					break;
			}
		},
	    
	    disabledButtons : function(item) {
	    	if (item.type == 'cadre') {
	    		this.addDatanode.set('disabled',true);
	    		this.addFrame.set('disabled',true);
	    	} else {
	    		this.addDatanode.set('disabled',false);
	    		this.addFrame.set('disabled',false);
	    	}
	    },
	});
});