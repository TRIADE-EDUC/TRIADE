// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Node.js,v 1.2 2018/03/13 09:31:55 tsamson Exp $

define([
        "dojo/_base/declare", 
        "dojo/_base/lang", 
        "dojo/topic", 
        "dojo/dom", 
        "dojo/dom-class", 
        "dojo/query", 
        "d3/d3"
    ], function(declare,lang, topic, dom, domClass, query, d3){
	return declare(null, {
		name: null,
		id: null,
		type: null,
		radius:null,
		x: null,
		y:null,
		colors: ["#1f77b4", "#aec7e8", "#ff7f0e", "#ffbb78", "#2ca02c", "#98df8a", "#d62728", "#ff9896", "#9467bd", "#c5b0d5", "#8c564b", "#c49c94", "#e377c2", "#f7b6d2", "#7f7f7f", "#c7c7c7", "#bcbd22", "#dbdb8d", "#17becf", "#9edae5"],
		constructor: function(data){
			this.name = data.name;
			this.id = data.id.toString();
			this.type = data.type;
			this.radius = data.radius;
			this.setPosition(data);
			this.color = data.color;
			this.signals = [];
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
		rightClicked: function(){
			//A dériver
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
		}
	});
});
	