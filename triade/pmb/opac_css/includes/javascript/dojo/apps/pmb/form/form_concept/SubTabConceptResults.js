// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabConceptResults.js,v 1.4 2021/01/14 16:30:12 tsamson Exp $


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
        'apps/pmb/form/SubTabResults',
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, ioQuery, iframe, request, SubTabResults){
		return declare([SubTabResults], {
			origin: '',
			currentType: '',
			constructor: function() {
//				console.log('SubtabconceptResultLoadeeeeeeeeeeed');
//				this.own(topic.subscribe('SubTabConceptAdd', lang.hitch(this, this.handleEvents))); 
			},
			handleEvents: function(evtClass, evtType, evtArgs){
//				switch(evtClass){
//					case 'SubTabConceptAdd':
//						switch(evtType){
//							case 'elementAdded':
//								this.getConceptFromAdd(evtArgs);
//								break;
//						}
//						break;
//						
//				}
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
				}			},
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
					on(searchForm, 'submit', lang.hitch(this, this.changePage, searchForm));
				}
				
				//Ici ce n'est pas un formulaire qui est posté, mais des href posés directement sur les numéros de page
				if(query('div[id="navbar_container"]', this.containerNode).length){
					var linkOverride = query('a[href]', query('div[id="navbar_container"]', this.containerNode)[0]);
					if(linkOverride.length){
						linkOverride.forEach(lang.hitch(this, function(link){
							on(link, 'click', lang.hitch(this, function(link,e){
								e.preventDefault();
								this.set('href', link.href);
								return false;
							}, link));
						}));
					}
					//Suppression du paramétrage de nombre résultat par page.
					var resultParameterBox = query('div[id="result_per_page"]', this.containerNode);
					if(resultParameterBox.length){
						domConstruct.destroy(resultParameterBox[0]);
					}	
				}
				
				this.linkChanger();
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
			setSearchType: function(currentType){
				this.currentType = currentType;
//				console.log('currenttype set to', this.currentType);
			},
			onClose: function(){
				topic.publish('SubTabConceptResults', 'SubTabConceptResults', 'destroyConceptResults');
			}
		})
});