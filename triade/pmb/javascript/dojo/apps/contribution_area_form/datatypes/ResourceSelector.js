// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ResourceSelector.js,v 1.1 2017/09/13 12:38:29 tsamson Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/dom-construct',
        'dijit/registry',
        'dojo/store/Memory',
        'dijit/form/FilteringSelect',
        'dojo/when'
], function(declare, dom, on, lang, topic, domConstruct, registry, Memory, FilteringSelect, when){
	return declare([FilteringSelect], {
		constructor: function() {			
			
		},
		
		postCreate : function() {
			this.inherited(arguments);			
		},

		_setValueAttr: function(/*String*/ value, /*Boolean?*/ priorityChange, /*String?*/ displayedValue, /*item?*/ item){
			this.inherited(arguments);
			if (item && item.value) {
				dom.byId(this.valueNodeId).value = item.value; 
			}
		},
	})
});