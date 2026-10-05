// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SimpleExternalSearchController.js,v 1.2 2016/11/25 16:34:48 apetithomme Exp $

define(['dojo/_base/declare',
        'apps/search/SearchController',
        'dojo/query!css3',
        'dojo/dom-construct',
        'dojo/dom-style'
], function(declare, SearchController, query, domConstruct, domStyle) {
	return declare([SearchController], {
		
		updateForm: function(form) {
			this.searchFieldsList = query('table tbody tr', form);
			var submit_button = query('input[type="submit"]', form)[0];
			if (this.searchFieldsList.length) {
				if (domStyle.get(submit_button, 'display') == 'none') {
					domStyle.set(submit_button, 'display', 'block');
				}
				if (dojo.byId("search_fields_no_selected_fields")) {
					domConstruct.destroy("search_fields_no_selected_fields");
				}
				this.initDnd();
				this.updateDeleteButtons();
			} else {
				domStyle.set(submit_button, 'display', 'none');
				domConstruct.place('<span class="saisie-contenu" id="search_fields_no_selected_fields">' + pmbDojo.messages.getMessage('search', 'search_fields_no_selected_fields') + '</span>',query('div.form-contenu', form)[0]);
			}
		},
		
		getTreeTitle: function() {
			return query('label', dojo.byId('add_field').parentNode)[0].innerHTML;
		}
	});
});