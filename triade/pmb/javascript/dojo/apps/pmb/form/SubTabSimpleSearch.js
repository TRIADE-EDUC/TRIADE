// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabSimpleSearch.js,v 1.6 2018/12/17 23:09:30 ccraig Exp $


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
			},
			onLoad: function(){
				this.searchButton = query('input[id="launch_search_button"]', this.containerNode)[0];
				this.form = this.searchButton.form;
				var formData = domForm.toObject(this.form);

				if (window.parent.directSearch) {
					var debRech = '*';
					this.parameters.selectorURL.split('?')[1].split('&').forEach((param) => {
						var item = param.split('=');
						if (item[0] == 'deb_rech' && item[1] != '*') debRech = decodeURIComponent(item[1]) + '*';
					});
					formData['search_field_tab_1[]'] = debRech;
					topic.publish('SubTabSimpleSearch', 'SubTabSimpleSearch', 'initStandby');
					request(this.parameters.selectorURL+"&action=results_search", {
						data: formData,
						method: 'POST',
						handleAs: 'html',
					}).then(lang.hitch(this, function(data){
						topic.publish('SubTabSimpleSearch', 'SubTabSimpleSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=results_search"});
					}));
				} else {
					var debRech = '';
					this.parameters.selectorURL.split('?')[1].split('&').forEach((param) => {
						var item = param.split('=');
						if (item[0] == 'deb_rech') debRech = decodeURIComponent(item[1]);
					});
					document.getElementById('search_field_tab_1').value = debRech;
				}
				this.resizeTimeout = setInterval(lang.hitch(this, this.checkSize), 200);
				return false;
			},
			onDownloadEnd: function(){
				
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