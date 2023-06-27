// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchFieldsTree.js,v 1.4 2016-11-25 16:34:48 apetithomme Exp $

define(['dojo/_base/declare',
        'dijit/Tree'
], function(declare, Tree) {
	return declare([Tree], {
		
		searchController: null,
		
		id: 'searchFieldsTree',

		showRoot: false,
		
		persist: true,
		
		openOnClick: true,
		
		getLabel: function(item) {
			return item.label;
		},
		
		onDblClick: function(item, node, evt) {
			if (item.leaf) {
				dojo.byId('add_field').value = item.id;
				this.searchController.getFormInfos();
				dojo.byId('add_field').value = '';
			}
		}
	});
});