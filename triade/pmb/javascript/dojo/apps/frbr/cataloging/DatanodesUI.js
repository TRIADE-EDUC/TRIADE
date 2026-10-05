// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DatanodesUI.js,v 1.3 2018/01/25 11:19:49 vtouchard Exp $


define(["dojo/_base/declare", 
        "apps/pmb/ExpandoPane", 
        "dijit/layout/ContentPane" ,
        "apps/frbr/cataloging/DatanodeStore", 
        "dojo/store/Observable", 
        "apps/frbr/cataloging/DatanodesModel", 
        "dijit/Tree", 
        "dojo/dom-construct", 
        "dojo/topic", 
        "dojo/_base/lang", 
        "dijit/form/Button", 
        "apps/frbr/cataloging/Dialog", 
        "dojo/on", 
        "dijit/tree/dndSource", 
        "dojo/aspect"], function(declare, ExpandoPane, ContentPane, DatanodeStore, Observable, DatanodesModel, Tree, domConstruct, topic, lang, Button, Dialog, on, dndSource, aspect){
	return declare([ExpandoPane], {
		datanodesStore:null,
		postCreate:function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe("DatanodeStore",lang.hitch(this,this.handleEvents)),
				topic.subscribe("EntitiesListUI",lang.hitch(this,this.handleEvents)),
				new Button({
					label: pmbDojo.messages.getMessage("frbr","frbr_cataloging_add_category"),
					onClick: lang.hitch(this, this.openCategoryForm,null)
				},domConstruct.create("button",{},this.containerNode,"last")),
				new Button({
					label: pmbDojo.messages.getMessage("frbr","frbr_cataloging_add_datanode"),
					onClick: lang.hitch(this, this.openDatanodeForm,null)
				},domConstruct.create("button",{},this.containerNode,"last")),
				new Dialog()
			);
			this.datanodesStore = new DatanodeStore({url:'./ajax.php?module=frbr&categ=cataloging&sub=datanodes'});
			var divCollapse = domConstruct.create("div", {style:{width:"100%"}}, this.containerNode, "last");
			this.own(
				on(domConstruct.create("img",{src:pmbDojo.images.getImage('expand_all.gif')},divCollapse,"last"),'click', lang.hitch(this, this.expandAll)),
				on(domConstruct.create("img",{src:pmbDojo.images.getImage('collapse_all.gif')},divCollapse,"last"), 'click', lang.hitch(this, this.collapseAll))
			);
		},
		
		handleEvents: function(evtType,evtArgs){
			//console.log("DatanodesUI", evtType, evtArgs);
			switch(evtType){
				case "got_datas":
			  		if(evtArgs.url == "./ajax.php?module=frbr&categ=cataloging&sub=datanodes"){
			  			this.init();
			  		}
			  		break;
				case "needTreeRefresh" :
					if (evtArgs.itemTreeToSelected) {
						var retourQuery = this.datanodesStore.query({
								id:evtArgs.itemTreeToSelected.id, 
								type:evtArgs.itemTreeToSelected.type
							});
						if(retourQuery.length > 0){
							this.refreshTree(retourQuery[0]);
						} else {
							this.refreshTree();
						}
					} else {
						this.refreshTree();
					}
					break;
//				case "openForm":
//					this.openForm(evtArgs.item);
//					break;
//				case "gotItems":
//					this.updateWatchTree(evtArgs);
//					break;
//				case "askedSources":
//					this.checkSourcesTTL(evtArgs.sources);
//					break;
			}
		},

		init: function(){
			aspect.around(this.datanodesStore, "put", function(originalPut){
		        return function(obj, options){
		        	if(options && options.parent){
		            	if (options.parent.type == "category") {
		            		obj.parent_category = options.parent.id;
		            	}
		            	if (options.parent.type == "datanode") {
		            		obj.parent_datanode = options.parent.id;
		            	}
		            }
		            return originalPut.call(lang.hitch(this,this.datanodesStore), obj, options);
		        }
		    });
			
			this.modelStore = new DatanodesModel({
				store: new Observable(this.datanodesStore),
				query: {parent_category : "-1"}, 
				rootId: 0,
			});
			this.own(
				this.tree = new Tree({
					id : 'datanodesTree',
		  			model : this.modelStore,
		  			persist : true,
		  			onClick: function(item){ 
		  				if((item.type != "category") || ((item.type == "category") && (item.id == 0))){ //0 = noeud racine 
	  						topic.publish("Tree","itemTreeSelected",{itemTree: item});
		  				}
		  			},
		  			getIconClass:function(item, opened){
		  				switch(item.type){
		  				case "category":
		  					return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitFolderOpened" : "dijitFolderClosed") : "dijitLeaf";
		  					break;
		  				case "datanode": 
		  					return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitIconBookmark" : "dijitIconBookmark") : "dijitLeaf";
		  					break;
		  				}
		  			},
		  			onDblClick: lang.hitch(this,this.openForm),
		  			getTooltip: function(item) {
				  					if(item.type == "datanode" && item.comment){
				  						return item.comment;	
				  					}else{
				  						return "";	
				  					}
		  						},
					dndController: dndSource
		  		})
		  	);
			this.tree.dndController.checkItemAcceptance = this.checkIfItemTreeCanDropHere;
			this.tree.dndController.checkAcceptance = this.checkIfDraggeableItemTree;
			dojo.connect(this.modelStore, 'onChildrenChange', lang.hitch(this,this.childrenChange));
		  	this.tree.placeAt(this.containerNode,"last");
		  	this.tree.startup();
		  	
		  	this.tree.set('path', [this.tree.rootNode.item]);
		  	
		},
		
		openForm: function(item){
			if(item.type == "category"){
				this.openCategoryForm(item);
			}else{
				this.openDatanodeForm(item);
			}
		},
		
		openCategoryForm: function(item){
			if(!item){
				item = {};
				if(this.tree.selectedItem && this.tree.selectedItem.type == "category"){
					item.parent_category = this.tree.selectedItem.id;
				}
			}
			topic.publish("DatanodesUI","showCategoryForm",{
				categories: this.datanodesStore.getCategories(),
				values: item
			});
		},
		
		openDatanodeForm: function(item){
			if(!item){
				item = {};
				if(this.tree.selectedItem && this.tree.selectedItem.type == "category"){
					item.parent_category = this.tree.selectedItem.id;
				}
			}
			topic.publish("DatanodesUI","showDatanodeForm",{
				categories: this.datanodesStore.getCategories(),
				values: item
			});
		},
		
		// copier/coller � peine adapt�  http://stackoverflow.com/questions/5409097/how-to-update-dojo-tree-data-dynamically
		refreshTree : function(itemTreeToSelected){
			// Tree.selectedItems, Tree.selectedNode, and Tree.selectedNodes.
			if (!itemTreeToSelected) {
				var selectedItem = this.tree.selectedItem;
			} else {
				var selectedItem = itemTreeToSelected;
			}
			this.tree.dndController.selectNone();

		    // Completely delete every node from the dijit.Tree     
			this.tree._itemNodesMap = {};
			this.tree.rootNode.state = "UNCHECKED";
			this.tree.model.root.children = null;

		    // Destroy the widget
		    this.tree.rootNode.destroyRecursive();

		    // Recreate the model, (with the model again)
		    this.tree.model.constructor(this.tree.model)

		    // Rebuild the tree
		    this.tree.postMixInProperties();
		    this.tree._load();
		    if(selectedItem != null){
				this.tree.set('selectedItem', selectedItem);
				topic.publish("Tree","itemTreeSelected",{itemTree: selectedItem});
			}
		},
		expandAll: function(){
			this.tree.expandAll();
		},
		collapseAll: function(){
			this.tree.collapseAll();
		},
		updateDatanodeTree:function(data){
			var selectedItem = this.tree.selectedItem;
			/** TODO: check if selected watch is edited watch, and reselect it **/;
			var retourQuery = this.datanodesStore.query({id:data.datanodeId, type:'datanode'});
			//console.log('dans update watch tree', retourQuery);
			if(retourQuery.length > 0){
				this.refreshTree();
				if(selectedItem != null){
					this.tree.set('selectedItem', selectedItem);
				}
			}
		},
		//détermine si l'item est déplaçable
		checkIfDraggeableItemTree:function(source,node) {
			var item = source.tree.selectedItem;
			var type = item.type;
			//on peut d�placer une veille, une source ou un classement! 
			switch(type){
				case 'datanode' :
				case 'category' :
					return true;
					break;
				default :
					return false;
					break;
			}
		},
		//détermine si c'est déposable en l'endroit
		checkIfItemTreeCanDropHere:function(target,source,position) {
			var target_item = dijit.getEnclosingWidget(target).item;
			var current_item = dijit.getEnclosingWidget(target).tree.selectedItem;
			if(target_item.root){
				//pour le root,seulement les classements et les veilles
				switch(current_item.type){
					case "datanode" :
					case "category" :
						return true;
						break;
					default : 
						return false;
						break;
				}
			} else {
				switch(target_item.type){
					case 'datanode' :
						if (current_item.type == 'source') return true;
						if (current_item.type == 'datanode' && position != 'over') return true;
						else return false;
						break;
					case 'category' :
						if (current_item.type == 'source') return false;
						return true;
						break;
					default :
						return false;
						break;
				}
			}
		},
		childrenChange:function(parent,childs) {
			if(parent.type == 'category'){
				var numParent = parent.id;
				var childrenCategories = new Array();
				var childrenDatanodes = new Array();
				for(var i=0 ; i<childs.length ; i++){
					var child = childs[i];
					if(child.type == 'category'){
						childrenCategories.push(child.id);
					} else if (child.type == "datanode"){
						childrenDatanodes.push(child.id);
					}
				}
				this.dndTreeUpdate(numParent,childrenCategories,"category");
				if(childrenDatanodes.length){
					this.dndTreeUpdate(numParent,childrenDatanodes,"datanode");
				}
			}
		},
		dndTreeUpdate:function(numParent,newChildren,type){
			topic.publish("DatanodesUI", "updateChildren", {id:numParent,children:newChildren,type:type});
		},

	});
});