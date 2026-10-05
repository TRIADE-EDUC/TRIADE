// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FilesUI.js,v 1.3 2018/11/30 13:53:07 dgoron Exp $


define(["dojo/_base/declare", 
        "apps/pmb/ExpandoPane",
        "apps/misc/files/FileStore", 
        "dojo/store/Observable", 
        "apps/misc/files/FilesModel",
        "dijit/Tree",
        "dojo/dom-construct", 
        "dojo/topic", 
        "dojo/_base/lang", 
        "dijit/form/Button", 
        "dojo/on",
        "dojox/widget/Standby",
        "dojo/request",
        "dojo/aspect"], function(declare, ExpandoPane, FileStore, Observable, FilesModel, Tree, domConstruct, topic, lang, Button, on, standby, request, aspect){
	return declare([ExpandoPane], {
		filesStore:null,
		postCreate: function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe("FileStore",lang.hitch(this,this.handleEvents)),
				topic.subscribe('FileUI', lang.hitch(this, this.handleEvents))
			);
			this.filesStore = new FileStore({url:'./ajax.php?module=admin&categ=misc&sub=files'});
			var divCollapse = domConstruct.create("div", {style:{width:"100%"}}, this.containerNode, "last");
			this.own(
				on(domConstruct.create("img",{src:pmbDojo.images.getImage('expand_all.gif')},divCollapse,"last"),'click', lang.hitch(this, this.expandAll)),
				on(domConstruct.create("img",{src:pmbDojo.images.getImage('collapse_all.gif')},divCollapse,"last"), 'click', lang.hitch(this, this.collapseAll))
			);
		},
		handleEvents : function(evtType,evtArgs){
			//console.log('FilesUI', evtType, evtArgs);
			switch(evtType){
				case "got_datas":
			  		if(evtArgs.url == "./ajax.php?module=admin&categ=misc&sub=files"){
			  			this.init();
			  		}
			  		break;
				case "needTreeRefresh" :
					if (evtArgs.itemTreeToSelected) {
						var retourQuery = this.filesStore.query({
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
		    }
		},
		init: function(){
			aspect.around(this.filesStore, "put", function(originalPut){
		        return function(obj, options){
		        	if(options && options.parent){
		            	if (options.parent.type == "folder") {
		            		obj.parent_folder = options.parent.id;
		            	}
		            	if (options.parent.type == "file") {
		            		obj.parent_file = options.parent.id;
		            	}
		            	if (options.parent.type == "substFile") {
		            		obj.parent_substFile = options.parent.id;
		            	}
		            }
		            return originalPut.call(lang.hitch(this,this.filesStore), obj, options);
		        }
		    });
			
			this.modelStore = new FilesModel({
				store: new Observable(this.filesStore),
				query: {parent_folder : "-1"}, 
				rootId: 0,
			});
			this.own(
				this.tree = new Tree({
					id : 'filesTree',
		  			model : this.modelStore,
		  			persist : true,
		  			onDblClick: function(item){
		  				if((item.type != "folder") || ((item.type == "folder") && (item.id == 0))){ //0 = noeud racine 
		  					var path = item.id;
		  					if(item.type == 'file') {
		  						topic.publish("Tree","fileTreeSelected",{path: path.substring(0, path.lastIndexOf('/')), filename: item.title, hasSubstFile: this.model.mayHaveChildren(item)});
		  					}
		  					if(item.type == 'substFile') {
		  						topic.publish("Tree","substFileTreeSelected",{path: path.substring(0, path.lastIndexOf('/')), filename: item.parent_file.replace('.xml',item.title).substring(path.lastIndexOf('/')+1)});
		  					}
		  				}
		  			},
		  			getIconClass:function(item, opened){
		  				switch(item.type){
			  				case "folder":
			  					return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitFolderOpened" : "dijitFolderClosed") : "dijitLeaf";
			  					break;
			  				case "file": 
			  					return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitIconFile" : "dijitIconFile") : "dijitLeaf";
			  					break;
			  				case "substFile": 
			  					return "dijitIconCopy";
			  					break;
		  				}
		  			},
		  			getTooltip: function(item) {
	  					if(item.type == "file" && item.comment){
	  						return item.comment;	
	  					}else{
	  						return "";	
	  					}
					}
		  		})
		  	);
		  	this.tree.placeAt(this.containerNode,"last");
		  	this.tree.startup();
		  	
		  	this.tree.set('path', [this.tree.rootNode.item]);
		  	
		},
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
		updateFileTree:function(data){
			var selectedItem = this.tree.selectedItem;
			/** TODO: check if selected watch is edited watch, and reselect it **/;
			var retourQuery = this.filesStore.query({path:data.path, filename:data.filename, type:'file'});
			//console.log('dans update watch tree', retourQuery);
			if(retourQuery.length > 0){
				this.refreshTree();
				if(selectedItem != null){
					this.tree.set('selectedItem', selectedItem);
				}
			}
		},
	});
});