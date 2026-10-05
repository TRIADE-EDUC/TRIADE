// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Node.js,v 1.8 2022/12/20 10:23:03 qvarin Exp $

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
		shape: '',
		entityType: null,
		active: null,
		graphShapes: null,
		isRecipient: null,
		fixed: true,
		x: null,
		y:null,
		signals: null,
		destType: '',
		img: '',
		constructor: function(data, graphShapes){
			this.name = data.name;
			this.destType = data.destType;
			this.id = data.id.toString();
			this.type = data.type;
			this.color = graphShapes ? graphShapes.color : "#000000";
			this.radius = graphShapes ? parseInt(graphShapes.size) : 15;
			this.shape = graphShapes ? graphShapes.shape : 'circle';
			this.entityType = data.entityType;
			this.img = this.getPicto(this.entityType);
			this.setPosition(data);
			this.signals = [];
			this.signals.push(topic.subscribe('Graph', lang.hitch(this, this.handleEvents)));
			this.signals.push(topic.subscribe('FormsList', lang.hitch(this, this.handleEvents)));
			this.signals.push(topic.subscribe('ScenariosList', lang.hitch(this, this.handleEvents)));
		},
		handleEvents: function(evtType, evtArgs){
			switch(evtType){
				case 'formEltDragStart':
					this.canReceive(evtArgs.formElt);
					break;
				case 'formEltClicked':
					this.formEltClicked(evtArgs.formElt);
					break;
				case 'scenarioEltUnselected':
				case 'formEltUnselected':
					d3.select(this.shape + "[id='"+this.id+"']").classed("inactive", false);
					break;
				case 'scenarioDragEnd':
				case 'formDragEnd':
					d3.select(this.shape + "[id='"+this.id+"']").classed("inactive", false);
					d3.select(this.shape + "[id='"+this.id+"']").classed("droppable", false);
					d3.select(this.shape + "[id='"+this.id+"']").classed("active",false);
					break;
				case 'scenarioEltDragStart':
					this.canReceive(evtArgs.scenarioElt);
					break;
			}
		},
		canReceive: function(element){
			d3.select(this.shape + "[id='"+this.id+"']").classed("inactive", false);
			switch(element.type) {
				case 'scenario':
					element.parent_type = element.entityType;
					break;
				case 'form':
				default :
					break;
			}	
			if(element.parent_type != this.entityType){
				d3.select(this.shape + "[id='"+this.id+"']").classed("inactive", true);
			}
		},
		clicked: function(){
			this.selectNode();
		},
		
		selectNode: function(){
			if(!this.isActive()) {
				this.unselectNode();
				d3.select(this.shape + "[id='"+this.id+"']").classed("active", true);
				topic.publish('Node','nodeSelected', {node:this});
			} else {
				this.unselectNode();
			}
			
		},
		isActive: function(){
			return d3.select(this.shape + "[id='"+this.id+"']").classed("active");
		},
		formEltClicked: function(formElt){
			this.canReceive(formElt);
		},
		
		dragOver: function(){
			//A DERIVER 
		},
		dragLeave: function(){
			//A DERIVER
		},
		dragDrop : function(){
			var elt = window.draggedContributionElt;
			
			switch(elt.type) {
			case 'form':
				elt.id = elt.form_id;
				break;
			case 'scenario':
				elt.parent_type = elt.entityType;
				break;
			}	
			topic.publish("Node", 'elementDropped', {target:this, elt:elt});
			d3.select(this.shape + "[id='"+this.id+"']").classed("droppable", false);
			d3.selectAll(this.shape).classed("inactive", false);
		},
		
		unselectNode: function(){
			d3.selectAll(".active").classed("active", false);
			topic.publish('Node','nodeUnselected', {});
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
		getPicto: function(entity) {
		
			let entityType = entity;
			if (Array.isArray(entity) && entity.length == 1) {
				entityType = entity[0];
			}
		
			switch(entityType) {
				case 'work':
					return './images/authorities/titre_uniforme_icon.png';
				case 'record':
					return './images/icon_a.gif';
				case 'docnum':
					return './images/icone_nouveautes.png';
				default:
					if (entity.indexOf('authperso') >= 0){
						return './images/authorities/authperso_icon.png';
					}
					if (Array.isArray(entity) && entity.length > 1) {
						return './images/authorities/author_icon.png';
					}
					return './images/authorities/' + entity + '_icon.png';
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
	