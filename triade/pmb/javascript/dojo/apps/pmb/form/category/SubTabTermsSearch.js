// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabTermsSearch.js,v 1.7 2023/09/06 09:16:24 dgoron Exp $


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
				
				var selectorThesaurus = query('select[id="id_thes_term"]', this.containerNode);
				//mode multi-thesaurus
				if(selectorThesaurus.length == 1) {
					domAttr.set(selectorThesaurus[0], 'onchange', '');
				}
				this.getParent().resizeIframe();
			},
			destroy: function(){
				this.inherited(arguments);
			},
			postForm: function(e){
				e.preventDefault();
				request(this.parameters.selectorURL+"&action=terms_results_search", {
					data: domForm.toObject(this.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabTermsSearch', 'SubTabTermsSearch', 'printResults', {results: data, origin: this.parameters.selectorURL + "&action=terms_results_search&search_type=term", search_type:'term'});
					this.connectLinks();
				}));
				return false;
			},
			connectLinks: function() {
				let searchLinks = query('a[data-name="term_show"]', this.ownerDocumentBody);
				if (searchLinks.length) {
					//Liens détéctés, application d'un evenement pour la publication des résultats
					searchLinks.forEach(lang.hitch(this, function(searchLink) {
						on(searchLink, 'click', lang.hitch(this, this.searchLinkClicked, searchLink));
					}));
				}
				let paginationLinks = query('a[data-name="term_search"]', this.ownerDocumentBody);
				if (paginationLinks.length) {
					//Liens détéctés, application d'un evenement pour la publication des résultats
					paginationLinks.forEach(lang.hitch(this, function(paginationLink) {
						on(paginationLink, 'click', lang.hitch(this, this.paginationLinkClicked, paginationLink));
					}));
				}
			},
			searchLinkClicked: function(searchLink, e) {
				if(searchLink.getAttribute('data-term-label')) {
					var term = searchLink.getAttribute('data-term-label');
				} else {
					var term = searchLink.text;
				}
				if(searchLink.getAttribute('data-term-thes')) {
					var id_thes = parseInt(searchLink.getAttribute('data-term-thes'));
				} else {
					var id_thes = -1;
				}
				e.preventDefault();
				request(this.parameters.selectorURL + "&action=terms_show_notice&term=" + encodeURIComponent(term) + "&id_thes=" + id_thes, {
					data: '',
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data) {
					topic.publish('SubTabTermsSearch', 'SubTabTermsSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=terms_results_search"});
					this.connectLinks();
				}));
				return false;
			},
			paginationLinkClicked: function(paginationLink, e) {
				e.preventDefault();
				if(domAttr.get(paginationLink, 'data-nbresultterme')) {
					var nbresultterme = domAttr.get(paginationLink, 'data-nbresultterme');
				} else {
					var nbresultterme = 0;
				}
				if(domAttr.get(paginationLink, 'data-page')) {
					var page = domAttr.get(paginationLink, 'data-page');
				} else {
					var page = 1;
				}
				request(this.parameters.selectorURL+"&action=terms_results_search&nbresultterme="+nbresultterme+"&page="+page, {
					data: domForm.toObject(this.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabTermsSearch', 'SubTabTermsSearch', 'printResults', {results: data, origin: this.parameters.selectorURL + "&action=terms_results_search&search_type=term", search_type:'term'});
					this.connectLinks();
				}));
				return false;
			}
		})
});