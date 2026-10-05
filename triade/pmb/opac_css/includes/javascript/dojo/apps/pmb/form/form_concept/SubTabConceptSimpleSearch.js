// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabConceptSimpleSearch.js,v 1.2 2018/10/12 10:16:18 tsamson Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request',
        'dojo/dom-form',
        'dijit/layout/TabContainer',
        'dojox/layout/ContentPane',
        'dojo/query',
        'dojo/ready',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dojo/dom-form',
        'apps/pmb/form/SubTabSimpleSearch'
        ], function(declare, dom, on, lang, request, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, domForm, SubTabSimpleSearch){
		return declare([SubTabSimpleSearch], {
			constructor: function(){
//				console.log('subtablesimplesearch loaded');
			},
			postForm: function(e){
				e.preventDefault()
				topic.publish('SubTabConceptSimpleSearch', 'SubTabConceptSimpleSearch', 'initStandby');
				request(this.parameters.selectorURL+"&action=results_search", {
					data: domForm.toObject(this.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabConceptSimpleSearch', 'SubTabConceptSimpleSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=results_search"});
				}));
				return false;
			},
			onShow: function(){
//				this.inherited(arguments);
				
			}
		})
});