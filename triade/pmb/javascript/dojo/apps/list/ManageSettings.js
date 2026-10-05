// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ManageSettings.js,v 1.3 2021/05/06 11:25:54 dgoron Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/dom-style",
        "dojo/request/xhr",
        "dojo/ready"
], function(declare, lang, request, query, on, domAttr, dom, domStyle, xhr, ready){
	return declare(null, {
		objects_type:null,
		constructor: function(objects_type) {
			this.objects_type = objects_type;
			on(dom.byId(this.objects_type+'_settings_img'), 'click', lang.hitch(this, this.contentShow));
			if(dom.byId(this.objects_type+'_settings_display_img')) {
				on(dom.byId(this.objects_type+'_settings_display_img'), 'click', lang.hitch(this, this.displayContentShow));
			}
			if(dom.byId(this.objects_type+'_settings_columns_img')) {
				on(dom.byId(this.objects_type+'_settings_columns_img'), 'click', lang.hitch(this, this.columnsContentShow));
			}
			if(dom.byId(this.objects_type+'_settings_filters_img')) {
				on(dom.byId(this.objects_type+'_settings_filters_img'), 'click', lang.hitch(this, this.filtersContentShow));
			}
			if(dom.byId(this.objects_type+'_settings_selection_actions_img')) {
				on(dom.byId(this.objects_type+'_settings_selection_actions_img'), 'click', lang.hitch(this, this.selectionActionsContentShow));
			}
		},
		contentShow: function() {
			var domNode = dom.byId(this.objects_type+'_settings_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_settings_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_settings_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		},
		displayContentShow: function() {
			var domNode = dom.byId(this.objects_type+'_settings_display_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_settings_display_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_settings_display_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		},
		columnsContentShow: function() {
			var domNode = dom.byId(this.objects_type+'_settings_columns_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_settings_columns_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_settings_columns_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		},
		filtersContentShow: function() {
			var domNode = dom.byId(this.objects_type+'_settings_filters_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_settings_filters_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_settings_filters_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		},
		selectionActionsContentShow: function() {
			var domNode = dom.byId(this.objects_type+'_settings_selection_actions_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_settings_selection_actions_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_settings_selection_actions_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		},
	});
});