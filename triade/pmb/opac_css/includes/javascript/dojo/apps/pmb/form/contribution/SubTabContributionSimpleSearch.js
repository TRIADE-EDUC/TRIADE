// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabContributionSimpleSearch.js,v 1.9 2022/03/23 15:03:56 gneveu Exp $


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
				let form_data = domForm.toObject(this.form);

				var readSearchFields = false;
				var emptySearchFields = true;
				let i = 1;
				
				while(readSearchFields == false){
					if(typeof form_data['search_field_tab_' + i + '[]'] === "undefined"){
						readSearchFields = true;
						break;
					}
					if(form_data['search_field_tab_' + i + '[]'] !== ""){
						emptySearchFields = false
						break;
					}
					i++;
				}
				
				if(emptySearchFields){
					form_data['search_field_tab_1[]'] = "*";
				}
				
				let selectResultTabSearch = form_data["selectResultTabSearch"];
				topic.publish('SubTabSimpleSearch', 'SubTabSimpleSearch', 'initStandby', {formId: e.target.id});
				request(this.parameters.selectorURL+"&action=results_search", {
					data: form_data,
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data) {
					data = JSON.parse(data);
					topic.publish('SubTabSimpleSearch', 'SubTabSimpleSearch', 'printResults', {formId: e.target.id, results: data.results, nb_results: data.nb_results, origin: this.parameters.selectorURL + "&action=results_search", selectResultTabSearch});
					//Declenchement de la seconde requete quand la premi�re est r�solue
					let selector_data = JSON.parse(this.parameters.queryParameters.selector_data);
					form_data.type = selector_data.type;
					
					if(!selector_data.is_entity){
						topic.publish('SubTabSimpleSearch', 'SubTabSimpleSearch', 'initStandbyStore', {formId: e.target.id});
						request(this.parameters.selectorURL + "&action=results_search_store", {
							data: form_data,
							method: 'POST',
							handleAs: 'html',
						}).then(lang.hitch(this, function(data) {
							data = JSON.parse(data);
							topic.publish('SubTabSimpleSearch', 'SubTabSimpleSearch', 'printResultsStore', {formId: e.target.id, results: data.results, nb_results: data.nb_results, origin: this.parameters.selectorURL + "&action=results_search",  selectResultTabSearch});
						}));
					}
				}));
				return false;
			}
		})
});