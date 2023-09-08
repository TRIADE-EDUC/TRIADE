// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabSimpleSearch.js,v 1.1 2018-10-08 13:59:39 vtouchard Exp $


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
        ], function(declare, dom, on, lang, request, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, domForm){
		return declare([ContentPane], {
			resizeTimeout: null,
			currentHeight: null,
			constructor: function() {
				
			},
			handleEvents: function(evtType,evtArgs){
				switch(evtType){
					case 'savedForm':
						break;
						
				}
			},
			postCreate: function() {
				this.inherited(arguments);
				this.resizeTimeout = setInterval(lang.hitch(this, this.checkSize), 200);
			},
			onLoad: function(){
				
			},
			onDownloadEnd: function(){
				var searchButton = query('input[id="launch_search_button"]', this.containerNode)[0];
				searchButton.setAttribute('type', 'submit'); 
				this.form = searchButton.form;
				
				on(this.form, 'submit', lang.hitch(this, this.postForm));				
				this.getParent().resizeIframe();
			},
			checkSize: function(){
				if(this.currentHeight < this.containerNode.clientHeight){				
					this.getParent().resizeIframe();
					this.currentHeight = this.containerNode.clientHeight;
					if(typeof ajax_resize_elements == "function"){
						ajax_resize_elements();
					}
				}
			},
			destroy: function(){
				this.inherited(arguments);
				clearTimeout(this.resizeTimeout);
			},
			postForm: function(e){
				e.preventDefault();
				topic.publish('SubTabSimpleSearch', 'SubTabSimpleSearch', 'initStandby');
				request(this.parameters.selectorURL+"&action=results_search", {
					data: domForm.toObject(this.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabSimpleSearch', 'SubTabSimpleSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=results_search"});
				}));
				return false;
			},
			onShow: function(){
//				this.inherited(arguments);
				
			}
		})
});