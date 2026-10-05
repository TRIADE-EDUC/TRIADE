// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchDnd.js,v 1.3 2016/11/28 10:37:20 apetithomme Exp $

define(['dojo/_base/declare',
        'dojo/dnd/Source',
        'dojo/_base/array',
        'dojo/query!css3',
        'dojo/dom-attr'
], function(declare, Source, array, query, domAttr) {
	return declare([Source], {
		
		searchController : null,
		
		withHandles: true,
		
		onDrop: function(source, nodes, copy) {
			this.inherited(arguments);
			var elements = source.node.children;
			
			if (elements.length) {
				array.forEach(elements, this.renameSearchFields, this);
			}
			this.searchController.getFormInfos();
		},
		
		renameSearchFields: function(item, i, list) {
			var oldIndex = domAttr.get(item, 'search_field_index');
			query('input, select', item).forEach(function(node, id, nodesList) {
				if (node.name.indexOf('inter_' + oldIndex + '_') != -1) {
					node.name = node.name.replace('inter_' + oldIndex + '_', 'inter_' + i + '_');
				}
				if (node.name.indexOf('op_' + oldIndex + '_') != -1) {
					node.name = node.name.replace('op_' + oldIndex + '_', 'op_' + i + '_');
				}
				if (node.name.indexOf('field_' + oldIndex + '_') != -1) {
					node.name = node.name.replace('field_' + oldIndex + '_', 'field_' + i + '_');
				}
				if (node.name.indexOf('fieldvar_' + oldIndex + '_') != -1) {
					node.name = node.name.replace('fieldvar_' + oldIndex + '_', 'fieldvar_' + i + '_');
				}
			});
		},
		
		checkAcceptance: function(source, nodes) {
			return true;
		}
	});
});