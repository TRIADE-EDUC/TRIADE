// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ManageQueryHuman.js,v 1.1 2021/02/15 14:26:45 dgoron Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/dom-construct",
        "dojo/dom-style",
        "dojo/request/xhr",
        "dojo/ready"
], function(declare, lang, request, query, on, domAttr, dom, domConstruct, domStyle, xhr, ready){
	return declare(null, {
		objects_type:null,
		constructor: function(objects_type) {
			this.objects_type = objects_type;
			var filtersNodes = document.querySelectorAll("."+this.objects_type+"_query_human_filter_reset");
			if(filtersNodes.length) {
				filtersNodes.forEach(function(filterNode) {
					var node_id = this.objects_type+'_query_human_filter_reset_'+filterNode.getAttribute('data-property');
					if(dom.byId(node_id)) {
						on(dom.byId(node_id), 'click', lang.hitch(this, this.filterReset, filterNode.getAttribute('data-property')));
					}
				}, this); 
			}
		},
		filterReset: function(property) {
			if(document.getElementsByName(this.objects_type+"_"+property)) {
				var nodes = document.getElementsByName(this.objects_type+"_"+property);
				if(nodes.length == 0) {
					nodes = document.getElementsByName(property);
				}
				if(nodes.length > 1) {
					nodes[0].setAttribute("checked", "checked");
				} else if(nodes.length == 1) {
					if(nodes[0].nodeName == 'INPUT') {
						nodes[0].value = '';
					} else if(nodes[0].nodeName == 'SELECT') {
						nodes[0].options[0].selected = true;
					}
				}
				if(dom.byId(this.objects_type+'_search_form')) {
					dom.byId(this.objects_type+'_search_form').submit();
				}
			}
		},
	});
});