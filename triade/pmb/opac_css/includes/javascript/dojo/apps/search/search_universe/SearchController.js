// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchController.js,v 1.3 2021/10/13 13:16:27 tsamson Exp $

define(['dojo/_base/declare',
		'apps/search/SearchController',
        'dojo/_base/lang',
        'dojo/on',
        'dojo/dom-attr',
        'dojo/request',
        'dojo/dom-form',
        'dojo/io-query',
        'dojo/dom-construct',
        'dojo/query!css3',
        'dojo/parser',
        'dijit/layout/ContentPane',
], function(declare, SearchController,lang, on, domAttr, request, domForm, ioQuery, domConstruct, query, parser, ContentPane) {
	return declare([SearchController], {
		
		buildForm: function() {
			var form = query('form[name="search_form"]')[0];
			domConstruct.place(form,this.contentForm.id);
			this.widgets = parser.parse(form);
			this.updateForm(form);
	    	domAttr.remove(form, "onSubmit");
			on(form , 'submit', lang.hitch(this, this.postForm));
		},
		
		postForm: function(e){
			e.preventDefault();
			e.stopPropagation();
			
			let params = ioQuery.queryToObject(document.location.href);
			let form_data = domForm.toObject(e.target);
			
			request("./ajax.php?module=selectors&what=search_segment&action=serialize_search&selector_data="+params.selector_data+"&iframe="+params.iframe, {
				data: form_data,
				method: 'POST',
				handleAs: 'json',
			}).then(lang.hitch(this, function(data) {
				if (data.serialize_search) {
					var user_rmc = window.parent.document.getElementById("refine_user_rmc");
					if (user_rmc) {
						user_rmc.value = data.serialize_search;
						var parent_form = window.parent.document.getElementById("refine_search_input");
						if (parent_form) {
							parent_form.submit();
						}
					}
				}
			}));
			window.close();
			return false;
		},
	});
});