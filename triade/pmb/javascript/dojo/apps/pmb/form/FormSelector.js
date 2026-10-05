// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormSelector.js,v 1.23.8.1 2025/02/14 10:47:58 dgoron Exp $

/*****
 * 
 * C'est cette classe qui aura la lourde responsabilite de mettree en place
 * l'ensemble des onnnnnglet permettant de representer un selecteur
 * 
 * 
 * *Cette classe devra pouvoir être utilisée dans les selecteur comme dans le module
 * de gestion des formulaire. prévoir l'utilisation d'un mod permettant de définir
 * le contexte dans lequel nous nous trouvons 
 * 
 * 
 * 
 */

define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dijit/layout/TabContainer',
        'dijit/layout/ContentPane',
        'dojo/query',
        'dojo/ready',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dijit/layout/LayoutContainer',
        'apps/pmb/form/FormTab',
        'dojox/layout/ContentPane',
        'apps/pmb/form/SubTabAdd',
        'apps/pmb/form/SubTabAdvancedSearch',
        'apps/pmb/form/SubTabSimpleSearch',
        'apps/pmb/form/SubTabResults',
        'apps/pmb/form/form_concept/SubTabConceptResults',
        'dojo/request',
        'dojo/io-query',
        'dojox/widget/Standby'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, 
        		geometry, domConstruct, domStyle, LayoutContainer, FormTab, ContentPaneDojox, SubTabAdd,
        		SubTabAdvancedSearch, SubTabSimpleSearch, SubTabResults, SubTabConceptResults, request, ioQuery, Standby){
		return declare([TabContainer], {
			simpleSearchTab: null,   //Onglet rech simple
			extendedSearchTab: null, //Onglet rech multicritere
			resultTab: null,		 //Onglet affichage des résultats de recherche
			newTab: null,
			entity: '',
			standby: null,
			parametersTabs:null,
			constructor: function(parameters) {
				this.parameters = parameters;
				/**
				 * Veillez ici à transmettre un nom unique facilement récupérable 
				 */
				//Des parametres sont fournis à l'url du selecteur
				if(this.parameters.selectorURL.indexOf('?')!==-1){
					this.parameters.queryParameters = ioQuery.queryToObject(this.parameters.selectorURL.split('?')[1]); 
				}
				this.initEvents();
			},
			handleEvents: function(evtClass, evtType, evtArgs){
				switch(evtClass){
					case 'SubTabAdvancedSearch':
						switch(evtType){
							case 'printResults':
					  			this.printResults(evtArgs);
					  			this.shutStandby();
					  			break;
							case 'initStandby':
					  			this.initStandby();
					  			break;
							case 'shutStandby':
					  			this.shutStandby();
					  			break;
						}
						break;
				  	case 'SubTabSimpleSearch':
				  		switch(evtType){
							case 'printResults':
					  			this.printResults(evtArgs);
					  			this.shutStandby();
					  			break;
							case 'initStandby':
					  			this.initStandby();
					  			break;
				  		}
				  		break;
				  	case 'SubTabAdd':
						switch(evtType){
							case 'elementAdded':
								this.elementAdded(evtArgs);
								break;
						}
						break;
				  	case 'tablist':
				  		switch(evtType){
				  			case 'expand':
				  				this.resizeIframe();
				  				break;
				  		}
				  		break;
				  	case 'SortIframe':
				  		switch(evtType){
				  			case 'reloadResult':
					  			this.reloadResults();
				  				break;
				  		}
				  		break;
				  }
			},
			postCreate: function() {
				this.inherited(arguments);
				this.initParametersTabs();
				this.createTabs();
			},
			createTabs: function(){
				/**
				 * Ici doit être récupérée l'url du selecteur
				 */
				
				//on teste si on ne provient pas d'un schema de catalogage
				if (!this.parameters.queryParameters.cataloging_scheme_id) {
					if(this.isVisibleTab('simple_search')) {
				  		this.simpleSearchTab = new SubTabSimpleSearch({title: pmbDojo.messages.getMessage('selector', 'selector_tab_simple_search'), style: 'width:95%; height:100%;', parameters: this.parameters});
						this.simpleSearchTab.href = this.parameters.selectorURL+'&action=simple_search';
						if(this.isDefaultSelectedTab('simple_search')) {
							this.simpleSearchTab.selected = true;
						}
						this.addChild(this.simpleSearchTab);
	
						this.simpleSearchTab.resize();
						this.simpleSearchTab.startup();
					}
					
					if(this.isVisibleTab('advanced_search')) {
						this.extendedSearchTab = new SubTabAdvancedSearch({title: pmbDojo.messages.getMessage('selector', 'selector_tab_advanced_search'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
						this.extendedSearchTab.href = this.parameters.selectorURL+'&action=advanced_search&mode='+this.parameters.multicriteriaMode;
						if(this.isDefaultSelectedTab('advanced_search')) {
							this.extendedSearchTab.selected = true;
						}
						this.addChild(this.extendedSearchTab);
	
						//Cela fait ralentir le chargement des formulaires
//						this.extendedSearchTab.startup();
//						this.extendedSearchTab.resize();
					}
				}
				
				if (this.isVisibleTab('add') && this.parameters.bt_ajouter != 'no') {
					if(!this.newTab) {
						this.newTab = new SubTabAdd({title: pmbDojo.messages.getMessage('selector', 'selector_tab_add'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
						this.newTab.href = this.parameters.selectorURL+'&action=add&form_display_mode=2';
						if(this.isDefaultSelectedTab('add')) {
							this.newTab.selected = true;
						}
					}
					this.addChild(this.newTab);	
					
					//Cela fait ralentir le chargement des formulaires
					
//					this.newTab.startup();
//					this.newTab.resize();
				}
				
				this.startup();
				this.resize();

			},
			printResults: function(evtData, autoSelect){
				if(!this.resultTab){
					if(!this.parameters.isOntology){
						this.resultTab = new SubTabResults({title: pmbDojo.messages.getMessage('selector', 'selector_tab_results'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});	
					}else{
						this.resultTab = new SubTabConceptResults({title: pmbDojo.messages.getMessage('selector', 'selector_tab_results'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
					}
					
					this.addChild(this.resultTab);
				}
				this.resultTab.setOrigin(evtData.origin);
				this.resultTab.set('content', evtData.results);
				
				this.resultTab.startup();
				this.resultTab.resize();
				
				this.selectChild((this.getChildren()[this.getChildren().length-1]), true);
				if(autoSelect){
					query('a[onclick^="set_parent("]', this.selectedChildWidget.domNode)[0].click();
				}
			},
			elementAdded: function(evtData){
				request(this.parameters.selectorURL+"&action=element_display", {
					data: evtData,
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					this.printResults({results: data}, true);
				}));
			},
			selectChild : function(page,animate) {
				this.inherited(arguments);
				this.resizeIframe();
			},
			resizeIframe: function(){
				if(window.frameElement){
					window.frameElement.style.height = window.frameElement.contentWindow.document.body.scrollHeight+50+'px';
				}
				this.resize();
			},
			initEvents: function(){
				this.own(
						topic.subscribe('SubTabSimpleSearch', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SubTabAdvancedSearch', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SubTabAdd', lang.hitch(this, this.handleEvents)),
						topic.subscribe('expandBase', lang.hitch(this, this.handleEvents)),
						topic.subscribe('tablist', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SortIframe', lang.hitch(this, this.handleEvents))
				);
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
			
			reloadResults: function(){
				var form = query("form", this.resultTab.containerNode);
				if (form[0]) {
					this.initStandby();
					var formData = new FormData(form[0]);
					formData.delete('action');
					xhr.post(domAttr.get(form[0], 'action'), {
						data: formData
					}).then(lang.hitch(this, this.setContentResult));
				}
			},
			
			setContentResult: function(content) {
				if (this.resultTab) {
					this.resultTab.set("content", content);
				}
				this.shutStandby();
			},
			
			initParametersTabs: function() {
				if(this.parametersTabs && typeof this.parametersTabs !== 'object') {
					this.parametersTabs = JSON.parse(this.parametersTabs);
				}
			},
			
			isVisibleTab: function(name) {
				if(!this.parametersTabs) {
					return true;
				}
				if(this.parametersTabs[name] && !parseInt(this.parametersTabs[name].visible_gestion)) {
					return false;
				}
				return true;
			},
			
			isDefaultSelectedTab: function(name) {
				if(!this.parametersTabs) {
					return true;
				}
				if(this.parametersTabs[name] && parseInt(this.parametersTabs[name].default_selected_gestion)) {
					return true;
				}
				return false;
			},
		})
});