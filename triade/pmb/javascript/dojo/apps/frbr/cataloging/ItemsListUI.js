// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ItemsListUI.js,v 1.15 2018/03/21 16:37:47 tsamson Exp $


define([
        "dojo/_base/declare", 
        "dojox/layout/ContentPane",
        "apps/pmb/ExpandoPane",
        "dojo/_base/lang", 
        "dojo/topic", 
        "dojo/ready", 
        "dojo/dom-construct", 
        "dojo/on",
        "dojo/query",
        'dijit/form/Button', 
        'dijit/form/RadioButton',  
        'dojox/widget/Standby', 
        "dojo/dom",
        "dojo/dom-attr",
        "dijit/form/DropDownButton", 
        "dijit/DropDownMenu", 
        "dijit/MenuItem",
        "dojo/request",
        "dijit/registry",
        "dojo/query",
        "dojo/dom-class"], function(declare,ContentPane, ExpandoPane, lang,topic,ready, domConstruct, on, query, Button, RadioButton, standby, dom, domAttr, DropDownButton, DropDownMenu, MenuItem, request, registry, query, domClass){
	
	return declare([ExpandoPane], {
		numDatanode:0,
		items:null,
		id: "FRBRItemsContainer",
		currentGraphItems: null,
		constructor : function() {
			this.own(
				topic.subscribe('DatanodesUI', lang.hitch(this, this.handleEvents)),
				topic.subscribe('SearchUI', lang.hitch(this,this.handleEvents)),
				topic.subscribe('SubTabResultsSearch', lang.hitch(this,this.handleEvents)),
				topic.subscribe('Tree', lang.hitch(this,this.handleEvents)),
				topic.subscribe('RemovalEvents', lang.hitch(this,this.handleEvents)),
				topic.subscribe('tablist', lang.hitch(this,this.handleEvents)),
				topic.subscribe('AddUI', lang.hitch(this,this.handleEvents)),
				topic.subscribe('GraphUI', lang.hitch(this,this.handleEvents))
			);
			this.dragHandlers = [];			
		},
		handleEvents : function(evtType,evtArgs){
//			console.log('ItemsListUI', evtType, evtArgs);
			switch(evtType){
		    	case 'gotItems':
			    	this.gotItems(evtArgs);
		    		break;
		    	case 'itemDeleted':
		    		break;
			    case 'datanodeDeleted':
			    	this.datanodeDeleted();
			      	break;
			    case 'eltClicked':
			    case 'itemAdded':
			    	this.addItem(evtArgs);
			    	break;
			    case 'removeItem':
			    	this.removeItem(evtArgs);
			    	break;
			    case 'itemTreeSelected':
			    	this.refreshItems(evtArgs);
			    	break;
			    case 'tablist':
			    	this.resize();
			    	break;
			    case 'graphDataUpdated':
			    	this.markUsedElements(evtArgs);
			    	break;
		    }
		},
		postCreate : function() {
			this.inherited(arguments);
			this.standby = new standby({target: this.domNode, zIndex: 10000000});
			this.getItems();
		},
		onLoad: function(){			
			if(query('input[type="button"]', this.containerNode).length){
				domConstruct.destroy(query('input[type="button"]', this.containerNode)[0]);
			}
			
			if(query('form[name^="search_form_"]', this.containerNode).length){
				var searchForm = query('form[name^="search_form_"]', this.containerNode)[0];
			}else{
				var searchForm = query('form[name="store_search"]', this.containerNode)[0];
			}
			if(searchForm){
				domAttr.set(searchForm, 'action', this.origin);
				searchForm.submit = lang.hitch(this, this.changePage, searchForm);	
			}
			
			var links = query('a[data-element-id]', this.containerNode);
			links.forEach(function(link){
				domAttr.set(link, 'onclick', '');
			});
			
			this.linkChanger();
			domConstruct.destroy(dom.byId('result_per_page'));
			this.addRemovalEvents();
			this.changeNavbar();
			collapseAll(this.containerNode);
			
			this.initDragItems();
		},
		changePage: function(link, e){
			e.preventDefault();
			request(link, {
				data: {
					type: this.type
				},
				method: 'POST',
				handleAs: 'html',
			}).then(lang.hitch(this, function(data){
				this.set('content', data);
				this.markUsedElement(this.currentGraphItems);
			}));
			return false;
		},
		setOrigin: function(url){
			this.origin = url;
		},
		linkChanger: function(){
			var noticeParents = query('div[class="notice-parent"]', this.containerNode);
			var noticeChilds = query('div[class="notice-child"]', this.containerNode);
			
			noticeParents.forEach(lang.hitch(this, function(parentDiv){
				var links = query('a[href]', parentDiv);
				links.forEach(lang.hitch(this, function(link){
					if(domAttr.get(link, 'target')){
						domAttr.remove(link, 'target');
					}
					if(domAttr.get(link, 'href') && (domAttr.get(link, 'href') != '#')){
						domAttr.set(link, 'href', '#');
					}
				}));
			}));
			
			noticeChilds.forEach(lang.hitch(this, function(childDiv){
				var links = query('a[href]', childDiv);
				links.forEach(lang.hitch(this, function(link){
					if(!domAttr.get(link, 'target') || (domAttr.get(link, 'target') && (domAttr.get(link, 'target') != '#'))){
						domAttr.set(link, 'target', '_blank');
					}
				}));
			}));
		},
		addRemovalEvents: function(){
			var itemsHeaders = query('.notice-heada', this.containerNode);
			itemsHeaders.forEach(function(itemHeader){
				if(query('a[data-element-id]', itemHeader).length) {
					var itemId = query('a[data-element-id]', itemHeader)[0].getAttribute('data-element-id');
				} else {
					var itemId = itemHeader.parentNode.getAttribute('id');
				}
				if(query('a[data-element-type]', itemHeader).length) {
					var itemType = query('a[data-element-type]', itemHeader)[0].getAttribute('data-element-type');
				} else {
					var itemType = 'records';
				}
				domConstruct.create('img', 
						{
							src: './images/suppr_all.gif',
							onclick: function(){topic.publish('RemovalEvents', 'removeItem', {id: itemId, type: itemType})},
							style: {
								margin: '0px',
							},
						}, itemHeader, 'last');	
			});
		},
		changeNavbar: function(){
			var navbarLinks = query('#navbar_container a[href]', this.containerNode);
			navbarLinks.forEach(lang.hitch(this, function(link){
				on(link, 'click', lang.hitch(this, this.changePage, link.href));
			}));
		},
		refreshItems: function(evtArgs) {
			
			this.numDatanode = evtArgs.itemTree.id;
//	    	console.log('itemtreeselected', this.numDatanode);
			this.getItems();
		},
		buildRendering : function(){
			this.inherited(arguments);
		},
		
		gotItems : function(datas) {
			
		},
		destroy : function() {
			  this.inherited(arguments);
		},
		showPatience:function(){
			this.standby.show();
		},
		hidePatience:function(){
			this.standby.hide();
		},
		datanodeDeleted: function(){		
			this.destroyDescendants();
		},
		
		addItem: function(item){
		  
		  request('./ajax.php?module=frbr&categ=cataloging&sub=item&action=add', {
				handleAs: 'json',
				method: 'POST',
				data: {
					id: (item.type == 'records' ? item.id : item.id_authority),
					type: item.type,
					num_datanode: this.numDatanode
				},
			}).then(lang.hitch(this, function(response){
				if(response){
					if(response.message){
						alert(response.message);
					}
					if(response.reload){
						this.getItems();
					}					
				}
			}));
		},
		getItems: function(){
			request('./ajax.php?module=frbr&categ=cataloging&sub=items&action=get_list', {
				handleAs: 'html',
				method: 'POST',
				data: {
					num_datanode : this.numDatanode
				},
			}).then(lang.hitch(this, function(response){
				if(response){
					this.set('content', response);
					collapseAll(this.containerNode);
					if(!this._showing){
						this.toggle();
					}					
					topic.publish('ItemsListUI', 'refreshGraph');
				}
				this.setTitle();
			}));
		},
		setPatience: function(){
			if(!this.patience){
				this.patience = new Standby({target: this.domNode.getAttribute('id')});
				document.body.appendChild(this.patience.domNode);
				this.patience.startup();
			}
			this.patience.show();
		},
		hidePatience: function(){
			if (this.patience) {
				this.patience.hide();
			}
		},
		removeItem: function(evtArgs){
			request('./ajax.php?module=frbr&categ=cataloging&sub=item&action=remove', {
				handleAs: 'json',
				method: 'POST',
				data: {
					id: evtArgs.id,
					type: evtArgs.type
				},
			}).then(lang.hitch(this, function(response){
				if(response){
					if(response.message){
						alert(response.message);
					}
					this.getItems();
				}
			}));
		},
		initDragItems : function() {
			//TODO: Ajouter une classe ou un attribut draggable sur l'élément
			var divs = query('div[id*="Parent"]', this.containerNode);
			divs.forEach((div) => {
				domAttr.set(div, 'draggable', 'true');
//				this.scenariosListHandler.push(on(node, 'dragstart', lang.hitch(this, this.dragStartHandler, scenarioDetail)));
				//this.dragHandlers.push(on(div, 'dragend', lang.hitch(this, this.dragEnd)));
//				this.dragHandlers.push(on(node, 'click', lang.hitch(this, this.scenarioClicked, div)));
				this.dragHandlers.push(on(div, 'dragstart', lang.hitch(this, this.entityDragStart, div)));
				var link = query('a', div);
				on(link, 'click', function(e){
					e.preventDefault();
				});
				on(div, 'dblclick', lang.hitch(this, this.itemSelected, div));
			});	
		},
		entityDragStart: function(div, event){
			event.dataTransfer.setData('text', JSON.stringify(
				{
					id: domAttr.get(query('*[data-element-id]', div)[0], 'data-element-id'), 
					type: domAttr.get(query('*[data-element-type]', div)[0], 'data-element-type')
				})
			);
		},
		entityDragStop: function(div){
		
		},
		itemSelected: function(div){
			var entity = query('a[data-element-id]', div)[0];
			var entityId = domAttr.get(entity, 'data-element-id');
			var entityType = domAttr.get(entity, 'data-element-type');
			var startEntity = {id: entityId, type: entityType};			
			
			topic.publish('ItemsListUI', 'itemSelected', {start_node: startEntity, items: this.getEntitiesList(startEntity)});
		},
		getEntitiesList:function(startEntity){
			var links = query('a[data-element-id]', this.containerNode);
			var entities = [];
			for(var link of links){
				let elementId = domAttr.get(link, 'data-element-id');
				let elementType = domAttr.get(link, 'data-element-type');
				if(!((elementId == startEntity.entityId) && (elementType == startEntity.entityType))){
					entities.push({id: elementId, type: elementType});	
				}
			}
			return entities;
		},
		
		setTitle : function() {
			domConstruct.create('h3', {'class' : 'section-sub-title listEntities-Title', innerHTML : pmbDojo.messages.getMessage("frbr","frbr_cataloging_list_title")}, this.containerNode, 'first');
		},
		markUsedElements: function(data){
			this.removeMark();
			this.currentGraphItems = data;
			data.data.entity_nodes.forEach(item => {
				
				var node = query('a[data-element-id="'+item.eltId+'"][data-element-type="'+item.type+'"]', this.containerNode);
				if(node.length){
					domClass.add(node[0].parentNode.parentNode, 'elt-in-graph');
				}
				/**
				 * TODO: set une class ici; l'enlever au retour
				 */
			});
		},
		removeMark(){			
			var elements = query('a[data-element-id][data-element-type]', this.containerNode);
			elements.forEach(node => {
				domClass.remove(node.parentNode.parentNode, 'elt-in-graph');
			});
		}
	});
});