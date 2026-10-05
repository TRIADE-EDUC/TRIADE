// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabTermsSearch.js,v 1.2 2020/07/22 13:17:20 jlaurent Exp $


define([
        'dojo/_base/declare',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request',
        'dojo/dom-form',
        'dojo/dom-attr',
        'dojox/layout/ContentPane',
        'dojo/query',
        'dojo/topic',
        ], function(declare, on, lang, request, domForm, domAttr, ContentPane, query, topic){
		return declare([ContentPane], {
			
			constructor: function() {
				
			},
			postCreate: function() {
				this.inherited(arguments);
			},
			onLoad: function(){
				
			},
			onDownloadEnd: function(){
				var searchButton = query('input[id="launch_terms_search_button"]', this.containerNode)[0];
				this.form = searchButton.form;			
				
				on(this.form, 'submit', lang.hitch(this, this.postForm));
				
				if(typeof this.getParent().resizeIframe == "function"){
					this.getParent().resizeIframe();
				} else {
					this.getParent().getParent().resizeIframe();
				}
			},
			destroy: function(){
				this.inherited(arguments);
			},
			postForm: function(e){
				e.preventDefault();
				request(this.parameters.selectorURL+"&action=terms_search", {
					data: domForm.toObject(this.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					this.set('content', data);
					this.connectLinks();
				}));
				return false;
			},
			connectLinks: function() {
				var searchLinks = query('a[target="term_show"]', this.containerNode);
				if(searchLinks.length){
					  //Liens détéctés, application d'un evenement pour la publication des résultats
					searchLinks.forEach(lang.hitch(this, function(searchLink){
						  on(searchLink, 'click', lang.hitch(this, this.searchLinkClicked, searchLink));
					  }));
					  return true;
				  }
			},
			searchLinkClicked: function(searchLink, e){
				e.preventDefault();
				request(this.parameters.selectorURL+"&action=terms_results_search&"+domAttr.get(searchLink, 'data-evt-args'), {
					data: '',
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabTermsSearch', 'SubTabTermsSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=terms_results_search"});
				}));
				return false;
			} 	
		})
});