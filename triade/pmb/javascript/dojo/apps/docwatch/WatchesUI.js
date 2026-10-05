// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: WatchesUI.js,v 1.27 2019/10/07 16:15:33 arenou Exp $


define(["dojo/_base/declare", "dijit/layout/ContentPane" ,"apps/docwatch/WatchStore", "dojo/store/Observable", "apps/docwatch/WatchesModel", "dijit/Tree", "dojo/dom-construct", "dojo/topic", "dojo/_base/lang", "dijit/form/Button", "apps/docwatch/Dialog", "dojo/on", "dijit/tree/dndSource", "dojo/aspect"], function(declare, ContentPane, WatchStore, Observable, WatchesModel, Tree, domConstruct, topic, lang, Button, Dialog, on, dndSource, aspect){
	return declare([ContentPane], {
		watchesStore:null,
		postCreate:function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe("store",lang.hitch(this,this.handleEvents)),
				topic.subscribe("watchStore",lang.hitch(this,this.handleEvents)),
				topic.subscribe("itemListUI",lang.hitch(this,this.handleEvents)),
				topic.subscribe("itemsStore",lang.hitch(this,this.handleEvents)),
				topic.subscribe("sourcesStore",lang.hitch(this,this.handleEvents)),
				topic.subscribe("source",lang.hitch(this,this.handleEvents)),
				//DEBUG, on s'abonne
				//topic.subscribe("itemsListUI",lang.hitch(this,this.handleEvents)),
				//topic.subscribe("sourcesListUI",lang.hitch(this,this.handleEvents)),
				//topic.subscribe("sourcesStore",lang.hitch(this,this.handleEvents)),
				//topic.subscribe("sourceUI",lang.hitch(this,this.handleEvents)),
				//topic.subscribe("itemUI",lang.hitch(this,this.handleEvents)),
				//topic.subscribe("watchesUI",lang.hitch(this,this.handleEvents)),
				//topic.subscribe("dialog",lang.hitch(this,this.handleEvents)),
				//topic.subscribe("watchStore",lang.hitch(this,this.handleEvents)),
				new Button({
					label: pmbDojo.messages.getMessage("dsi","docwatch_add_category"),
					onClick: lang.hitch(this, this.openCategoryForm,null)
				},domConstruct.create("button",{},this.domNode,"last")),
				new Button({
					label: pmbDojo.messages.getMessage("dsi","docwatch_add_watch"),
					onClick: lang.hitch(this, this.openWatchForm,null)
				},domConstruct.create("button",{},this.domNode,"last")),
				new Dialog()
			);
			this.watchesStore = new WatchStore({url:'./ajax.php?module=dsi&categ=docwatch&sub=watches'});
			var divCollapse = domConstruct.create("div", {style:{width:"100%"}}, this.domNode, "last");
			this.own(
				on(domConstruct.create("img",{src:pmbDojo.images.getImage('expand_all.gif')},divCollapse,"last"),'click', lang.hitch(this, this.expandAll)),
				on(domConstruct.create("img",{src:pmbDojo.images.getImage('collapse_all.gif')},divCollapse,"last"), 'click', lang.hitch(this, this.collapseAll))
			);
			//setInterval(lang.hitch(this, this.checkWatchesTTL), 120000);
		},
		
		handleEvents: function(evtType,evtArgs){
			//console.log("watchesUI", evtType, evtArgs);
			switch(evtType){
				case "got_datas":
			  		if(evtArgs.url == "./ajax.php?module=dsi&categ=docwatch&sub=watches"){
			  			this.init();
			  		}
			  		break;
				case "needTreeRefresh" :
					if (evtArgs.itemTreeToSelected) {
						var retourQuery = this.watchesStore.query({
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
				case "openForm":
					this.openForm(evtArgs.item);
					break;
				case "gotItems":
					this.updateWatchTree(evtArgs);
					break;
				case "askedSources":
					this.checkSourcesTTL(evtArgs.sources);
					break;
				case "openDuplicateSourceForm":
					this.openDuplicateSourceForm(evtArgs.item);
					break;
			}
		},

		init: function(){
			aspect.around(this.watchesStore, "put", function(originalPut){
		        return function(obj, options){
		        	if(options && options.parent){
		            	if (options.parent.type == "category") {
		            		obj.parent_category = options.parent.id;
		            	}
		            	if (options.parent.type == "watch") {
		            		obj.parent_watch = options.parent.id;
		            	}
		            }
		            return originalPut.call(lang.hitch(this,this.watchesStore), obj, options);
		        }
		    });
			
			this.modelStore = new WatchesModel({
				store: new Observable(this.watchesStore),
				query: {parent_category : "-1"}, 
			});
			this.own(
				this.tree = new Tree({
					id : 'watchesTree',
		  			model : this.modelStore,
		  			persist : true,
		  			onClick: function(item){
		  				if(item.type != "category"){
	  						topic.publish("tree","itemTreeSelected",{itemTree: item});
		  				}
		  			},
		  			getIconClass:function(item, opened){
		  				switch(item.type){
		  				case "category":
		  					return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitFolderOpened" : "dijitFolderClosed") : "dijitLeaf";
		  					break;
		  				case "watch": 
		  					return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitIconBookmark" : "dijitIconBookmark") : "dijitLeaf";
		  					break;
		  				case "source":
		  					return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitIconDatabase" : "dijitIconDatabase") : "dijitLeaf";
		  					break;
		  				}
		  			},
		  			getIconStyle:function(item, opened){
		  				switch(item.type){
		  				case "watch": 
		  					if(item.logo_url){
		  						return {backgroundImage: "url("+item.logo_url+")", backgroundSize:"16px 16px"};	
		  					}
		  					break;
		  				}
		  			},
		  			onDblClick: lang.hitch(this,this.openForm),
		  			getTooltip: function(item) {
				  					if(item.type == "watch" && item.desc){
				  						return item.desc;	
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
		  	this.tree.placeAt(this.domNode,"last");
		  	this.tree.startup();
		},
		
		openForm: function(item){
			if(item.type != "source"){
				if(item.type == "category"){
					this.openCategoryForm(item);
				}else{
					this.openWatchForm(item);
				}	
			}
		},
		
		openCategoryForm: function(item){
			if(!item){
				item = {};
				if(this.tree.selectedItem && this.tree.selectedItem.type == "category"){
					item.parent_category = this.tree.selectedItem.id;
				}
			}
			topic.publish("watchesUI","showCategoryForm",{
				categories: this.watchesStore.getCategories(),
				values: item
			});
		},
		
		openWatchForm: function(item){
			if(!item){
				item = {};
				if(this.tree.selectedItem && this.tree.selectedItem.type == "category"){
					item.parent_category = this.tree.selectedItem.id;
				}
			}
			topic.publish("watchesUI","showWatchForm",{
				categories: this.watchesStore.getCategories(),
				values: item
			});
		},
		
		openDuplicateSourceForm: function(item){
			if(!item){
				item = {};
			}
			if(this.tree.selectedItem && this.tree.selectedItem.type == "watch"){
				item.parent_watch = this.tree.selectedItem.id;
			}
			topic.publish("watchesUI","showDuplicateSourceForm",{
				watches: this.watchesStore.getWatches(),
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
				topic.publish("tree","itemTreeSelected",{itemTree: selectedItem});
			}
		},
		expandAll: function(){
			this.tree.expandAll();
		},
		collapseAll: function(){
			this.tree.collapseAll();
		},
		updateWatchTree:function(data){
			var selectedItem = this.tree.selectedItem;
			/** TODO: check if selected watch is edited watch, and reselect it **/;
			var retourQuery = this.watchesStore.query({id:data.watchId, type:'watch'});
			//console.log('dans update watch tree', retourQuery);
			if(retourQuery.length > 0){
				retourQuery[0].formated_last_date = data.formated_last_date; 
				this.refreshTree();
				if(selectedItem != null){
					this.tree.set('selectedItem', selectedItem);
				}
			}
		},
		
		checkWatchesTTL:function(){
			var tabContainer = this.getParent().getChildren()[1].getChildren()[0];
			if(typeof tabContainer.selectedChildWidget.itemsStore != "undefined"){ //Permet de ne pas d�naturer le code courant
				var watches = this.tree.model.store.query({type:'watch'});
				for(var i=0 ; i<watches.length ; i++){
					var sources = this.tree.model.store.query({type:'source', parent_watch:watches[i].id});
					if(sources.length > 0){//Des sources sont pr�sentes pour cette veille
						topic.publish("watchesUI", "sourcesAsked", {watchId:watches[i].id});	
					}
				}
			}
		},
		checkSourcesTTL:function(sources){
			var needUpdate = false;
			for(var i=0 ; i<sources.length ; i++){
				var currentDate = Math.floor(new Date().getTime()/ 1000);
				var sourceDate =  Math.floor(new Date(sources[i].formated_last_date).getTime()/ 1000);
				if((sourceDate + (parseInt(sources[i].ttl)*3600)) < currentDate){
					needUpdate = true;
				} 
			}
			if(needUpdate){
				topic.publish('watchesUI', 'watchOutdated', {watchId: sources[0].num_watch});
			}
		},
		//d�termine si l'item est d�pla�able
		checkIfDraggeableItemTree:function(source,node) {
			var item = source.tree.selectedItem;
			var type = item.type;
			//on peut d�placer une veille, une source ou un classement! 
			switch(type){
				case 'watch' :
				case 'category' :
				case 'source' :
					return true;
					break;
				default :
					return false;
					break;
			}
		},
		//d�termine si c'est d�posable en l'endroit
		checkIfItemTreeCanDropHere:function(target,source,position) {
			var target_item = dijit.getEnclosingWidget(target).item;
			var current_item = dijit.getEnclosingWidget(target).tree.selectedItem;
			if(target_item.root){
				//pour le root,seulement les classements et les veilles
				switch(current_item.type){
					case "watch" :
					case "category" :
						return true;
						break;
					default : 
						return false;
						break;
				}
			} else {
				switch(target_item.type){
					case 'watch' :
						if (current_item.type == 'source') return true;
						if (current_item.type == 'watch' && position != 'over') return true;
						else return false;
						break;
					case 'source' :
						if (current_item.type == 'source' && position != 'over') return true;
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
				var childrenWatches = new Array();
				for(var i=0 ; i<childs.length ; i++){
					var child = childs[i];
					if(child.type == 'category'){
						childrenCategories.push(child.id);
					} else if (child.type == "watch"){
						childrenWatches.push(child.id);
					}
				}
				this.dndTreeUpdate(numParent,childrenCategories,"category");
				if(childrenWatches.length){
					this.dndTreeUpdate(numParent,childrenWatches,"watch");
				}
			}
			if(parent.type == 'watch'){
				var numParent = parent.id;
				var childrenSources = new Array();
				for(var i=0 ; i<childs.length ; i++){
					var child = childs[i];
					if(child.type == 'source'){
						childrenSources.push(child.id);
					}
				}
				if(childrenSources.length){
					this.dndTreeUpdate(numParent,childrenSources,"source");
				}
			}
		},
		dndTreeUpdate:function(numParent,newChildren,type){
			topic.publish("watchesUI", "updateChildren", {id:numParent,children:newChildren,type:type});
		},

	});
});