// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabCategoryResults.js,v 1.1 2018-10-08 13:59:39 vtouchard Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dojo/dom-attr',
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
        'dojo/io-query',
        'dojo/request/iframe',
        'dojo/request',
        'apps/pmb/form/SubTabResults',
        ], function(declare, dom, on, lang, xhr, domForm, domAttr, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, ioQuery, iframe, request, SubTabResults){
		return declare([SubTabResults], {
			searchType: null,
			onLoad: function(){
				if(this.searchType == 'hierarchy') {
					var elements = query('[data-type-link="pagination"]', this.containerNode);
					elements.forEach(lang.hitch(this, function(element){
						on(element, 'click', lang.hitch(this, this.changePage, element));
					}));
				} else {
					this.inherited(arguments);
				}
			},
			changePage: function(elementClicked, e){
				e.preventDefault();	
				var link = elementClicked.form;
				if(domAttr.get(elementClicked, 'href').indexOf('select.php') != -1){
					var link = domAttr.get(elementClicked, 'href').replace('select.php?', 'ajax.php?module=selectors&');
				}
				domAttr.set(elementClicked, 'href', '');
				request(link+"&action=hierarchical_results_search", {
					data: '',
					method: 'GET',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabCategoryResults', 'SubTabCategoryResults', 'printResults', {results: data, origin: link+"&action=hierarchical_results_search&search_type=hierarchy", search_type:'hierarchy'});
				}));
				return false;
			},
			setSearchType: function(searchType){
				this.searchType = searchType;
			}
		})
});