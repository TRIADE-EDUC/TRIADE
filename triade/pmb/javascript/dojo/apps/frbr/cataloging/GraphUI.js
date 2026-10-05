// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: GraphUI.js,v 1.17 2018/03/21 09:46:49 tsamson Exp $


define([
        "dojo/_base/declare",
        "d3/d3",
        "apps/pmb/graph/PMBGraph",
        "apps/frbr/cataloging/Node",
        "apps/frbr/cataloging/LinkNode",
        "dojox/layout/ContentPane",
        "dojo/dom-construct",
        "dojo/dom",
        "dojo/on",
        "dojo/topic",
        "dojo/_base/lang",
        "dijit/registry",
        "dijit/form/Button",
        "dijit/form/CheckBox",
        "dijit/form/ValidationTextBox",
        'dojo/request',
        'dojo/query',
        'dojox/widget/Standby', 
        'dijit/registry',
        ], function(declare, d3, PMBGraph, Node, LinkNode, ContentPane, domConstruct, dom, on, topic, lang, registry, Button, CheckBox, ValidationTextBox, request, query, Standby, registry){
	//http://bl.ocks.org/rkirsling/5001347
	return declare(PMBGraph, {
		numDatanode:0,
        entityLinks: null,
        entityNodes: null, //-> Noeud Svg
        entityWires: null, //-> wire 
        tooltipDiv: null,
        centerNode: null,
        clickedNode: null,
        id: 'PMBGraph',
        constructor: function () {
	        window.d3 = d3;
	        this.entityNodes = [];
	        this.entityWires = [];
	        this.entityLinks = [];
			this.own(
				topic.subscribe('dragEntities', lang.hitch(this,this.handleEvents)),
				topic.subscribe('Tree', lang.hitch(this,this.handleEvents)),
				topic.subscribe('ItemsListUI', lang.hitch(this, this.handleEvents)),
				topic.subscribe('PopupAddLink', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Node', lang.hitch(this, this.handleEvents)),
				topic.subscribe('DatanodesUI', lang.hitch(this, this.handleEvents))
			);	
        },
        
		postCreate:function(){
			this.inherited(arguments);
            on(dom.byId('svgGraph'), 'dragover', lang.hitch(this, this.draggedOver));
//            document.addEventListener('drop', lang.hitch(this, this.dropEntity));
            this.standby = new Standby({id: 'standbyNode', target: this.containerNode, zIndex: 10000000});
            document.body.appendChild(this.standby.domNode);
	    },		
	 
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'dragEntity':
					this.addStartNode(evtArgs.node);
					break;
			    case 'itemTreeSelected':
			    	this.updateProperty(evtArgs);
			    	break;
			    case 'itemSelected':
			    	this.clearGraph();
			    	this.loadGraph(evtArgs);
			    	break;
			    case 'linkCreated':
			    	this.linkCreated(evtArgs);
			    	break;
			    case 'refreshGraph':
			    	this.refreshGraph();
			    	break;
			}
		},	
		dropEntity : function(e) {
			var entity = JSON.parse(e.dataTransfer.getData('text'));
			if (this.entityNodes.length == 0) {
				this.addStartNode(entity);
			}
		},
		
		addStartNode: function(entity){
			this.showPatience();
			request("./ajax.php?module=frbr&categ=cataloging&sub=graph&action=add_start_node", {
				data : {
					num_datanode : this.numDatanode,
					entity_type : entity.entityType,
					entity_id : entity.entityId,
				},
				method: 'POST',
				handleAs: 'json',
			}).then(lang.hitch(this, function(data){
				if (data) {
					this.entityNodes.push(data);					
					this.update();
				}
				this.hidePatience();
			}));
		},
		
		draggedOver: function(e){		
			 e.dataTransfer.dropEffect = 'copy';
			 e.preventDefault();
		},
		
		onShow : function () {
			this.inherited(arguments);		
		},
		
		getGraphData : function() {
			request("./ajax.php?module=frbr&categ=cataloging&sub=graph&action=get_graph_data", {
				data : {
					num_datanode : this.numDatanode,
				},
				method: 'POST',
				handleAs: 'json',
			}).then(lang.hitch(this, function(data){
				if(data){
					this.updateGraphData(data);
				}
			}));
		},
		
		updateGraphData : function(data) {
			this.entityNodes = [];
			this.entityLinks = [];
			this.entityWires = [];
			
			data.entity_nodes.forEach((node) => {
				this.entityNodes.push(new Node(node));
			});
			data.entity_links.forEach((node) => {
				this.entityLinks.push(new LinkNode(node));
			});
//			console.log(data.entity_wires)
			data.entity_wires.forEach((wire) => {
				this.addEntityWire(wire);
			});
			this.update();
			topic.publish('GraphUI', 'graphDataUpdated', {data: data});
		},
		
		displayGraph: function(){
		},

	    getNodes : function() {
			return this.entityNodes.concat(this.entityLinks);
	    },
	    
	    getLinks : function() {
	    	var links = [];
	    	this.entityWires.forEach((link) => {
    			links.push({
	    			target: this.getNodeInstanceFromId(link.target), 
	    			source: this.getNodeInstanceFromId(link.source),
	    			color: (link.color ? link.color : ''),
	    		});	
	    	});
	    	return links;
	    },
	    getNodeInstanceFromId: function(nodeId){
	    	var nodes = this.getNodes();
	    	var nodeFound = null;
//	    	console.log('nodes:' ,nodes, 'node id: ', nodeId);
	    	nodes.forEach((node) => {
	    		if(node.id == nodeId){
	    			nodeFound = node;
	    		}
	    	});
	    	return nodeFound;
	    },
	    
	    loadGraph: function(data){
	    	this.showPatience();
	    	this.clickedNode = data.start_node;
			request("./ajax.php?module=frbr&categ=cataloging&sub=graph&action=set_graph_data", {
				data : {
					num_datanode : this.numDatanode,
					items_list: JSON.stringify(data),
				},
				method: 'POST',
				handleAs: 'json',
			}).then(lang.hitch(this, function(data){
				if(data){
					this.updateGraphData(data);
				}
				this.hidePatience();
			}));
	    },
	    
	    showPatience: function(){
	    	this.standby.show();
	    },
	    
	    hidePatience: function(){
	    	this.standby.hide();
	    },
	    
	    updateProperty : function (evtArgs) {
			let newNumDatanode = evtArgs.itemTree.id;
			if (newNumDatanode != this.numDatanode) {
				this.numDatanode = newNumDatanode;
				this.clearGraph();
			}
	    },
	    findNode: function(id, type){
	    	
	    },
	    linkCreated: function(data){ //{retourAjax, entity}
//	    	{data: data, entity:{id:entityDroppedId, type: entityDroppedType}}
	    	/**
	    	 * TODO: Voir si nous ne pouvons pas retourner directement le bon type dans le point d'entrée PHP
	    	 */
	    	var entity = data.entity;
	    	if(entity.type != "record"){
	    		entity.type = "authorities";
	    	}else{
	    		entity.type = "records";
	    	}
	    	var node = this.getNodeInstanceFromId(entity.type+'_'+data.entity.id);
	    	node.removeAddLinkPopup();
	    	
	    	var itemsListUI = registry.byId('FRBRItemsContainer');
	    	this.loadGraph({start_node: this.clickedNode, items: itemsListUI.getEntitiesList(this.clickedNode)})
	    },
	    addEntityWire: function(wire){
	    	var flag = true; 
	    	for(var i=0 ; i<this.entityWires.length ; i++){
	    		if((this.entityWires[i].source == wire.source) && (this.entityWires[i].target == wire.target)){
	    			flag = false;
	    		}
	    	}
	    	if(flag){
	    		this.entityWires.push(wire);
	    	}
	    },
	    
	    refreshGraph : function() {
	    	if(this.clickedNode){
	    		var entityId = query('a[data-element-id="'+this.clickedNode.id+'"]')[0];
				var entityType = query('a[data-element-type="'+this.clickedNode.type+'"]')[0];
				if (entityId && entityType) {
					var itemsListUI = registry.byId('FRBRItemsContainer');
			    	this.loadGraph({start_node: this.clickedNode, items: itemsListUI.getEntitiesList(this.clickedNode)});
				}
	    	}
	    },
	    
	    clearGraph : function() {
	    	this.updateGraphData({entity_nodes:[], entity_links:[], entity_wires:[]});
	    },
	});
});