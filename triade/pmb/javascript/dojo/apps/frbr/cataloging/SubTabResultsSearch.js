// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabResultsSearch.js,v 1.2 2018/02/21 10:00:58 vtouchard Exp $


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
        'dojo/io-query',
        'dojo/request',
        'apps/pmb/form/SubTabResults'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, ioQuery, request, SubTabResults){
		return declare([SubTabResults], {
			origin: '',
			constructor: function() {
			},
			handleEvents: function(evtType,evtArgs){
				switch(evtType){
					case 'savedForm':
						break;
						
				}
			},
			onDownloadEnd: function(){
				this.inherited(arguments);
				this.getParent().resizeIframe();
			},
			setContent:function(){
				this.inherited(arguments);
				this.getParent().resizeIframe();
			},
			extraTreatment: function(){
				//Link remapping
				
				var results = query('a[data-element-id]', this.containerNode);
				results.forEach(lang.hitch(this, function(a){
					on(a, 'click', lang.hitch(this, function(a){
						var propName = (domAttr.get(a, 'data-element-type') == "authorities" ? 'id_authority' : 'id');
						topic.publish('SubTabResultsSearch', 'eltClicked', 
							{
								[propName]: domAttr.get(a, 'data-element-id'), 
								type: domAttr.get(a, 'data-element-type'),
							}
						);	
					}, a));
					domAttr.set(a, 'onclick', '');
				}));
			}
		})
});