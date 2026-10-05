// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FieldsTree.js,v 1.2 2017/04/14 08:58:55 dgoron Exp $

define(['dojo/_base/declare',
        'dijit/Tree'
], function(declare, Tree) {
	return declare([Tree], {
		
		entityManageController: null,

		showRoot: false,
		
		persist: true,
		
		openOnClick: true,
		
		getLabel: function(item) {
			return item.label;
		},
		
		onDblClick: function(item, node, evt) {
			if (item.leaf) {
				dojo.byId(this.entityManageController.domNodeId+'_add_field').value = item.id;
				this.entityManageController.getFormInfos();
				dojo.byId(this.entityManageController.domNodeId+'_add_field').value = '';
			}
		}
	});
});