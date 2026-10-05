// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AttachmentNode.js,v 1.8 2022/03/15 14:40:51 tsamson Exp $

define([
        "dojo/_base/declare", 
        "dojo/_base/lang", 
        "dojo/topic", 
        "dojo/dom-class", 
        "dojo/query", 
        "apps/contribution_area/svg/Node",
        "d3/d3"
    ], function(declare,lang, topic, domClass, query, SvgNode, d3){
	return declare(SvgNode, {
		propertyPmbName: '',
		
		constructor: function(data, graphShape){
			this.propertyPmbName = data.propertyPmbName;
		},
		
		dragOver: function(){
			var elt = window.draggedContributionElt;
			if (elt.type == 'scenario') {
				//si c'est le méme type		
				if(this.entityType.includes(elt.entityType)){
					//on s'assure qu'il n'est pas déjé associé é ce noeud précis...
					var elts = graphStore.query({parent:this.id,type:'scenario'});
					var alreadyDroppedHere = false;
					elts.forEach(function(checkingElt){
                        if(typeof checkingElt.name !== undefined && typeof elt.name !== undefined && checkingElt.name== elt.name){
                            alreadyDroppedHere = true;
                        }
					});
					if(!alreadyDroppedHere){
						d3.select(this.shape + "[id='"+this.id+"']").classed("droppable", true);
						d3.event.preventDefault();
					}else{
						d3.select(this.shape + "[id='"+this.id+"']").classed("alreadyDropped", true);
					}
				} else {
					d3.select(this.shape + "[id='"+this.id+"']").classed("alreadyDropped", true);
				}
			}
		},
		
		dragLeave: function(){		
			d3.select(this.shape + "[id='"+this.id+"']").classed("droppable", false);
			d3.select(this.shape + "[id='"+this.id+"']").classed("alreadyDropped", false);
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
			if(element.parent_type != this.entityType || (element.type == "form")){
				d3.select(this.shape + "[id='"+this.id+"']").classed("inactive", true);
			}
		},
	});
});
	