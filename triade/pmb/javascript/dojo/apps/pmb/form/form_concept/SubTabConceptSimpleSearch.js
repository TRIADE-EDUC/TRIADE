// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabConceptSimpleSearch.js,v 1.5 2018/12/28 15:27:10 ngantier Exp $


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

			},
			onLoad: function(){
				this.searchButton = query('input[id="launch_search_button"]', this.containerNode)[0];
				this.form = this.searchButton.form;
				if (window.parent.directSearch) {
					var debRech = '*';
					this.parameters.selectorURL.split('?')[1].split('&').forEach((param) => {
						var item = param.split('=');
						if (item[0] == 'deb_rech') debRech = decodeURIComponent(item[1]);
					});
					if(!debRech) debRech = '*';
					var formData = domForm.toObject(this.form);
					formData['search_field_tab_1[]'] = debRech;
					topic.publish('SubTabConceptSimpleSearch', 'SubTabConceptSimpleSearch', 'initStandby');
					request(this.parameters.selectorURL+"&action=results_search", {
						data: formData,
						method: 'POST',
						handleAs: 'html',
					}).then(lang.hitch(this, function(data){
						topic.publish('SubTabConceptSimpleSearch', 'SubTabConceptSimpleSearch', 'printResults', {results: data, currentType: this.getParent().getParent().getCurrentType(), origin: this.parameters.selectorURL+"&action=results_search"});
					}));
				} else {
					var debRech = '*';
					this.parameters.selectorURL.split('?')[1].split('&').forEach((param) => {
						var item = param.split('=');
						if (item[0] == 'deb_rech') debRech = decodeURIComponent(item[1]);
					});
					document.getElementById('search_field_tab_1').value = debRech;
				}
				this.resizeTimeout = setInterval(lang.hitch(this, this.checkSize), 200);
				return false;
			},
			postForm: function(e){
				e.preventDefault()
				topic.publish('SubTabConceptSimpleSearch', 'SubTabConceptSimpleSearch', 'initStandby');
				request(this.parameters.selectorURL+"&action=results_search", {
					data: domForm.toObject(this.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabConceptSimpleSearch', 'SubTabConceptSimpleSearch', 'printResults', {results: data, currentType: this.getParent().getParent().getCurrentType(), origin: this.parameters.selectorURL+"&action=results_search"});
				}));
				return false;
			},
			onShow: function(){
//				this.inherited(arguments);	
			}
		})
});