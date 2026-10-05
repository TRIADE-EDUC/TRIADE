// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: LinkNode.js,v 1.3 2018/03/19 14:49:42 vtouchard Exp $

define([
        "dojo/_base/declare", 
        "dojo/_base/lang", 
        "dojo/topic", 
        "dojo/dom", 
        "dojo/dom-class", 
        "dojo/query", 
        "d3/d3",
        "apps/pmb/graph/Node",
    ], function(declare,lang, topic, dom, domClass, query, d3, Node){
	return declare([Node], {
		nodeType: 'link',
		constructor: function(){
		
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
			//A DERIVER 
		},
		dragLeave: function(){
			//A DERIVER
		},
		dragDrop : function(){
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
		rightClicked: function(e){
		}
	});
});
	