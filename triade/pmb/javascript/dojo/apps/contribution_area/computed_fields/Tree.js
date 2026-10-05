// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Tree.js,v 1.6 2019/02/05 15:01:13 apetithomme Exp $

define(['dojo/_base/declare',
        'dijit/Tree',
        'dojo/dom-construct',
        'dijit/tree/dndSource',
        'dojo/_base/lang',
        'dojo/on',
        'dojo/dom',
        'dojo/topic'
], function(declare, Tree, domConstruct, dndSource, lang, on, dom, topic) {
	return declare([Tree], {	
		id: 'scenariosComputedTree',
		
		dndController: dndSource,
		
		showRoot: false,
		
		persist: true,
		
		openOnClick: true,
						
		postCreate: function() {
			this.inherited(arguments);
			this.dndController.checkAcceptance = lang.hitch(this, this.dndCheckAcceptance);
			this.dndController.checkItemAcceptance = lang.hitch(this, this.dndCheckItemAcceptance);
		},
		
		dndCheckAcceptance: function(source, nodes) {
			var item = source.tree.selectedItem;
			if (this.isLeaf(item)) return true;
			return false;
		},

		dndCheckItemAcceptance: function(target, source, position) {
			return false;
		},
		
		getLabel: function(item) {
			return item.name;
		},
		
		getLabelStyle: function(item) {
			if (item.alreadyComputed) {
				return {'font-weight': 'bold'};
			}
		},
		
		onDblClick: function(item, node, evt) {
			if (this.isLeaf(item) && !item.selection) topic.publish('dblClick', item, node, evt);
		},
		
		getIconClass: function(item, opened){
		    return (this.isLeaf(item) ? "dijitLeaf" : (opened ? "dijitFolderOpened" : "dijitFolderClosed"));
		},
		
		/**
		 * Dis si un item est une feuille de l'arbre
		 */
		isLeaf: function(item) {
			return ((item.type == "property") || (item.type === 'environmentField') || (item.type === 'emprField'));
		}
	});
});