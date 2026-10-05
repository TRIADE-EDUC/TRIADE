// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Node.js,v 1.11 2018/03/21 10:36:12 tsamson Exp $

define([
        "dojo/_base/declare", 
        "dojo/_base/lang", 
        "dojo/topic", 
        "dojo/dom", 
        "dojo/dom-class", 
        "dojo/query", 
        "d3/d3",
        "apps/pmb/graph/Node",
        "dojo/request",
        "apps/pmb/PMBDojoxDialogSimple",
        "dijit/Menu",
        "dijit/MenuItem",
        "dijit/CheckedMenuItem",
        "dijit/MenuSeparator",
        "dijit/PopupMenuItem",
        "dijit/registry",
    ], function(declare,lang, topic, dom, domClass, query, d3, Node, request, Dialog, Menu, MenuItem, CheckedMenuItem, MenuSeparator, PopupMenuItem, registry){
	return declare([Node], {
		nodeType: 'entity',
		eltId: null,
		links: null,
		contextMenu: null,
		constructor: function(){
			this.links = arguments[0].links;
			this.eltId = arguments[0].eltId;
			this.img = arguments[0].img;
			console.log('node: ', this);
		},
		handleEvents: function(evtType, evtArgs){
		},
		canReceive: function(element){
//			d3.select("circle[id='"+this.id+"']").classed("inactive", false);
//			switch(element.type) {
//				case 'scenario':
//					element.parent_type = element.entityType;
//					break;
//				case 'form':
//				default :
//					break;
//			}	
//			if(element.parent_type != this.entityType){
//				d3.select("circle[id='"+this.id+"']").classed("inactive", true);
//			}
		},
		clicked: function(){
			this.selectNode();
		},
		
		selectNode: function(){			
//			if(this.isActive()){
//				this.unselectNode();
//			}else{
//				d3.selectAll('circle').classed("active", false);
//				d3.select("circle[id='"+this.id+"']").classed("active", true);
//				topic.publish('Node','nodeSelected', {node:this});
//			}
			
		},
		isActive: function(){
//			return d3.select("circle[id='"+this.id+"']").classed("active");
		},
		
		dragOver: function(){
			d3.select("circle[id='"+this.id+"']").classed("droppable", true);
			//A DERIVER 
		},
		dragLeave: function(){
			d3.select("circle[id='"+this.id+"']").classed("droppable", false);
			//A DERIVER
		},
		dragDrop : function(elt, e){
			/**
			 * Get everything back
			 */
			var graph = registry.byId('PMBGraph');
			graph.showPatience();
			d3.select("circle[id='"+this.id+"']").classed("droppable", false);
//			console.log('arguments dropped on', )
			var entity = JSON.parse(e.dataTransfer.getData('text'));
			var data = {
				source: JSON.stringify({type: this.type, id: this.eltId}),
				target: JSON.stringify(entity)
			}
			
			request("./ajax.php?module=frbr&categ=cataloging&sub=graph&action=get_link_form", {
				data : data,
				method: 'POST',
				handleAs: 'json',
			}).then(lang.hitch(this, function(data){

				if (data.status) {
					if (data.entity_added) {
						topic.publish('Node', 'refreshGraph');
					}else if(data.html.trim()){
						this.dialogLinkEntity = new Dialog({title: pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_link_an_entity'), content: data.html});
						this.dialogLinkEntity.placeAt(document.body);
						graph.hidePatience();
						var hideSave = this.dialogLinkEntity.hide;
						this.dialogLinkEntity.hide = lang.hitch(this, function(){
							lang.hitch(this.dialogLinkEntity, hideSave)();
							this.dialogLinkEntity.destroyRecursive();
							this.dialogLinkEntity = null;
						});
						this.dialogLinkEntity.startup();
						this.dialogLinkEntity.show();
					}	
				} else {
					alert(pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_link_no_link_possible'));
					graph.hidePatience();
				}
			}));
//			var elt = window.draggedContributionElt;
//			
//			switch(elt.type) {
//			case 'form':
//				elt.id = elt.form_id;
//				break;
//			case 'scenario':
//				elt.parent_type = elt.entityType;
//				break;
//			}	
//			topic.publish("Node", 'elementDropped', {target:this, elt:elt});
//			d3.select("circle[id='"+this.id+"']").classed("droppable", false);
//			d3.selectAll("circle").classed("inactive", false);
		},
		
		unselectNode: function(){
//			d3.selectAll('circle').classed("active", false);
//			topic.publish('Node','nodeUnselected', {});
		},
		setPosition: function(data){
			if(data.x && data.y){
				this.x = data.x;
				this.y = data.y;
			}else{
				this.x = 0;
				this.y = 0;
			}
		},
		destroy: function(){
			for(var i=0 ; i<this.signals.length ; i++){
				this.signals[i].remove();
			}
			for(var key in this){
				this[key] = null;
			}
		},
		removeAddLinkPopup: function(){
			this.dialogLinkEntity.hide();
		},
		getContextualMenu: function(x, y){
			if(!this.contextualMenu){
				this.createContextualMenu(this.id);
				if(dom.byId(this.id+'_img')){
					this.createContextualMenu(this.id+'_img');	
				}
			}
		},
		createContextualMenu: function(id){
			this.contextualMenu = new Menu({
				targetNodeIds: [id]
			});
			this.contextualMenu.addChild(new MenuItem({
		        label: "Simple menu item",
		    }));
			this.contextualMenu.startup();
		}
	});
});
	