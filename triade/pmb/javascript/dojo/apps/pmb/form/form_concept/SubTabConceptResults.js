// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabConceptResults.js,v 1.8 2017/11/22 14:58:37 vtouchard Exp $


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
				this.getParent().resizeIframe();
			},
			setContent:function(){
				this.inherited(arguments);
				this.getParent().resizeIframe();
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
				if(this.currentType != 'concepts'){
					var elements = query('a[href="#"][onclick^="set_parent("]', this.containerNode);
					elements.forEach(lang.hitch(this, function(element){
						domAttr.set(element, 'onclick', '');
						on(element, 'click', lang.hitch(this, this.getComposedConcept, element));
					}));
				}else{ //Cas d'une liste de concept composé, il faut gérer la pagination
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
					this.set('content', data);
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
			getComposedConcept: function(element, e){
				e.preventDefault();
				var elementId = domAttr.get(element, 'data-element-id');
				//this.getParent().initLoading() // démarrage de la patience
				this.callComposedConcept(elementId);
				return false;
			},
			callComposedConcept: function(elementId){
				topic.publish('SubTabConceptResults', 'SubTabConceptResults', 'initStandby');
				request(this.parameters.currentURL+'&action=get_composed_concept', {
					method: 'POST',
					data: {element_id: elementId},
					handleAs: 'html'
				}).then(function(response){
					topic.publish('SubTabConceptResults', 'SubTabConceptResults', 'printConcept', {html:response});
				});
			},
			onClose: function(){
				topic.publish('SubTabConceptResults', 'SubTabConceptResults', 'destroyConceptResults');
			}
		})
});