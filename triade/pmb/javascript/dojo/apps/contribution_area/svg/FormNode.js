// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormNode.js,v 1.4 2018/10/16 12:06:57 apetithomme Exp $

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
		selectNode: function(){
			this.unselectNode();
		},
	});
});
	