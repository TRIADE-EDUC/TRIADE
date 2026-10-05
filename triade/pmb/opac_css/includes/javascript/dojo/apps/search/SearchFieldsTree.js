// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchFieldsTree.js,v 1.4 2020/12/24 14:26:49 qvarin Exp $

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
			    var node = dojo.byId('add_field');
			    node.value = item.value;
			    
			    if (item.authperso && node.form.authperso_id) {
			        node.form.authperso_id.value = item.authperso
                }
			    
				this.searchController.getFormInfos();
				node.value = '';
			}
		}
	});
});