// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabAdvancedSearch.js,v 1.5 2021/11/17 13:37:00 gneveu Exp $


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
        'dijit/registry',
        'dojo/request',
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, registry, request){
		return declare([ContentPane], {
			dijitTree: null,
			button: null,
			dblClickSet: null,
			constructor: function() {
				this.own(topic.subscribe('AdvancedSearchTree', lang.hitch(this, this.handleEvents)));
			},
			handleEvents: function(evtClass, evtType,evtArgs){
				switch(evtClass){
					case 'AdvancedSearchTree':
						switch(evtType){
							case 'elementAdded':
								this.manageSearchButton();
								break;
						}
						break;
						
				}
			},
			postCreate: function() {
				this.inherited(arguments);
			},
			destroy: function(){
				this.inherited(arguments);
			},
			setFormActionEvent: function(queryResult){
				domAttr.remove(queryResult, 'href');
				domAttr.set(queryResult, 'style', 'cursor:pointer');
				on(queryResult, 'click', lang.hitch(this, this.postSearchPersoForm, queryResult));
			},
			onDownloadEnd: function(){
				this.dijitTree = registry.byId('searchFieldsTree');
				var dijitTreeDblClickClb = this.dijitTree.onDblClick;
				if(!this.dblClickSet){
					this.dblClickSet = true;
					this.dijitTree.onDblClick = lang.hitch(this.dijitTree, function(){
						lang.hitch(this, dijitTreeDblClickClb, arguments[0], arguments[1], arguments[2])();
//						topic.publish('AdvancedSearchTree', 'AdvancedSearchTree', 'elementAdded', {});
					});
				}
				var querySearchPerso = query('div[id="search_perso"] a', this.containerNode);
			  	if(querySearchPerso.length){
			  		for(var i=0; i < querySearchPerso.length; i++) {
			  			this.setFormActionEvent(querySearchPerso[i]);
			  		}
			  	}
				if(typeof this.getParent().resizeIframe == "function"){
					this.getParent().resizeIframe();
				} else {
					this.getParent().getParent().resizeIframe();
				}
			},
			manageSearchButton: function(){
				domConstruct.destroy(dom.byId('save_predefined_search'));
				
				var elem = document.getElementById('search_form_submit');
				if(elem){
					elem.replaceWith(elem.cloneNode(true));
				}
				
				this.button = dom.byId('search_form_submit');
				
				this.button.form.setAttribute('onsubmit', '');
				on(this.button.form , 'submit', lang.hitch(this, this.postForm));
			},
			postForm: function(e){
				e.preventDefault();
				topic.publish('SubTabAdvancedSearch', 'SubTabAdvancedSearch', 'initStandby');
				
				//Méthode normalement appellée au post du formulaire de multicritère.
				enable_operators(); 
				active_autocomplete();
				
				request(this.parameters.selectorURL+"&action=results_search&mode="+this.parameters.multicriteriaMode, {
					data: domForm.toObject(this.button.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabAdvancedSearch', 'SubTabAdvancedSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=results_search&mode="+this.parameters.multicriteriaMode});
				}));
				return false;
			},
			postSearchPersoForm: function(e){
				topic.publish('SubTabAdvancedSearch', 'SubTabAdvancedSearch', 'initStandby');
				request(this.parameters.selectorURL+"&action=advanced_search&mode="+this.parameters.multicriteriaMode, {
					data: domForm.toObject(document.forms['search_form'+e.getAttribute('data-search-perso-id')]),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					this.set('content', data);
					topic.publish('AdvancedSearchTree', 'AdvancedSearchTree', 'elementAdded', {});
					topic.publish('SubTabAdvancedSearch', 'SubTabAdvancedSearch', 'shutStandby');
				}));
				return false;
			},
		})
});