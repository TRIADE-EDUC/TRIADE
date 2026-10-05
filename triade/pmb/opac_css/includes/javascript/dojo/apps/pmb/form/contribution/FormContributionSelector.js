// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormContributionSelector.js,v 1.27 2023/01/27 09:42:35 tsamson Exp $

/*
 * C'est cette classe qui aura la lourde responsabilit� de mettre en place
 * l'ensemble des onglets permettant de repr�senter un selecteur
 * 
 * Cette classe devra pouvoir �tre utilis�e dans les selecteurs comme dans le module
 * de gestion des formulaires. Pr�voir l'utilisation d'un mode permettant de d�finir
 * le contexte dans lequel nous nous trouvons 
 */

define([
        'dojo/_base/declare',
        'apps/pmb/form/FormSelector',
        'apps/pmb/form/FormBaseSelector',
        'apps/pmb/form/SubTabAdd',
        'apps/pmb/form/SubTabAdvancedSearch',
        'apps/pmb/form/contribution/SubTabContributionSimpleSearch',
        'apps/pmb/form/SubTabResults',
        'dojo/_base/lang',
        'dojo/topic',
        'dojox/widget/Standby',
        'dojo/query',
        'dojo/dom-attr',
        'dojo/on',
        ], function(declare, FormSelector, FormBaseSelector, SubTabAdd, SubTabAdvancedSearch,	SubTabContributionSimpleSearch, SubTabResults, lang, topic, Standby, query, domAttr, on){
		return declare([FormBaseSelector], {
			subTabAdd:0,
			formURL:'',
			tabId :'',
			selectTab:'',
			multiScenario:0,
			newAttachmentTab:null,
			multiScenarioTitle:"",
			
			createTabs: function() {
				
				if (this.multiScenario) {
					//Afficher la question de l'attachment
						
					if(!this.newAttachmentTab) {
						this.newAttachmentTab = new SubTabAdd({
						    title: this.multiScenarioTitle, 
						    style: 'width:95%; height:100%;', 
						    loadScripts: true, 
						    parameters: this.parameters
						});

						this.newAttachmentTab.href = this.formURL;
						this.newAttachmentTab.set({
						    onDownloadEnd:lang.hitch(this, this.downloadEndNewAttachmentTab)
						});
					}
					
					this.addChild(this.newAttachmentTab);	
					
					this.newAttachmentTab.startup();
					this.newAttachmentTab.resize();
					
					this.startup();
					this.resize();
					this.selectChild(this.newAttachmentTab);
					
				} else {
					
					//on teste si on ne provient pas d'un schema de catalogage
					if (!parseInt(this.parameters.noSearch)) {
						this.simpleSearchTab = new SubTabContributionSimpleSearch({
							title: pmbDojo.messages.getMessage('selector', 'selector_tab_simple_search'), 
							style: 'width:95%; height:100%;', 
							parameters: this.parameters
						});
						this.simpleSearchTab.href = this.parameters.selectorURL+'&action=simple_search';
						this.addChild(this.simpleSearchTab);
						
						this.simpleSearchTab.resize();
						this.simpleSearchTab.startup();
					}
					
//					this.extendedSearchTab = new SubTabAdvancedSearch({title: pmbDojo.messages.getMessage('selector', 'selector_tab_advanced_search'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
//					this.extendedSearchTab.href = this.parameters.selectorURL+'&action=advanced_search&mode='+this.parameters.multicriteriaMode;
//					this.addChild(this.extendedSearchTab);
//
//					this.extendedSearchTab.startup();
//					this.extendedSearchTab.resize();
					
					this.addSubTabAdd();
					
					this.startup();
					this.resize();
					if (this.subTabAdd && this.newTab && this.selectTab) {
						// Selection de l'onglet Ajouter.
						this.selectChild(this.newTab);
					}
				}
				

			},
			printResults: function(evtData, autoSelect) {
				if (!query('form[id="'+evtData.formId+'"]', this.domNode)[0]) {
					return;
				}
				
				title = pmbDojo.messages.getMessage('contribution', 'search_result_tab_fond').replace('%s', evtData.nb_results);
				if (!this.resultTab) {
					if (!this.parameters.isOntology) {
						this.resultTab = new SubTabResults({title: title, style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters, tabId:this.tabId});	
					} else {
						this.resultTab = new SubTabConceptResults({title: title, style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
					}
					//on precise la position de l'onglet
					this.addChild(this.resultTab, 1);
				} else {
					this.resultTab.set('title', title);
				}
				
				this.resultTab.setOrigin(evtData.origin);
				this.resultTab.set('content', evtData.results);
				
				this.resultTab.startup();
				this.resultTab.resize();
				
				this.selectChild((this.getChildren()[this.getChildren().length-1]), true);
				if (autoSelect) {
					query('a[onclick^="set_parent("]', this.selectedChildWidget.domNode)[0].click();
				}
				
				if (typeof evtData.selectResultTabSearch !== 'undefined' && evtData.selectResultTabSearch == "contribution") {
					this.selectChild(this.resultTab);
				}
			},
			printResultsStore: function(evtData, autoSelect) {
				if (!query('form[id="'+evtData.formId+'"]', this.domNode)[0]) {
				    return;
				}
				
				title = pmbDojo.messages.getMessage('contribution', 'search_result_tab_title').replace('%s', evtData.nb_results);
				if (!this.resultTabStore) {
					if (!this.parameters.isOntology) {
						this.resultTabStore = new SubTabResults({title: title, style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});	
					} else {
						this.resultTabStore = new SubTabConceptResults({title: title, style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
					}
					//on precise la position de l'onglet
					this.addChild(this.resultTabStore, 2);
				} else {
					this.resultTabStore.set('title', title);
				}
				
				this.resultTabStore.setOrigin(evtData.origin);
				this.resultTabStore.set('content', evtData.results);
				
				this.resultTabStore.startup();
				this.resultTabStore.resize();
				
				if (typeof evtData.selectResultTabSearch !== 'undefined' && evtData.selectResultTabSearch == "contribution") {
					this.selectChild(this.resultTabStore);
				}else{
					this.selectChild(this.resultTab);
				}
			},
			initStandby: function(evtData){
				if (!query('form[id="'+evtData.formId+'"]', this.domNode)[0]) {
					return;
				}
				
				if(!this.standby){
					this.standby = new Standby({
						target: this.domNode
					});
					document.body.appendChild(this.standby.domNode);
					this.standby.startup();
				}
				this.standby.show();
				
			},
			initStandbyStore: function(evtData){
				if (!query('form[id="'+evtData.formId+'"]', this.domNode)[0]) {
				    return;
				}
				if(!this.standbyStore){
					this.standbyStore = new Standby({
						target: this.domNode
					});
					document.body.appendChild(this.standbyStore.domNode);
					this.standbyStore.startup();
				}
				this.standbyStore.show();
			},
			closePrintResultsStore: function(){
				if (this.resultTabStore) {
					this.removeChild(this.resultTabStore);
					this.resultTabStore = null;
				}
			},
			closeSubTabAdd: function(){
				if (this.newTab) {
					this.removeChild(this.newTab);
					this.newTab= null;
				}
			},
			addSubTabAdd: function(){
				if (this.subTabAdd) {
					if(!this.newTab) {
						//On test que l'on dispose de la valeur et que l'on provient d'une notice avant d'ajouter le parametre
						if (document.getElementById('item_uri').value && document.getElementById('prefix_uri').value.split('_')[0] == "record"){
							this.parameters.parentUri = document.getElementById('item_uri').value;
							//recupere le titre de la notice
							if (document.getElementById('form_id').value){
								var prefix = document.getElementById('form_id').value;
								this.parameters.noticeTitle = document.getElementById(prefix+'_tit1_0_value').value;
							}
						}
						
						//Ajouter ou modification
						let selector_tab_name = 'selector_tab_add';
						if(this.subTabEdit){
							selector_tab_name = 'selector_tab_edit';
						}
						
						this.newTab = new SubTabAdd({
							title: pmbDojo.messages.getMessage('selector', selector_tab_name), 
							style: 'width:95%; height:100%;', 
							loadScripts: true, 
							parameters: this.parameters
						});
						this.newTab.href = this.formURL;
						this.newTab.set({
							onDownloadEnd:lang.hitch(this, this.downloadEndNewTab)
						});
					}
					this.addChild(this.newTab);	
					
					this.newTab.startup();
					this.newTab.resize();
				}
			},
			downloadEndNewTab: function(){
				var nodeList = query("a[data-form_url]", this.newTab.domNode);
				nodeList.forEach(
					lang.hitch(this, 
						function (node){
							on(node, "click", lang.hitch(this, function(e){
								this.newTab.href = domAttr.get(e.target, "data-form_url")
								this.newTab.refresh();
							}))
						}
					)
				);
				
				//Dans le cas de l'edition d'un docnum li� �une notice
				var prefix_form = query("input[id=form_id]",this.newTab.domNode);
				var prefix = query("input[id=prefix_uri]",this.newTab.domNode);
				if(prefix[0]){
					var prefix_split= prefix[0].value.split('_');
					if (this.parameters.parentUri && this.subTabAdd && this.newTab && prefix_split[0] == 'docnum'){
						if (query('input[id='+prefix_form[0].value+'_has_record_0_value]')[0]){
							query('input[id='+prefix_form[0].value+'_has_record_0_value]')[0].value = this.parameters.parentUri;
						}
						if (query('input[id='+prefix_form[0].value+'_has_record_0_display_label]')[0]){
							if (this.parameters.noticeTitle!=undefined && this.parameters.noticeTitle!=''){
								query('input[id='+prefix_form[0].value+'_has_record_0_display_label]')[0].value = this.parameters.noticeTitle;
							}
							else{
								query('input[id='+prefix_form[0].value+'_has_record_0_display_label]')[0].value = pmbDojo.messages.getMessage('contribution', 'contribution_draft_name_js');
							}
							query('input[id='+prefix_form[0].value+'_has_record_0_display_label]')[0].setAttribute("disabled", "disabled");
						}
						//On masque les boutons et empeche de modifier la notice lie
						if (query('input[id='+prefix_form[0].value+'_has_record_0_del')[0]){
							query('input[id='+prefix_form[0].value+'_has_record_0_del')[0].type = "hidden";
						}
						if (query('input[id='+prefix_form[0].value+'_has_record_0_search')[0]){
							query('input[id='+prefix_form[0].value+'_has_record_0_search')[0].type = "hidden";
						}
						//On masque la classe CSS applique et on retire la value is_draft
						if (query('input[id='+prefix_form[0].value+'_has_record_0_is_draft')[0]){
							query('input[id='+prefix_form[0].value+'_has_record_0_is_draft')[0].value = "";
						}
						if (query('div[id='+prefix_form[0].value+'_has_record_0')[0]){
							query('div[id='+prefix_form[0].value+'_has_record_0')[0].classList.remove("contribution_draft");
						}
						//On ajoute le type
						if (query('input[id='+prefix_form[0].value+'_has_record_0_type')[0]){
							query('input[id='+prefix_form[0].value+'_has_record_0_type')[0].value = "http://www.pmbservices.fr/ontology#record";
						}
					}
				}
				
				var uriTemp = "";
				if (!this.subTabEdit && this.newTab) {
					// On r�cup�re l'uri de l'entit�
					var itemUri = this.newTab.containerNode.querySelector("#item_uri");
					if (itemUri) {
						uriTemp = itemUri.value;
					}
				}
				
				var formList = query("form", this.newTab.domNode);
				if (formList && formList[0]) {
					var urlForm = formList[0].action;
					var type = /lvl=(\w+)&?/g.exec(urlForm)[1];
					switch(type){
						case 'contribution_area':
							var form = /form_id=(\w+)&?/g.exec(urlForm);
							if (form) {
								var formId = form[1];
								type += '_form_'+formId;
								break;
							}
						default:
							type = "contribution_area_form_"
							break;
					}
				}

				if (!type) {
					var type = "contribution_area_form_"
				}

				topic.publish('FormContributionSelector', 'formLoaded', {tabId:this.tabId,formType:type,uriTemp: uriTemp});
			},
			
			downloadEndNewAttachmentTab: function() {
			
			    var nodeList = query("a[data-form_url]", this.newAttachmentTab.domNode);
			    nodeList.forEach(
			        lang.hitch(this, function (node) {
			            on(node, "click", lang.hitch(this, function(e) {
			                this.newAttachmentTab.destroyDescendants();
			                this.removeChild(this.newAttachmentTab);
			                this.newAttachmentTab = null;
			                
			                this.multiScenario = 0;
			                this.formURL = domAttr.get(e.target, "data-form_url");
			                this.parameters.selectorURL += "&current_scenario_id="+domAttr.get(e.target, "data-scenario_id"); 
			                this.createTabs();
			            }))
			        })
			    );
			}
		});
})