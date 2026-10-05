// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntityTree.js,v 1.11 2019/03/29 16:16:25 tsamson Exp $


define(["dojo/_base/declare", 
        "apps/pmb/tree_interface/EntityTree", 
        "dojo/store/Memory", 
        "dijit/tree/ObjectStoreModel", 
        "dojo/_base/lang",
        "dijit/form/Button",
        "dojo/dom-construct",
        "dojo/topic",
        "dojox/widget/Standby"], 
        function(declare, EntityTree, Memory, ObjectStoreModel, lang, Button, domConstruct, topic, Standby){
	return declare([EntityTree], {
		id : 'frbrTree',
		
		initObjectStore: function(){
			//ModelStore le mod�le autour duquel est articul� l'arbre
			this.model = new ObjectStoreModel({
				store: this.memoryStore,
				labelType: 'html',
				query: {root:true},
				mayHaveChildren : function(item) {
					//pas d'icone + sur les cadres
					if (item.type == "cadre") {
						return false;
					}
					return true;
				}
			});
		},
		
		postCreate:function(){
			this.inherited(arguments);
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){			
				case 'formLoaded':
					this.hidePatience();
					break;
				case 'parentChange':
					this.focusParent(evtArgs.parentId);
					break;
				case 'checkChildrenToDelete':
					this.checkChildrenToDelete(evtArgs);
					break;
				case 'expandAll':
					this.expandAll();
					break;
				case 'collapseAll':
					this.collapseAll();
					break;
			}
		},

		formatData : function(data) {
			var formatData = [];
			formatData.push(data['rootNode']);
			formatData = formatData.concat(this.nodeCrawler(data, "treeDatanodes"));
			formatData = formatData.concat(this.nodeCrawler(data, "treeCadres"));
			return formatData;
		},
		
		checkChildrenToDelete : function(params) {
			var childrenItems = this.getChildrenItems(params);
			if (childrenItems) {
				if (confirm(pmbDojo.messages.getMessage('frbr', 'frbr_delete_recursive'))) {
					topic.publish('formButton', 'deleteNode', {id : params.id, type : params.type, recursive : 1});
				}
			} else {
				topic.publish('formButton', 'deleteNode', {id : params.id, type : params.type, recursive : 0});
			}		
		},
		
		getLabel : function(item){
			var label = this.model.getLabel(item);
			if(item.root){
				return '<i class="fa fa-database" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
			}else if(item.type == "cadre"){
				return '<i class="fa fa-picture-o" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
			}else{
				return '<i class="fa fa-database" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
			}
			return label;
		},

	});
});