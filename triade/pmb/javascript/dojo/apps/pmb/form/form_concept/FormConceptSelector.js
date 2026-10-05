// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormConceptSelector.js,v 1.19 2021/02/09 11:09:53 qvarin Exp $

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
        'apps/pmb/form/FormSelector',
        'apps/pmb/form/form_concept/SubTabConceptAdd',
        'apps/pmb/form/form_concept/SubTabConceptAdvancedSearch',
        'apps/pmb/form/form_concept/SubTabConceptSimpleSearch',
        'apps/pmb/form/SubTabResults',
        'apps/pmb/form/form_concept/SubTabConceptResults',
        'dojo/request',
        'dojo/io-query',
        'dojox/widget/Standby'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, 
        		geometry, domConstruct, domStyle, LayoutContainer, FormTab, ContentPaneDojox, FormSelector, SubTabConceptAdd,
        		SubTabConceptAdvancedSearch, SubTabConceptSimpleSearch, SubTabResults, SubTabConceptResults, request, ioQuery, Standby){
		return declare([FormSelector], {
			simpleSearchTab: null,   //Onglet rech simple
			extendedSearchTab: null, //Onglet rech multicritere
			resultTab: null,		 //Onglet affichage des résultats de recherche
			resultConceptTab:null,   //Onglet affichage des concept composés utilisant l'entité courante
			newTab: null,
			entity: '',			
			constructor: function(parameters) {
				this.parameters = parameters;
				/**
				 * Veillez ici à transmettre un nom unique facilement récupérable 
				 */
			},
			handleEvents: function(evtClass, evtType, evtArgs){
//				console.log('event found ',evtClass, evtType, evtArgs)
				switch(evtClass){
					case 'SubTabConceptAdvancedSearch':
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
				  	case 'SubTabConceptSimpleSearch':
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
				  	case 'SubTabConceptResults':
						switch(evtType){
							case 'printConcept':
								this.printConcept(evtArgs);
								this.shutStandby();
								break;
							case 'destroyConceptResults':
								if(this.resultConceptTab){
									this.removeChild(this.resultConceptTab);
									this.resultConceptTab.destroyRecursive();
									this.resultConceptTab = null;
								}
								break;
							case 'initStandby':
					  			this.initStandby();
								break;	
						}
						break;
				  	case 'SubTabConceptAdd':
				  		switch(evtType){
				  			case 'elementAdded':
				  				this.getConceptFromAdd(evtArgs);
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
			createTabs: function(){
				/**
				 * Ici doit être récupérée l'url du selecteur
				 */
				
		  		this.simpleSearchTab = new SubTabConceptSimpleSearch({title: pmbDojo.messages.getMessage('selector', 'selector_tab_simple_search'), style: 'width:95%; height:100%;', parameters: this.parameters});
				this.simpleSearchTab.href = this.parameters.selectorURL+'&action=simple_search';
				
				if(!this.parameters.conceptSchemes){
					this.extendedSearchTab = new SubTabConceptAdvancedSearch({title:  pmbDojo.messages.getMessage('selector', 'selector_tab_advanced_search'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
					this.extendedSearchTab.href = this.parameters.selectorURL+'&action=advanced_search';
				}
				this.newTab = new SubTabConceptAdd({title: pmbDojo.messages.getMessage('selector', 'selector_tab_add'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
				this.newTab.href = this.parameters.selectorURL+'&action=add&form_display_mode=2';
			
				this.addChild(this.simpleSearchTab);
				if(!this.parameters.conceptSchemes){
					this.addChild(this.extendedSearchTab);
				}
				this.addChild(this.newTab);	
				
				this.simpleSearchTab.resize();
				this.simpleSearchTab.startup();
				
				if(!this.parameters.conceptSchemes){
					this.extendedSearchTab.startup();
					this.extendedSearchTab.resize();
				}
				this.startup();
				this.resize();

			},
			printResults: function(evtData, autoSelect){
				if(this.resultConceptTab){
					this.resultConceptTab.getParent().closeChild(this.resultConceptTab);
				}
				if(!this.resultTab){
					this.resultTab = new SubTabConceptResults({title: pmbDojo.messages.getMessage('selector', 'selector_tab_results'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
					this.addChild(this.resultTab);
				}
				this.resultTab.setOrigin(evtData.origin);
				
				this.resultTab.setSearchType(autoSelect ? 'concepts' : evtData.currentType);
				this.resultTab.set('content', evtData.results);
				
				this.resultTab.startup();
				this.resultTab.resize();
				
				/**
				 * TODO: faire la selection de l'ongletà  afficher de façon plus propre !
				 */
				if(this.getChildren().length == 5){
					this.selectChild((this.getChildren()[this.getChildren().length-2]), true);	
				}else{
					this.selectChild((this.getChildren()[this.getChildren().length-1]), true);
				}
				
				if(autoSelect){
					query('a[onclick^="set_parent("]', this.selectedChildWidget.domNode)[0].click();
				}
			},
			initEvents: function(){
				this.own(
						topic.subscribe('SubTabConceptSimpleSearch', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SubTabConceptAdvancedSearch', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SubTabConceptResults', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SubTabConceptAdd', lang.hitch(this, this.handleEvents)),
						topic.subscribe('tablist', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SortIframe', lang.hitch(this, this.handleEvents))
				);
			},
			getCurrentMode: function(){ //Le parent est toujours un FormConceptContainer
				var selectedTab = query('span[class="selected"] > a[href^="autorites.php?categ=search&mode="]', this.getParent().containerNode);
				if(selectedTab.length){
					var tabHref = domAttr.get(selectedTab[0], 'href');
				  	var tabQuery = ioQuery.queryToObject(tabHref.substring(tabHref.indexOf("?") + 1, tabHref.length));
				  	return tabQuery.mode;	
				}
				return '';
			},
			elementAdded: function(evtData){
				request(this.parameters.currentURL+"&action=element_display&id_authority="+evtData.id_authority, {
					data: '',
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					this.printResults({results: data}, true);
				}));
			},
			printConcept: function(evtArgs){
				if(!this.resultConceptTab){
					this.resultConceptTab = new SubTabConceptResults({title: pmbDojo.messages.getMessage('selector', 'selector_tab_linked_concept'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
					this.resultConceptTab.set({closable: true});
					this.addChild(this.resultConceptTab);
				}
				//Ici on a toujours des concept donc 9 passé par défaut
				this.resultConceptTab.setSearchType('concepts');
				this.resultConceptTab.set('content', evtArgs.html);
				
				this.selectChild((this.getChildren()[this.getChildren().length-1]), true);
				if(query('a[onclick^="set_parent("]', this.selectedChildWidget.domNode).length == 1){
					query('a[onclick^="set_parent("]', this.selectedChildWidget.domNode)[0].click();	
				}
			},
			getConceptFromAdd: function(evtArgs){
				if(this.getSelectedTab() != 'concepts'){
					if(!this.resultConceptTab){
						this.resultConceptTab = new SubTabConceptResults({title: pmbDojo.messages.getMessage('selector', 'selector_tab_linked_concept'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
						this.resultConceptTab.set({closable: true});
						this.addChild(this.resultConceptTab);
					}
					this.resultConceptTab.setSearchType('concepts');
					this.resultConceptTab.callComposedConcept(evtArgs.id_authority);	
				}else{
					this.elementAdded(evtArgs);
				}
			},
			getSelectedTab: function(){
				var nodesList = query('span.selected[data-pmb-object-type]');
				if (nodesList[0]) {
					return domAttr.get(nodesList[0], 'data-pmb-object-type');
				}
				return "";
			},
		})
});