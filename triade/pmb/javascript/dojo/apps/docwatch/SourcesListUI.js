// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SourcesListUI.js,v 1.34 2015/03/19 09:46:59 dgoron Exp $


define(["dojo/_base/declare", "dijit/layout/ContentPane","dojo/_base/lang", "dojo/topic", "dojox/grid/DataGrid", "dojo/data/ObjectStore", "dojo/store/Memory", "dojo/ready", "apps/docwatch/SourcesStore", "dijit/form/Button", "dojo/dom-construct", "dijit/DropDownMenu", "dijit/MenuItem", "dijit/form/DropDownButton", "dojo/Deferred", "dojo/date/locale"], function(declare,ContentPane,lang,topic,DataGrid,ObjectStore,Memory,ready,SourcesStore, Button, domConstruct, DropDownMenu, MenuItem, DropDownButton, Deferred, locale){
	return declare([ContentPane], {
		idWatch:0,
		currentIdWatch:0,
		sourcesLayout:null,
		sourcesGrid:null,
		dataGridStore : null,
		sourcesStore: null,
		deferred: null,
		displayed : false,
		sourceAsked: 0,
		  
		constructor : function() {
			this.sourcesStore = new SourcesStore({
				url:'./ajax.php?module=dsi&categ=docwatch&sub=sources',
			  	directInit: false,
			});
			this.sourcesLayout = [[
				{'name': pmbDojo.messages.getMessage('dsi', 'dsi_js_source_title'), 'field': 'title', 'width': '10%'},
			    {'name': pmbDojo.messages.getMessage('dsi', 'dsi_js_source_last_sync'), 'field': 'formated_last_date', 'width': '40%', formatter:this.dateFormatter}
			]];
		},
		  
		postCreate : function() {
			this.own(
				topic.subscribe("tree",lang.hitch(this,this.handleEvents)),
				topic.subscribe("sourcesStore",lang.hitch(this,this.handleEvents)),
				topic.subscribe("watchStore",lang.hitch(this,this.handleEvents))
			);  
		},
		handleEvents : function(evtType,evtArgs){
			//console.log('sourceListUI', evtType, evtArgs);
			switch(evtType){
		    	case "itemTreeSelected" :
		    		if (evtArgs.itemTree.type == "watch") {
		    			this.idWatch = evtArgs.itemTree.id;
		    			this.sourceAsked = 0;
	    				this.refreshContent();
	    				topic.publish("sourcesListUI", "displayRaz", {});
	    				if(this.sourcesGrid){
	    					if(this.sourcesGrid.selection.getSelected().length > 0){
	    						this.sourcesGrid.selection.deselectAll();
	    					}
	    				}
	    				if (evtArgs.itemTree.nb_sources == 0) {
	    					if(this.getParent().selectedChildWidget !== this){
			    				this.getParent().selectChild(this);
			    			}
	    				}
		    		} else if(evtArgs.itemTree.type == "source"){
	    				this.idWatch = evtArgs.itemTree.parent_watch;
	    				this.sourceAsked = evtArgs.itemTree.id;
	    				if(this.getParent().selectedChildWidget !== this){
		    				this.getParent().selectChild(this);
		    			}
		    			this.refreshContent();
		    		}
		    		break;
		    	case "needRefresh":
		    	case "sourceSaved":
		    	case "sourceDeleted":
		    		this.sourceAsked = 0;
		    		this.gotSources();
		    		topic.publish("sourcesListUI", "displayRaz", {});
		    		break;
		    	case "gotSources":
			    	this.gotSources();
		    		break;
		    	case "watchDeleted":
			    	this.watchDeleted();
			      	break;
				}
		},
		  
		buildRendering : function(){
			this.inherited(arguments);
		},
		  
		refreshContent : function() {
			if (this.idWatch != this.currentIdWatch || this.sourceAsked) {
				this.currentIdWatch = this.idWatch;	
				if(!this.sourcesStore.query({watch_id:this.idWatch}).length){
					this.sourcesStore.needSources(this.currentIdWatch);
				}else{
					this.gotSources();
				}
			}
		},
		  
		gotSources : function() {
			if(!this.dataGridStore){
				var menu = new DropDownMenu({ style: "display: none;"});
				for(var i=0 ; i<availableDatasources.data.length ; i++){
					menu.addChild(new MenuItem({
						label : availableDatasources.data[i].label,
						onClick : lang.hitch(this,this.openSourceForm,availableDatasources.data[i].class)
					}));
				}
				menu.startup();
				var button = new DropDownButton({
					label: pmbDojo.messages.getMessage("dsi","docwatch_add_source"),
				    dropDown: menu,
				    style: { width: "auto"}
				},domConstruct.create("button",{},this.domNode,"last"));
				button.startup();
								
				this.dataGridStore = new ObjectStore({
					objectStore: this.sourcesStore
				});
			}
			if(!this.sourcesGrid){
				this.sourcesGrid = new DataGrid({
					id: 'sourcesGrid'+this.idWatch,
					store: this.dataGridStore,
					query:{num_watch:this.idWatch},
					structure: this.sourcesLayout,
					selectionMode:'single',
					onStyleRow: lang.hitch(this, this.styleRow),
					onRowClick: lang.hitch(this, this.rowClick),
				});
				this.sourcesGrid.placeAt(this.domNode);
				this.sourcesGrid.startup();
				this.own(this.sourcesGrid);
			}
			
			this.sourcesGrid.setQuery({num_watch:this.idWatch});
			this.sourcesGrid.selection.deselectAll();
			if(this.sourceAsked){
				this.selectSource();
			}
			
		},
		  
		styleRow : function(row){
			var i = row;
			i.customClasses += (i.odd?" dojoxGridRowOdd":"") + (i.selected?" dojoxGridRowSelected":"") + (i.over?" dojoxGridRowOver":"");
			this.sourcesGrid.focus.styleRow(row);
			this.sourcesGrid.edit.styleRow(row);
		},
		  
		rowClick : function(evt){
			this.sourcesGrid.selection.clickSelectEvent(evt);
			this.sourcesGrid.edit.rowClick(evt);
			var item = this.sourcesGrid.getItem(evt.rowIndex);
			topic.publish("sourcesListUI","sourceSelected",{
				id: this.sourcesGrid.store.getValue(item, "id", null),
				watchId: this.sourcesGrid.store.getValue(item, "num_watch", null),
				className : this.sourcesGrid.store.getValue(item, "class_name", null)
			});
		},
		
		onShow:function(){
			this.inherited(arguments);
			this.displayed = true;
			topic.publish("sourcesListUI", "showSourcesList", {});
		},
		
		onHide:function(){
			this.inherited(arguments);
			this.displayed = false;
		},
		
		openSourceForm: function(sourceClassName){
			topic.publish("sourcesListUI","addSource",{ 
				className: sourceClassName,
				sourceId : 0,
				watchId : this.idWatch
			});
		},
		
		selectSource : function(){
			this.sourcesGrid.selection.deselectAll();
			var search = this.dataGridStore.objectStore.query({id:this.sourceAsked})
			var item = search[0];
			var idx = this.sourcesGrid.getItemIndex(item);
			this.sourcesGrid.selection.addToSelection(idx);
			topic.publish("sourcesListUI","sourceSelected",{
				id: this.sourcesGrid.store.getValue(item, "id", null),
				watchId: this.sourcesGrid.store.getValue(item, "num_watch", null),
				className : this.sourcesGrid.store.getValue(item, "class_name", null)
			});
		},
        dateFormatter:function(date){
        	var date = new Date(date);
        	if(date.toString() == "Invalid Date"){
        		return '--/--/---- --:--';
        	}else{
        		return locale.format(date);	
        	}
        },
        watchDeleted: function(){		
			this.destroyDescendants();
		},
	});
});