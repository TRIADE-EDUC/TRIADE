// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ManageSearch.js,v 1.1 2018-04-24 12:49:03 dgoron Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/dom-style",
        "dojo/ready"
], function(declare, lang, request, query, on, domAttr, dom, domStyle, ready){
	return declare(null, {
		objects_type:null,
		constructor: function(objects_type) {
			this.objects_type = objects_type;
			on(dom.byId(this.objects_type+'_search_img'), 'click', lang.hitch(this, this.contentShow));
		},
		contentShow: function() {
			var domNode = dom.byId(this.objects_type+'_search_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_search_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_search_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		}
	});
});