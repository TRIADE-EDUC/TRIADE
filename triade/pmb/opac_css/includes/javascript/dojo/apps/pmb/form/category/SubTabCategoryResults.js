// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabCategoryResults.js,v 1.2 2020/09/15 08:19:59 dgoron Exp $


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
			
			setSearchType: function(searchType){
				this.searchType = searchType;
			}
		})
});