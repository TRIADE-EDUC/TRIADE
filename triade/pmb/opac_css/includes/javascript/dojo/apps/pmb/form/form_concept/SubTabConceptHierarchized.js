// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabConceptHierarchized.js,v 1.2.10.1 2025/02/14 10:47:58 dgoron Exp $


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
        'dojo/request/iframe',
        'dojo/request',
        'dojox/widget/Standby'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, ioQuery, iframe, request, Standby){
		return declare([ContentPane], {
			origin: '',
			currentType: '',
			currentURL: '',
			backURL: '',
			standby: null,
			constructor: function() {
			},
			postCreate: function() {
				this.inherited(arguments);
			},
			destroy: function(){
				this.inherited(arguments);
			},
			onDownloadEnd: function(){
				this.inherited(arguments);
				this.resizeIframe();
			},
			setContent:function(){
				this.inherited(arguments);
				this.resizeIframe();
			},
			onLoad: function(){
				if(this.href){
					this.backURL = this.href;
				}
				var resultTable = query('table', this.containerNode)[1];
				
				domConstruct.destroy(query('div.hmenu', this.containerNode)[0]);
				
				query('tr', resultTable).forEach(function(tr){
					domConstruct.destroy(tr.firstElementChild);
					domConstruct.destroy(tr.lastElementChild);
				});
				
				query('div.skos_concept_search_form_breadcrumb a, a.skos_concept_list_line_folder_icon, a[data-type-link="pagination"], a#skos_concept_search_form_last_concepts_link', this.containerNode).forEach(lang.hitch(this, function(folder) {
					on(folder, 'click', lang.hitch(this, function(e){
						e.preventDefault();
						var href = domAttr.get(folder, 'href');
						var args = href.split('&action=')[1];
						this.set('href', this.backURL.split('&action=')[0]+'&action='+args);
						return false;
					}));
				}));
				
				query('input[type="button"]', this.containerNode).forEach(function(node) {
					domConstruct.destroy(node);
				});
				
				var searchForm = query('form[name="search"]', this.containerNode)[0];
				on(searchForm, 'submit', lang.hitch(this, function(e){
					var args = domAttr.get(searchForm, 'action').split('&action=')[1];
					var newAction = this.backURL.split('&action=')[0]+'&action='+args;
					domAttr.set(searchForm, 'action', newAction);
					e.preventDefault();
					this.initStandby();
					request(domAttr.get(searchForm, 'action'), {
						data: domForm.toObject(searchForm),
						method: 'POST',
						handleAs: 'html',
					}).then(lang.hitch(this, function(data){
						this.set('content', data);
						this.shutStandby();
						setTimeout(lang.hitch(this, this.resizeIframe), 100);
					}));
					return false;
				}));
				
				this.resizeIframe();
			},
			changePage: function(searchForm){
				var data = domForm.toObject(searchForm);
				if(data.action){
					delete data.action;
				}
				var previousOrigin = domAttr.get(searchForm, 'action');
				var queryObject = ioQuery.queryToObject(previousOrigin.substring(previousOrigin.indexOf('?')+1, previousOrigin.length));
				if(queryObject.mode && !data.mode){
					data.mode = queryObject.mode;
				}
				request(domAttr.get(searchForm, 'action'), {
					data: data,
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					var content = "";
					try{
						//on teste s'il s'agit d'un json ou non
						data = JSON.parse(data);
						content = data.results;
					} catch(e) {
						content = data;
					}
					this.set('content', content);
				}));
				return false;
			},
			setOrigin: function(url){
				this.origin = url;
			},
			resizeIframe: function(){
				if(window.parent.location.href != window.location.href){
				    window.frameElement.style.height = window.frameElement.contentWindow.document.body.scrollHeight+'px';
				}
				this.resize();
			},
			initStandby: function(){
				if(!this.standby){
					this.standby = new Standby({
						target: this.domNode
					});
					document.body.appendChild(this.standby.domNode);
					this.standby.startup();
				}
				this.standby.show();
			},
			shutStandby: function(){
				if(this.standby){
					this.standby.hide();
				}
			},
		})
});