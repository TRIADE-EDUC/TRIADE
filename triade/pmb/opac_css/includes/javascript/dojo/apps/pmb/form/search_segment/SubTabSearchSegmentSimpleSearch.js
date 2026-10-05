// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabSearchSegmentSimpleSearch.js,v 1.2 2021/10/13 13:16:27 tsamson Exp $


define([
        'dojo/_base/declare',
        'dojo/_base/lang',
        'dojo/request',
        'dojo/dom-form',
        'dojo/topic',
        'apps/pmb/form/SubTabSimpleSearch'
        ], function(declare, lang, request, domForm, topic, SubTabSimpleSearch) {
		return declare([SubTabSimpleSearch], {
			postForm: function(e) {
				e.preventDefault();
				e.stopPropagation();

				let form_data = domForm.toObject(this.form);

				//topic.publish('SubTabSimpleSearch', 'SubTabSimpleSearch', 'initStandby');
				
				var user_query = window.parent.document.getElementById("segment_user_query");
				if (user_query) {
					user_query.value = form_data['user_query'];
					var parent_form = window.parent.document.getElementById("search_segment_input");
					if (parent_form) {
						parent_form.submit();
					}
				}
				
				window.close();
				return false;
			}
		})
});