// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabSearchConcept.js,v 1.8.16.1 2025/02/14 10:47:58 dgoron Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'apps/pmb/form/FormSelector',
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
        'dojo/io-query',
        ], function(declare, dom, on, FormSelector, lang, request, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, domForm, ioQuery){
		return declare([ContentPane], {
			currentMode: null,
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
			},
			onLoad: function(){
				
			},
			onDownloadEnd: function(){
				var searchButton = query('input[type="submit"]', this.containerNode)[0];
				var searchTabs = query('a[href^="autorites.php?categ=search&mode="]', this.containerNode);
				
				searchTabs.forEach(lang.hitch(this, this.applySearchTabEvent));
				
				var h1 = query('h1', this.containerNode);
				if(h1.length){
					domConstruct.destroy(h1[0]);
				}
				domConstruct.destroy(searchButton.form);

			},
			destroy: function(){
				this.inherited(arguments);
			},
			postForm: function(e){
				e.preventDefault();
				
				var postData = domForm.toObject(this.form);
				if(postData.action){
					delete postData.action;
				}
				
				var currentQuery = ioQuery.queryToObject(this.parameters.selectorURL.substring(this.parameters.selectorURL.indexOf("?") + 1, this.parameters.selectorURL.length));
				currentQuery.mode = this.getCurrentMode();
				currentQuery.action = 'results_search';
				
//				console.log('trying to post form to url', this.parameters.selectorURL.split('?')[0]+'?'+ioQuery.objectToQuery(currentQuery));
				request(this.parameters.selectorURL.split('?')[0]+'?'+ioQuery.objectToQuery(currentQuery), {
					data: postData,
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabSearchConcept', 'SubTabSearchConcept', 'printResults', {results: data, currentType: this.getCurrentType(), origin: this.parameters.selectorURL+"&action=results_search"});
				}));
				return false;
			},
			onShow: function(){
//				alert('coucou');
//				this.inherited(arguments);
				
			},
			applySearchTabEvent: function(tab){
				on(tab, 'click', lang.hitch(this, function(tab, e){
					e.preventDefault();
					
				  	var currentHref = this.get('href');
				  	var currentQuery = ioQuery.queryToObject(currentHref.substring(currentHref.indexOf("?") + 1, currentHref.length));
				  	if(currentQuery.entity_type){
				  		delete currentQuery.entity_type;
				  	}
				  	var tabHref = domAttr.get(tab, 'href');
				  	var tabQuery = ioQuery.queryToObject(tabHref.substring(tabHref.indexOf("?") + 1, tabHref.length));
				  	
				  	currentQuery.mode = tabQuery.mode;
				  	var entityType = domAttr.get(tab.parentNode, 'data-pmb-object-type');
				  	var newHref = this.get('href').split('?')[0] + '?' + ioQuery.objectToQuery(currentQuery)+'&entity_type='+entityType;
				  	if (entityType == 'authperso') newHref += "&authperso_id=" + currentQuery.mode.slice(3);
				  	this.currentMode = currentQuery.mode;
				  	var widgets = registry.findWidgets(this.containerNode);
				  	widgets.forEach(function(widget){
				  		widget.destroyRecursive();
				  	});
				  	this.set('href', newHref);
					
					return false;
				}, tab));
			},
			getCurrentMode: function(){
				var selectedTab = query('span[class="selected"]', this.containerNode);
				if(selectedTab.length){
				  	return domAttr.get(selectedTab[0], 'data-pmb-mode');
				}
				return '';
			},
			getCurrentType: function(){
				var selectedTab = query('span[class="selected"]', this.containerNode);
				if(selectedTab.length){
					return domAttr.get(selectedTab[0], 'data-pmb-object-type');
				}
				return 'concepts';
			},
			getRandomNumber:function(){
				return ''+Math.random()+''.replace('.', '');
			},
			resizeIframe: function(){
				if(window.parent.location.href != window.location.href){
				    window.frameElement.style.height = window.frameElement.contentWindow.document.body.scrollHeight+50+'px';
				}
				this.resize();
			},
		})
});