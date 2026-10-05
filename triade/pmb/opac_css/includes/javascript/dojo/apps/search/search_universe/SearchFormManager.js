// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchFormManager.js,v 1.8 2021/12/15 11:26:48 gneveu Exp $

define(['dojo/_base/declare',
        'dojo/_base/lang',
        'dojo/on',
        'dojo/dom-attr',
        'dojo/request',
        'dojo/dom-form',
        'dojo/io-query',
        'dojo/dom-construct',
        'dojo/query!css3',
], function(declare,lang, on, domAttr, request, domForm, ioQuery, domConstruct, query) {
	return declare(null, {
		
		inDialog : false,
		
		constructor: function() {
			this.manageForm();
		},
		
		manageForm: function() {
            window.addEventListener('DOMContentLoaded', lang.hitch(this, function() {
				var form = query('form[name="search_form"]')[0];
				if(form){
			    	domAttr.remove(form, "onSubmit");
					on(form , 'submit', lang.hitch(this, this.postForm));
				}
				var elem = document.getElementById('search_form_submit');
				if(elem){
					elem.replaceWith(elem.cloneNode(true));
				}
			}));
		},
		
		postForm: function(e){
			e.preventDefault();
			e.stopPropagation();
			
			let params = ioQuery.queryToObject(document.location.href);
			let form_data = domForm.toObject(e.target);
			
			//peut etre faudrait-il utiliser une autre classe que selector_search_segment
			request("./ajax.php?module=selectors&what=search_segment&action=serialize_search", {
				data: form_data,
				method: 'POST',
				handleAs: 'json',
			}).then(lang.hitch(this, function(data) {
				if (data.serialize_search) {
					if (this.inDialog) {
						this.refineSearch(data.serialize_search);
					} else {
						this.newAdvancedSearch(data.serialize_search);
					}
				}
			}));
			if (this.inDialog) {
				window.close();
			}
			return false;
		},
		
		refineSearch : function(serializedSearch) {
			var user_rmc = window.parent.document.getElementById("refine_user_rmc");
			if (user_rmc) {
				user_rmc.value = serializedSearch;
				var parent_form = window.parent.document.getElementById("refine_search_input");
				if (parent_form) {
					parent_form.submit();
				}
			}
		},
		
		newAdvancedSearch : function(serializedSearch) {
			var user_rmc = document.getElementById("segment_user_rmc");
			if (user_rmc) {
				user_rmc.value = serializedSearch;
				var parent_form = document.getElementById("segment_advanced_form");
				if (parent_form) {
					parent_form.submit();
				}
			}
		}
	});
});