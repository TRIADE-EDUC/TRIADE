// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ScenarioNode.js,v 1.2 2018/10/16 12:06:57 apetithomme Exp $

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
		name: null,
		id: null,
		type: null,
		
		dragOver: function(){
			//on r�cup�re le form en drag..
			var elt = window.draggedContributionElt;
//			var form = JSON.parse(d3.event.dataTransfer.getData('form'));
			//si c'est le m�me type
			if(elt.type != 'scenario'){
				if(elt.parent_type == this.entityType){
					//on s'assure qu'il n'est pas d�j� associ� � ce noeud pr�cis...
					var forms = graphStore.query({parent:this.id,type:'form'});
					var alreadyDroppedHere = false;
					forms.forEach(function(checkingform){
						if(checkingform.eltId == elt.form_id){
							alreadyDroppedHere = true;
						}
					})
					if(!alreadyDroppedHere){
						d3.select(this.shape + "[id='"+this.id+"']").classed("droppable", true);
						d3.event.preventDefault();
					}else{
						d3.select(this.shape + "[id='"+this.id+"']").classed("alreadyDropped", true);
					}
				}	
			}
			
		},
		canReceive: function(element){
			d3.select(this.shape + "[id='"+this.id+"']").classed("inactive", false);
			if(element.parent_type != this.entityType || element.type == 'scenario'){
				d3.select(this.shape + "[id='"+this.id+"']").classed("inactive", true);
			}
		},
		dragLeave: function(){
			d3.select(this.shape + "[id='"+this.id+"']").classed("droppable", false);
			d3.select(this.shape + "[id='"+this.id+"']").classed("alreadyDropped", false);
		},
	});
});
	