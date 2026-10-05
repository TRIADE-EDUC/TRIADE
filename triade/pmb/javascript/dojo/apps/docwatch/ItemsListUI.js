// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ItemsListUI.js,v 1.50 2021/08/10 09:53:05 dgoron Exp $


define(["dojo/_base/declare", "dijit/layout/ContentPane", "dojo/_base/lang", "dojo/topic", "dojox/grid/DataGrid", "dojo/data/ObjectStore", "dojo/store/Memory", "dojo/ready", "apps/docwatch/ItemsStore", "dojo/date/locale", "dojo/dom-construct", "dojo/on", 'dijit/form/Button', 'dijit/form/RadioButton',  'dojox/widget/Standby', "dojo/dom"], function(declare,ContentPane,lang,topic,DataGrid,ObjectStore,Memory,ready,ItemsStore,locale, domConstruct, on, Button, RadioButton, standby, dom){
	
	return declare([ContentPane], {
		idWatch:0,
		currentIdWatch:0,
		itemsLayout:null,
		itemsGrid:null,
		itemsStore:null,
		datagridStore:null,
		header:null,
		storeQuery:null,
		myItemsSearch:null,
		defltFilterDeleted:null,
		constructor : function() {
			this.storeQuery = {num_watch:this.idWatch};
			this.itemsStore = new ItemsStore({
				url:'./ajax.php?module=dsi&categ=docwatch&sub=items',
				directInit: false,
			});
			this.itemsLayout = [[
				{'name': pmbDojo.messages.getMessage('dsi', 'dsi_js_item_interesting'), 'field': 'interesting', 'width': '10%', formatter:this.formatInteresting},
				{'name': pmbDojo.messages.getMessage('dsi', 'dsi_js_item_title'), 'field': 'title', 'width': '60%'},
				{'name': pmbDojo.messages.getMessage('dsi', 'dsi_js_item_publication_date'), 'field': 'formated_publication_date', 'width': '15%', formatter:this.dateFormatter},
				{'name': pmbDojo.messages.getMessage('dsi', 'dsi_js_item_source_name'), 'field': 'datasource_title', 'width': '15%'}
			]];
		},

		postCreate : function() {
			this.own(
				topic.subscribe("tree",lang.hitch(this,this.handleEvents)),
				topic.subscribe("itemsStore",lang.hitch(this,this.handleEvents)),
				topic.subscribe("watchStore", lang.hitch(this, this.handleEvents)),
				topic.subscribe('watchesUI', lang.hitch(this, this.handleEvents))
			);
			this.standby = new standby({target: this.domNode, zIndex: 10000000});
			document.body.appendChild(this.standby.domNode);
			this.standby.startup();
			this.header = domConstruct.create('div', {style:{display:'none',marginBottom: '4px', border:"1px solid #b5bcc7", width:"100%"}}, this.domNode);
		},

		handleEvents : function(evtType,evtArgs){
			//console.log('itemListUI', evtType, evtArgs);
			switch(evtType){
		    	case "itemTreeSelected" :
		    		if (evtArgs.itemTree.type == "watch") {
		    			this.idWatch = evtArgs.itemTree.id;
		    			this.storeQuery = {num_watch:this.idWatch};
	    				this.refreshContent();
	    				if (evtArgs.itemTree.nb_sources > 0) {
	    					if(this.getParent().selectedChildWidget !== this){
	    						this.getParent().selectChild(this);
	    					}
		    			}
	    				this.updateHeader(evtArgs.itemTree);
		    		}else if(evtArgs.itemTree.type == "source"){
		    			this.idWatch = evtArgs.itemTree.parent_watch;
		    			this.storeQuery = {num_watch:this.idWatch};
	    				this.refreshContent();
		    		}
		    		break;
		    	case "gotItems":
			    	this.gotItems(evtArgs);
		    		break;
		    	case "itemModified":
		    		this.refreshItem(evtArgs);
		    		break;
		    	case "itemDeleted":
    				this.refreshItem(null, true);
    				this.selectUpper();
		    		break;
		    	case "needWatchUpdate":
		    		this.itemsStore.updateWatch(evtArgs.watchId,evtArgs.needItems);
		    		break;
			    case "watchOutdated":
			    	this.watchOutdated(evtArgs.watchId);
			      	break;
			    case "watchDeleted":
			    	this.watchDeleted();
			      	break;
			    case "itemsMarkAsDeletedPurged":
			    	this.itemsMarkAsDeletedPurged(evtArgs.watchId);
			      	break;
		    }
		},

		buildRendering : function(){
			this.inherited(arguments);
		},

		refreshContent : function() {
			if (this.idWatch != this.currentIdWatch) {
				this.currentIdWatch = this.idWatch;	
				var query = this.itemsStore.query({watch_id:this.idWatch});
				if(!query.length){
					this.showPatience();
					this.itemsStore.needItems(this.currentIdWatch);
				}else{
					if(query[0].outdated){
						this.showPatience();
						this.itemsStore.updateWatch(this.idWatch);
					}else{
						//Les items de la veille sont d�j� pr�sent, on va juste les afficher (la veille n'a pas �t� mise � jour)
						this.gotItems();	
					}	
				}
			}
		},
		formatInteresting: function(value){
			if(parseInt(value) == 0){
				return "<img src='"+pmbDojo.images.getImage('star_unlight.png')+"'></img>";
			}
            return "<img src='"+pmbDojo.images.getImage('star.png')+"'></img>";
        },
        dateFormatter:function(date){
			if(date=="")return "";
			return locale.format(
					new Date(date)
			);
        },
		gotItems : function(datas) {
			if(!this.datagridStore){
				this.datagridStore = new ObjectStore({
					objectStore: this.itemsStore
				});	
				this.own(this.datagridStore);
			}
			if(!this.itemsGrid){
				this.itemsGrid = new DataGrid({
					id: 'itemsGrid'+this.idWatch,
					selectionMode:'single',
					store: this.datagridStore,
					query:{num_watch:this.idWatch},
					escapeHTMLInData: false,
					structure: this.itemsLayout,
					onStyleRow: lang.hitch(this, this.styleRow),
					onRowClick: lang.hitch(this, this.rowClick),
					onHeaderCellClick: lang.hitch(this, this.headerClick)
				});
				this.itemsGrid.placeAt(this.domNode);
				this.itemsGrid.startup();
				this.own(this.itemsGrid);
				this.itemsGrid.setSortIndex(2);
				this.itemsGrid.setSortIndex(2);
			}else{
				this.itemsGrid.setQuery(this.storeQuery);
				this.itemsGrid.selection.deselectAll();
				topic.publish("itemsListUI","noMoreItems");
			}
			this.hidePatience();
			if(datas != undefined) {
				this.updateDate(datas);
				this.defltFilterDeleted = parseInt(datas.deflt_docwatch_watch_filter_deleted);
			}
			if(dijit.byId('filter_deleted_hide')) {
				if(!this.defltFilterDeleted || dijit.byId('filter_deleted_hide').checked) {
					dijit.byId('filter_deleted_hide').setChecked(true);
					this.filterDeletedHideClicked();
				}
			}
		},

		refreshItem : function(datas, deletion) {
			deletion = typeof deletion !== 'undefined' ? deletion : false;
			if(!deletion && this.itemsGrid.sortInfo != 0){
				var valueScroll = this.itemsGrid.scrollTop;
				//Recuperation de la ligne de l'item avant refresh
				var itmIdx = this.itemsGrid.selection.selectedIndex;
				//Recuperation de l'item sur le datagrid � partir de son index
				var item = this.itemsGrid.getItem(itmIdx);
				//refresh du datagrid
				this.itemsGrid.setQuery(this.storeQuery);
				
				//Recuperation de l'index de l'item apr�s refresh, et de l'index actuellement s�l�ctionn�
				itmIdx = this.itemsGrid.getItemIndex(item);
				var currentIdx = this.itemsGrid.selection.selectedIndex;
				
				if(currentIdx != -1){//Une ligne est s�l�ctionn�e
					if(currentIdx != itmIdx && itmIdx != -1){//Mais ce n'est pas la bonne
						this.itemsGrid.selection.deselectAll();
						this.itemsGrid.selection.addToSelection(itmIdx);
				
						//on scroll �galement jusqu'a cette ligne
						this.itemsGrid.scrollToRow(itmIdx);
					}else{
						this.itemsGrid.scrollTo(valueScroll);			
					}
				}
				if(datas && datas.itemUIRefresh){
					var item = this.itemsGrid.store.objectStore.query({id:datas.itemId})[0];
    				topic.publish("itemsListUI","itemSelected",{item: item});
				}
			}else if(datas!=null){//Comportement normal sans sort
				/** Permet de revenir a la m�me vue qu'avant la mise � jour **/
				var valueScroll = this.itemsGrid.scrollTop;
				this.itemsGrid.setQuery(this.storeQuery);
				this.itemsGrid.scrollTo(valueScroll);	
				if(datas.itemUIRefresh){
	    		//	console.log('evt args', evtArgs, 'this', this.itemsGrid, 'selected index', this.itemsGrid.selection.selectedIndex)
					var item = this.itemsGrid.store.objectStore.query({id:datas.itemId})[0];
    				topic.publish("itemsListUI","itemSelected",{item: item});	
				}
			}else{//Cas de la suppression
				var valueScroll = this.itemsGrid.scrollTop;
				this.itemsGrid.setQuery(this.storeQuery);
				this.itemsGrid.scrollTo(valueScroll);	
				var item = this.itemsGrid.getItem(this.itemsGrid.selection.selectedIndex);
				if(item != null){
					topic.publish("itemsListUI","itemSelected",{item: item});	
				}else{
					topic.publish("itemsListUI","noMoreItems");	
				}
					
			}
		},

		styleRow : function(row){
			  var i = row;
			  i.customClasses += (i.odd?" dojoxGridRowOdd":"") + (i.selected?" dojoxGridRowSelected":"") + (i.over?" dojoxGridRowOver":"");
			  var item = this.itemsGrid.getItem(row.index);
		      if(item){
		         var status = this.itemsGrid.store.getValue(item, "status", null);
		         if(status == 0){
		             row.customStyles += "font-weight:bold;";
		         }else if(status == 2){
		        	 row.customStyles += "text-decoration:line-through;";
		         }
		      }
		      this.itemsGrid.focus.styleRow(row);
		      this.itemsGrid.edit.styleRow(row);
		  },

		rowClick : function(evt){
			if(this.itemsGrid.selection.getSelected()[0] != this.itemsGrid.getItem(evt.rowIndex)){
				this.itemsGrid.selection.clickSelectEvent(evt);
				this.itemsGrid.edit.rowClick(evt);
				var item = this.itemsGrid.getItem(evt.rowIndex);
				if(item != null){
					topic.publish("itemsListUI","itemSelected",{item: item});	
				}
			}
			if(evt.cellIndex == 0){
				if(parseInt(this.itemsGrid.getItem(evt.rowIndex).interesting) == 1){
					topic.publish('itemsListUI',"itemMarkAsUninteresting",{itemId:this.itemsGrid.getItem(evt.rowIndex).id});
				}else{
					topic.publish('itemsListUI',"itemMarkAsInteresting",{itemId:this.itemsGrid.getItem(evt.rowIndex).id});
				}
			}
			
		},

		destroy : function() {
			  this.inherited(arguments);
		},
		onShow:function(){
			this.inherited(arguments);
			topic.publish("itemsListUI", "showItemsList", {});
		},

		selectUpper:function(){
			if(this.itemsGrid.selection.getSelected()[0] != undefined){
				var item = this.itemsGrid.getItem(this.itemsGrid.selection.selectedIndex);
				topic.publish("itemsListUI","itemSelected",{item: item});
			}else{
				topic.publish("itemsListUI","noMoreItems");
			}
		},
		headerClick: function(e){
			if(this.itemsGrid.selection.getSelected().length > 0 && this.itemsGrid.selection.getSelected()[0] != null){
				var itm = this.itemsGrid.selection.getSelected()[0];
				this.itemsGrid.setSortIndex(e.cell.index);
				this.itemsGrid.onHeaderClick(e);
				if(this.itemsGrid.getItem(this.itemsGrid.selection.selectedIndex) != itm){
					//L'item n'est plus selectionn�
					this.itemsGrid.selection.deselectAll();
					this.itemsGrid.selection.setSelected(this.itemsGrid.getItemIndex(itm), true);
					this.itemsGrid.scrollToRow(this.itemsGrid.getItemIndex(itm));
				}
			}else{
				this.itemsGrid.setSortIndex(e.cell.index);
				this.itemsGrid.onHeaderClick(e);	
			}
			
		},
		showPatience:function(){
			this.standby.show();
		},

		hidePatience:function(){
			this.standby.hide();
		},
		updateHeader:function(watch){
			if(this.header.style.display == "none"){
				this.header.style.display = 'block';
			}
			domConstruct.empty(this.header);
			var mainRow = domConstruct.create('div', {class:'row', style:{ padding:'5px'}}, this.header);
			if(watch.logo_url){
				domConstruct.create('img', {src:watch.logo_url, style:{width:'20px', height:'20px',marginLeft:'5px',marginRight:'5px'}}, mainRow);
			}
			domConstruct.create('span', {innerHTML:watch.title, style:{marginLeft:'5px',marginRight:'5px'}}, mainRow);
			domConstruct.create('span', {id: 'watch_last_date', innerHTML:'('+locale.format(new Date(watch.formated_last_date))+')', style:{marginLeft:'5px',marginRight:'5px'}}, mainRow);
			domConstruct.create('a', {target: '_blank', href:watch.opac_link, innerHTML:'<img src=\''+pmbDojo.images.getImage('rss.png')+'\'/>', style:{marginLeft:'5px',marginRight:'5px'}}, mainRow);
			var myButton = new Button({
			        label: pmbDojo.messages.getMessage('dsi', 'dsi_js_docwatch_edit_watch'),
			        onClick: lang.hitch(this, this.editClicked, watch)
			    }).placeAt(mainRow).startup();
			var myButton = new Button({
			        label: pmbDojo.messages.getMessage('dsi', 'docwatch_watch_purge'),
			       	title: pmbDojo.messages.getMessage('dsi', 'docwatch_watch_purge_items_mark_as_deleted'),
			        onClick: lang.hitch(this, this.purgeItemsMarkAsDeletedClicked, watch)
			    }).placeAt(mainRow).startup();
			domConstruct.create('span', {innerHTML:pmbDojo.messages.getMessage("dsi","docwatch_watch_filter_deleted"), style:{marginLeft:'5px',marginRight:'5px'}}, mainRow);
			
			if(dijit.byId('filter_deleted_show')) {
				dijit.byId('filter_deleted_show').placeAt(mainRow).startup();
			} else {
				var myButton_1 = new RadioButton({
					id: 'filter_deleted_show',
					name: 'filter_deleted',
			        checked: true,
			        onClick: lang.hitch(this, this.filterDeletedShowClicked, watch)
			    }).placeAt(mainRow).startup();
			}
			domConstruct.create('span', {innerHTML:pmbDojo.messages.getMessage("dsi","docwatch_yes"), style:{marginLeft:'5px',marginRight:'5px'}}, mainRow);
			if(dijit.byId('filter_deleted_hide')) {
				dijit.byId('filter_deleted_hide').placeAt(mainRow).startup();
			} else {
				var myButton_2 = new RadioButton({
					id: 'filter_deleted_hide',
					name: 'filter_deleted',
			        onClick: lang.hitch(this, this.filterDeletedHideClicked, watch)
			    }).placeAt(mainRow).startup();
			}
			domConstruct.create('span', {innerHTML:pmbDojo.messages.getMessage("dsi","docwatch_no"), style:{marginLeft:'5px',marginRight:'5px'}}, mainRow);
			this.myItemsSearch = domConstruct.create('input', {
				type:'text', 
				class:'saisie-30em', 
				style:{ padding:'5px'},
				'placeholder' : pmbDojo.messages.getMessage("dsi","docwatch_watch_filter_items")}, mainRow);
			this.own(
				on(this.myItemsSearch,"keyup", lang.hitch(this, this.filterItemsSearch, watch))
			);
		},
		editClicked: function(watch){
			topic.publish('itemListUI', 'openForm', {item:watch});
		},
		filterDeletedShowClicked: function(watch){
			this.storeQuery = {num_watch:this.idWatch};
			this.refreshItem(null, false);
		},
		filterDeletedHideClicked: function(watch){
			var num_watch = this.idWatch;
			this.storeQuery = function(object){
				if (object.num_watch == num_watch) {
					if (object.status == 0 || object.status == 1) {
						return object;
					}
				}
			};
			this.refreshItem(null, false);
		},
		filterItemsSearch: function(watch) {
			var num_watch = this.idWatch;
			var search_value = this.myItemsSearch.value.toLowerCase();
			this.storeQuery = function(object){
				if (object.num_watch == num_watch) {
					var title = object.title.toLowerCase();
					var source_label = object.source.title.toLowerCase();
					if (title.indexOf(search_value) != -1 || source_label.indexOf(search_value) != -1) {
						return object;
					}
				}
			};
			this.refreshItem(null, false);
		},
		purgeItemsMarkAsDeletedClicked: function(watch) {
			if(confirm(pmbDojo.messages.getMessage("dsi","docwatch_watch_purge_items_mark_as_deleted_confirm"))){
				topic.publish("itemsListUI","purgeItemsMarkAsDeleted",{
				watchId: this.idWatch
				});
			}
		},
		updateDate:function(datas){
			if(dom.byId('watch_last_date')){
				dom.byId('watch_last_date').innerHTML = '('+locale.format(new Date(datas.formated_last_date))+')';	
			}
		},
		watchOutdated: function(watchId){
			if(watchId == this.idWatch){
				this.itemsStore.updateWatch(watchId);
			}else{
				this.itemsStore.query({watch_id:watchId})[0].outdated = true;
				//TODO: ajouter le false sur le litem qui repr�sente la veille dans l'itemStore
			}
		},
		watchDeleted: function(){		
			this.destroyDescendants();
		},
		itemsMarkAsDeletedPurged: function(watchId){
			topic.publish("itemsListUI","updateWatch",{
				watchId: watchId
			});
		},
	});
});