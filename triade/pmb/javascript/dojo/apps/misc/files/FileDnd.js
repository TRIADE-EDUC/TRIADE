// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FileDnd.js,v 1.2 2018-11-23 13:58:14 dgoron Exp $

define(['dojo/_base/declare',
        'dojo/dnd/Source',
        'dojo/_base/array',
        'dojo/query!css3',
        'dojo/dom-attr',
        'dojo/dom'
], function(declare, Source, array, query, domAttr, dom) {
	return declare([Source], {
		
		fileController : null,
		
		withHandles: true,
		
		onDrop: function(source, nodes, copy) {
			this.inherited(arguments);
			var elements = source.node.children;
			this.updateGroup(nodes[0]);
		},
		updateGroup: function(item) {
			if(item.nextElementSibling) {
				var group = '';
				if(item.nextElementSibling.getAttribute('data-file-group')) {
					group = item.nextElementSibling.getAttribute('data-file-group');
				}
				domAttr.set(item, 'data-file-group', group);
				var domHiddenGroup = dom.byId("subst_file_data_"+domAttr.get(item, 'data-file-element')+"_group");
				if(domHiddenGroup) {
					domAttr.set(domHiddenGroup, 'value', group);
				}
			}
		},
		checkAcceptance: function(source, nodes) {
			return true;
		}
	});
});