// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabConceptAdvancedSearch.js,v 1.2 2018/10/12 10:16:18 tsamson Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
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
        'dijit/registry',
        'dojo/request',
        'apps/pmb/form/SubTabAdvancedSearch'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, registry, request, SubTabAdvancedSearch){
		return declare([SubTabAdvancedSearch], {
			dijitTree: null,
			button: null,
			dblClickSet: null,
			postForm: function(e){
				e.preventDefault();
				topic.publish('SubTabConceptAdvancedSearch', 'SubTabConceptAdvancedSearch', 'initStandby');
				request(this.parameters.selectorURL+"&action=results_search&mode="+this.parameters.multicriteriaMode, {
					data: domForm.toObject(this.button.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabConceptAdvancedSearch', 'SubTabConceptAdvancedSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=results_search&mode="+this.parameters.multicriteriaMode});
				}));
				return false;
			},
		})
});