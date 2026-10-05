// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabComposedConceptResults.js,v 1.3 2021/01/14 16:30:12 tsamson Exp $


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
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, ioQuery, iframe, request){
		return declare([ContentPane], {
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
				if(this.currentType != 'concepts'){
					var elements = query('a[href="#"][onclick^="set_parent("]', this.containerNode);
					elements.forEach(lang.hitch(this, function(element){
						domAttr.set(element, 'onclick', '');
						on(element, 'click', lang.hitch(this, this.getComposedConcept, element));
					}));
				}
//				on(searchForm, 'submit', lang.hitch(this, this.changePage, searchForm));
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
			},
			getComposedConcept: function(element, e){
				e.preventDefault();
				var elementId = domAttr.get(element, 'data-element-id');
				//this.getParent().initLoading() // démarrage de la patience
				this.callComposedConcept(elementId);
				return false;
			},
			callComposedConcept: function(elementId){
				request(this.parameters.currentURL+'&action=get_composed_concept', {
					method: 'POST',
					data: {element_id: elementId},
					handleAs: 'html'
				}).then(function(response){
					topic.publish('SubTabConceptResults', 'SubTabConceptResults', 'printConcept', {html:response});
				});
			},
			onHide: function(){
				alert('lsdkslkd');
			}
		})
});