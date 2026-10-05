// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchUniverseFormManager.js,v 1.2 2021/11/03 15:59:23 tsamson Exp $

define(['dojo/_base/declare',
		'apps/search/search_universe/SearchFormManager',
        'dojo/_base/lang',
        'dojo/on',
        'dojo/dom-attr',
        'dojo/request',
        'dojo/dom-form',
        'dojo/io-query',
        'dojo/dom-construct',
        'dojo/query!css3',
], function(declare,SearchFormManager,lang, on, domAttr, request, domForm, ioQuery, domConstruct, query) {
	return declare([SearchFormManager], {
			
		newAdvancedSearch : function(serializedSearch) {
			var user_rmc = document.getElementById("universe_user_rmc");
			if (user_rmc) {
				user_rmc.value = serializedSearch;
				var parent_form = document.getElementById("search_universe_input");
				if (parent_form) {
					//parent_form.submit();
					on.emit(parent_form, "submit", {
					    bubbles: true,
					    cancelable: true
				    });
				}
			}
		}
	});
});