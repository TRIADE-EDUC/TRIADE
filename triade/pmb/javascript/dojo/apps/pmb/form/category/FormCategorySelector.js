// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormCategorySelector.js,v 1.8 2021/11/12 13:52:04 dgoron Exp $

define([
        'dojo/_base/declare',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/query',
        'dojo/on',
        'dojo/request',
        'dojo/dom-attr',
        'apps/pmb/form/category/SubTabHierarchicalSearch',
        'apps/pmb/form/category/SubTabTermsSearch',
        'apps/pmb/form/category/SubTabAutoindexSearch',
        'apps/pmb/form/category/SubTabCategoryAdd',
        'apps/pmb/form/category/SubTabCategoryResults',
        'apps/pmb/form/FormSelector',
        ], function(declare, lang, topic, query, on, request, domAttr, SubTabHierarchicalSearch, SubTabTermsSearch, SubTabAutoindexSearch, SubTabCategoryAdd, SubTabCategoryResults, FormSelector){
		return declare([FormSelector], {
			hierarchicalSearchTab: null,   //Onglet rech hiérarchique
			termsSearchTab: null,   //Onglet rech par termes
			autoindexSearchTab: null,	//Onglet indexation automatique
			handleEvents: function(evtClass, evtType, evtArgs) {
				switch (evtClass) {
					case 'SubTabHierarchicalSearch':
					case 'SubTabTermsSearch':
					case 'SubTabAutoindexSearch':
					case 'SubTabCategoryResults':
				  		switch (evtType) {
							case 'printResults':
					  			this.printResults(evtArgs);
					  			break;
			  			}
				  		break;
				  }
				this.inherited(arguments);
			},
			createTabs: function(){
				/**
				 * Ici doit être récupérée l'url du selecteur
				 */
				if(this.isVisibleTab('hierarchical_search')) {
					this.hierarchicalSearchTab = new SubTabHierarchicalSearch({title: pmbDojo.messages.getMessage('selector', 'selector_tab_hierarchical_search'), style: 'width:90%; height:100%;', parameters: this.parameters});
					this.hierarchicalSearchTab.href = this.parameters.selectorURL+'&action=hierarchical_search&search_type=hierarchy';
					if(this.isDefaultSelectedTab('hierarchical_search')) {
						this.hierarchicalSearchTab.selected = true;
					}
					this.addChild(this.hierarchicalSearchTab);
					
					this.hierarchicalSearchTab.resize();
					this.hierarchicalSearchTab.startup();
				}
					
				if(this.isVisibleTab('terms_search')) {
					this.termsSearchTab = new SubTabTermsSearch({title: pmbDojo.messages.getMessage('selector', 'selector_tab_terms_search'), style: 'width:90%; height:100%;', parameters: this.parameters});
					this.termsSearchTab.href = this.parameters.selectorURL+'&action=terms_search&search_type=term';
					if(this.isDefaultSelectedTab('terms_search')) {
						this.termsSearchTab.selected = true;
					}
					this.addChild(this.termsSearchTab);
				
					this.termsSearchTab.resize();
					this.termsSearchTab.startup();
				}
				
				if(this.isVisibleTab('indexation_auto') && this.parameters.autoindex_class && this.parameters.auto_index_notice_fields) {
					this.autoindexSearchTab = new SubTabAutoindexSearch({title: pmbDojo.messages.getMessage('selector', 'selector_tab_indexation_auto'), style: 'width:90%; height:100%;', parameters: this.parameters});
					this.autoindexSearchTab.href = this.parameters.selectorURL+'&action=autoindex_search&search_type=autoindex';
					if(this.isDefaultSelectedTab('indexation_auto')) {
						this.autoindexSearchTab.selected = true;
					}
					
					this.addChild(this.autoindexSearchTab);
					
					this.autoindexSearchTab.resize();
					this.autoindexSearchTab.startup();
				}
				
				if (this.isVisibleTab('add') && this.parameters.bt_ajouter != 'no') {
					if(!this.newTab) {
						this.newTab = new SubTabCategoryAdd({title: pmbDojo.messages.getMessage('selector', 'selector_tab_add'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
						this.newTab.href = this.parameters.selectorURL+'&action=add&form_display_mode=2';
						if(this.isDefaultSelectedTab('add')) {
							this.newTab.selected = true;
						}
					}
				}
				
				this.inherited(arguments);
			},
			printResults: function(evtData, autoSelect){
				if(!this.resultTab){
					this.resultTab = new SubTabCategoryResults({title: pmbDojo.messages.getMessage('selector', 'selector_tab_results'), style: 'width:90%; height:100%;', loadScripts: true, parameters: this.parameters });
					this.addChild(this.resultTab);
				}
				this.resultTab.setSearchType(evtData.search_type);
				this.inherited(arguments);
	
				if(document.getElementById('category_parent_id') && evtData.parent && document.getElementById('category_parent') && document.getElementById('categ_libelle_header')) {
					document.getElementById('category_parent_id').value = evtData.parent;
					document.getElementById('category_parent').value = document.getElementById('categ_libelle_header').innerHTML;
				}
			},
			initEvents: function(){
				this.own(
						topic.subscribe('SubTabHierarchicalSearch', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SubTabTermsSearch', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SubTabAutoindexSearch', lang.hitch(this, this.handleEvents)),
						topic.subscribe('SubTabCategoryResults', lang.hitch(this, this.handleEvents))
				);
				this.inherited(arguments);
			}
		})
});