// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabHierarchicalSearch.js,v 1.1 2017/10/25 11:43:06 dgoron Exp $


define([
        'dojo/_base/declare',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request',
        'dojo/dom-form',
        'dojox/layout/ContentPane',
        'dojo/query',
        'dojo/topic',
        ], function(declare, on, lang, request, domForm, ContentPane, query, topic){
		return declare([ContentPane], {
			
			constructor: function() {
				
			},
			postCreate: function() {
				this.inherited(arguments);
			},
			onLoad: function(){
				
			},
			onDownloadEnd: function(){
				var searchButton = query('input[id="launch_hierarchical_search_button"]', this.containerNode)[0];
				this.form = searchButton.form;			
				
				on(this.form, 'submit', lang.hitch(this, this.postForm));				
				this.getParent().resizeIframe();
			},
			destroy: function(){
				this.inherited(arguments);
			},
			postForm: function(e){
				e.preventDefault();
				request(this.parameters.selectorURL+"&action=hierarchical_results_search", {
					data: domForm.toObject(this.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabHierarchicalSearch', 'SubTabHierarchicalSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=hierarchical_results_search&search_type=hierarchy", search_type:'hierarchy'});
				}));
				return false;
			},
		})
});