// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabResults.js,v 1.10 2023/12/27 13:57:47 tsamson Exp $


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
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, ioQuery, request){
		return declare([ContentPane], {
			origin: '',
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
			destroy: function(){
				this.inherited(arguments);
			},
			onDownloadEnd: function(){
				this.inherited(arguments);
				if(typeof this.getParent().resizeIframe == "function"){
					this.getParent().resizeIframe();
				} else {
					this.getParent().getParent().resizeIframe();
				}
			},
			setContent:function(){
				this.inherited(arguments);
				if(typeof this.getParent().resizeIframe == "function"){
					this.getParent().resizeIframe();
				} else {
					this.getParent().getParent().resizeIframe();
				}
			},
			onLoad: function(){
				if(query('input[type="button"]', this.containerNode).length){
					domConstruct.destroy(query('input[type="button"]', this.containerNode)[0]);
				}
				collapseAll(this.containerNode);
				if(query('form[name^="search_form_"]', this.containerNode).length){
					var searchForm = query('form[name^="search_form_"]', this.containerNode)[0];
				}else{
					var searchForm = query('form[name="store_search"]', this.containerNode)[0];
				}
				if(searchForm){
					domAttr.set(searchForm, 'action', this.origin);
					searchForm.submit = lang.hitch(this, this.changePage, searchForm);	
				}
				this.linkChanger();
//				on(searchForm, 'submit', lang.hitch(this, this.changePage, searchForm));
				this.extraTreatment();
			},
			changePage: function(searchForm){
//				e.preventDefault();
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
			linkChanger: function(){
				var noticeParents = query('div[class="notice-parent"]', this.containerNode);
				var noticeChilds = query('div[class="notice-child"]', this.containerNode);
				
				noticeParents.forEach(lang.hitch(this, function(parentDiv){
					var links = query('a[href]', parentDiv);
					links.forEach(lang.hitch(this, function(link){
						if(domAttr.get(link, 'target')){
							domAttr.remove(link, 'target');
						}
						if(domAttr.get(link, 'href') && (domAttr.get(link, 'href') != '#')){
							domAttr.set(link, 'href', '#');
						}
					}));
				}));
				
				noticeChilds.forEach(lang.hitch(this, function(childDiv){
					var links = query('a[href]', childDiv);
					links.forEach(lang.hitch(this, function(link){
						if(domAttr.get(link, 'href') != '#'){
							domAttr.set(link, 'target', '_blank');
						}
					}));
				}));
			},
			extraTreatment: function(){
				//if((typeof this.parameters.queryParameters.tab != "undefined") && (this.parameters.queryParameters.tab == "frbr")){
					//Link remapping
					var results = query('a[onclick][data-element-id]', this.domNode);
					results.forEach(lang.hitch(this, function(a){
						on(a, 'click', lang.hitch(this, function(a, e){
							e.preventDefault();
							e.stopPropagation();
							var propName = (domAttr.get(a, 'data-element-type') == "authorities" ? 'id_authority' : 'id');
							var params = this.parameters.queryParameters.params ? JSON.parse(this.parameters.queryParameters.params) : {};
							if (this.parameters.tabId) {
								this.tabId = this.parameters.tabId;
							}
							topic.publish('SubTabResults', 'eltClicked', 
								{
									[propName]: domAttr.get(a, 'data-element-id'), 
									type: domAttr.get(a, 'data-element-type'),
									params,
									tabId: (this.tabId ? this.tabId : ''),
								}
							);
						}, a));
						//domAttr.set(a, 'onclick', '');
					}));
				//}
			},
		})
});