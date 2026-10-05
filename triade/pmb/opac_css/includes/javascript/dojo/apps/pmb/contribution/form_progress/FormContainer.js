// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormContainer.js,v 1.51 2023/12/27 13:57:47 tsamson Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dijit/layout/TabContainer',
        'apps/pmb/contribution/ContentContribution',
        'dojo/query',
        'dojo/io-query',
        'apps/pmb/contribution/Contribution',
        'dojo/ready',
        'apps/pmb/contribution/ContributionFormEdit',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentContribution, query, ioQuery, Contribution, ready, ContributionFormEdit, topic, registry, domAttr, geometry, domConstruct, domStyle){
		return declare([TabContainer], {
			standby : null,
			overlayDiv: null,
			tabContributions:[],
			isDraft:0,
			paramsURL:null,
			rightArrow: ' <i class="fa fa-arrow-circle-right"></i>',
			'class': "contributionFormContainer",
			usedScenario : 0,
			scrollPosition : 0,
			constructor: function() {
				this.getParamsFromUrl(window.location.toString());
				topic.subscribe('Contribution', lang.hitch(this, this.handleEvents))
				topic.subscribe('ButtonFunction', lang.hitch(this, this.handleEvents))
				topic.subscribe('SubTabResults', lang.hitch(this, this.handleEvents))
				topic.subscribe('FormContributionSelector', lang.hitch(this, this.handleEvents))
				topic.subscribe('removeSubContrib', lang.hitch(this, this.handleEvents))
				topic.subscribe('ContentContribution', lang.hitch(this, this.handleEvents))
			},
			getParamsFromUrl: function(url){
				  var query = url.substring(url.indexOf("?") + 1, url.length);
				  var queryObject = ioQuery.queryToObject(query);
				  this.paramsURL = queryObject;
			},
			handleEvents: function(evtType,evtArgs) {
				switch(evtType){
					case 'savedForm':
						this.fillField(evtArgs);
						this.closeTab(evtArgs.widgetId, evtArgs.response.id);
						this.getEditUrl(evtArgs)
						break;
					case 'addEventOnButton':
						this.addEventOnButton(evtArgs.node);
						break;
					case 'eltClicked':
						this.eltClicked(evtArgs);
						break;
					case 'formLoaded':
						this.parseTab(evtArgs.tabId,evtArgs.formType, evtArgs.uriTemp);
						break;
					case 'savedSubDraftForm':
						this.fillFieldDraft(evtArgs);
						break;						
					case 'removeSubContrib':
						this.fillField(evtArgs);
						this.closeTab(evtArgs.widgetId, evtArgs.response.id);
						this.removeEditButton(evtArgs);
						//on empeche le changement de page tant que l'utilisateur n'a pas re-enregistre
						if (typeof unloadOn == 'function'){
							unloadOn()
						}
						break;						
					case 'closeForm':
						this.closeSubTab(evtArgs.widgetId);
						break;						
				}
			},
			postCreate: function() {
				this.inherited(arguments);
				ready(lang.hitch(this,this.parseTab));
			},
			
			formClicked:function(node){
				//On store la position pour remettre le formulaire en place par la suite
				if(window.scrollY){
					this.scrollPosition = window.scrollY;
				}
				
				var formURL = domAttr.get(node, "data-form_url");
				this.usedScenario = domAttr.get(node, "data-linked_scenario"); 
				var hiddenValue = dom.byId(this.fillIdFinder(node.id) + "_value");
				if (hiddenValue && hiddenValue.value) {
					var parameters = formURL.split("?");
					var objURL = ioQuery.queryToObject(parameters[1]);
					objURL.id = hiddenValue.value; 
					parameters[1] = ioQuery.objectToQuery(objURL);
					formURL = parameters[0] + "?" + parameters[1];
				}
				var formTitle = domAttr.get(node, "data-form_property");
				
				var formType = 'contribution_area_form_';
				var formId = 0;
				formType += formId;
				var newTab = new ContentContribution({title:formTitle, closable:true, nodeClickedId: this.fillIdFinder(node.id), preload : true});
				newTab.set({href:formURL+"&tab_id="+newTab.id+"&sub_title="+formTitle});
				newTab.set({onDownloadEnd : lang.hitch(this, this.parseTab, newTab.id, formType)});
				this.addChild(newTab);
				this.selectChild(newTab);
				
				this.setClosableTab();				
			},
			parseTab: function(tabId, formType, uriTemp) {
				
				if (uriTemp) {
					var nodeToFill = registry.byId(tabId).nodeClickedId;
					if (nodeToFill) {
						domAttr.set(nodeToFill+'_value', 'value', uriTemp);
					}
				}
				
				if (!tabId) {
					tabId = this.getChildren()[0].id
				} else {
					var cancel_button = query(".cancel_part > *", dom.byId(tabId))[0];
					if(cancel_button) {
						cancel_button.onclick = '';
						on(cancel_button, "click", lang.hitch(this, this.closeTab, tabId));
					}
				}
				
				new ContributionFormEdit(formType, dom.byId(tabId));				
				
				var nodes_sel = query('input[id$="_sel"][type="button"]', tabId);
				var nodes_sel_hidden = query('input[id$="_sel"][type="hidden"]', tabId);
				var nodes_search = query('input[id$="_search"][type="button"]', tabId);
				var nodes_edit = query('input[id$="_edit"][type="button"]', tabId);
				var nodes_edit_hidden = query('input[id$="_edit"][type="hidden"]', tabId);
				var elements  = [nodes_sel, nodes_sel_hidden, nodes_search, nodes_edit, nodes_edit_hidden];
				if (this.tabContributions[tabId]){
					this.tabContributions[tabId].init();
				} else {
					// Contenu de l'onglet
					this.tabContributions[tabId] = new Contribution(tabId);
					this.tabContributions[tabId].init();
				}
				
				//Ajout du style Draft
				nodes = query('input[id*="_is_draft"][type="hidden"]', tabId);
				for (node of nodes){
					if (node.value == 1){
						const regex = /(\w*)_\d+_is_draft$/i;
						var match = regex.exec(node.id)
						if (match) {
							var containerNode = dom.byId(match[1]);
							let label = query('label.etiquette', containerNode);
							if (!dom.byId(containerNode.id+"_etiquette_draft")){
								domConstruct.create("span", {
									id : containerNode.id+"_etiquette_draft",
									innerHTML : "<i>"+pmbDojo.messages.getMessage("contribution","onto_contribution_draft_editing")+"</i>",
								}, label[0]);
							}
						}
					}
				}
							
				elements.forEach(lang.hitch(this,function(element){
					element.forEach(lang.hitch(this,function(node){
						this.addEventOnButton(node);
					}));
				}));
				
				this.resize();
			},
			addEventOnButton: function(node){
				on(node, "click", lang.hitch(this, this.formClicked, node));
			},
			closeTab: function(tabId, id){
				var child = registry.byId(tabId);
				child.destroyDescendants();
				this.removeChild(child, id);
			},
			getIsDraft:function(tabId){
				var child = registry.byId(tabId);
				var nodeToFill = child.nodeClickedId;
				var node = dom.byId(nodeToFill+'_is_draft');
				if (node){
					return node.value;
				}
				return false;
			},
		    fillIdFinder: function(originalOne){
                if (originalOne.indexOf('_sel',originalOne.length-5) > 0){
                    var baseId = originalOne.split(/_sel$/)[0]; //Id du bouton (...)
                } else if (originalOne.indexOf('_search', originalOne.length-8) > 0){
                    var baseId = originalOne.split(/_search$/)[0]; //Id du bouton (...)
                }else {
                    var baseId = originalOne.split(/_edit$/)[0]; //Id du bouton (...)
                }
                return baseId;
                var nodeList = query('input[id*="'+baseId+'"][type="text"]'); //Noeuds dom correspondants aux champs texte associes au bouton
                var nodeToFill = nodeList[(nodeList.length)-1]; // On recupere le dernier creer (dernier dans la liste de resultat)
                var splittedId = nodeToFill.getAttribute('id').split('_display_label')[0];//On split cet id pour recuperer la chaine de base (a terme pour valoriser type / value)
                return splittedId;
            },
			fillField: function(data){
				var nodeToFill = registry.byId(data.widgetId).nodeClickedId;
				if (nodeToFill) {
					domAttr.set(nodeToFill+'_display_label', 'value', data.response.displayLabel);
					domAttr.set(nodeToFill+'_value', 'value', data.response.uri);
					domAttr.set(nodeToFill+'_is_draft', 'value', '0');
					
					var resourceSelector = registry.byId(nodeToFill+'_display_label');
					if (resourceSelector) {
						resourceSelector.updateTemplate();
					}
				}
			},

			fillFieldDraft: function(data){
				var nodeToFill = registry.byId(data.widgetId).nodeClickedId;
				var displayLabel = pmbDojo.messages.getMessage("contribution", "contribution_draft_name_js");
				if (typeof data.response.displayLabel != "undefined"){
					displayLabel = data.response.displayLabel;
				}
				if (nodeToFill) {
					this.getEditUrl(data, true);
					domAttr.set(nodeToFill+'_display_label', 'value', displayLabel);
					domAttr.set(nodeToFill+'_value', 'value', data.response.uri);
					domAttr.set(nodeToFill+'_is_draft', 'value', '1');
					node = dom.byId(nodeToFill);
					node.classList.add("contribution_draft");
					let label = query('label.etiquette', node.parentNode);
					if (!dom.byId(node.parentNode.id+"_etiquette_draft")){
						domConstruct.create("span", {
							id : node.parentNode.id+"_etiquette_draft",
							innerHTML : "<i>"+pmbDojo.messages.getMessage("contribution","onto_contribution_draft_editing")+"</i>",
						}, label[0]);
					}
					var resourceSelector = registry.byId(nodeToFill+'_display_label');
					if (resourceSelector) {
						resourceSelector.updateTemplate();
					}
				}
			},
			removeChild : function(tab, id) {
				if (tab){
					this.isDraft = this.getIsDraft(tab);
				}
				this.inherited(arguments);
				this.setClosableTab();
				if (this.getChildren().length == 0) {
					window.location.href = "./empr.php?tab=contribution_area&lvl=contribution_area_list&last_id="+id;
				}
				
				// Si on a une uri temporaire dans le champs cache on la supprime (sauf s'il sagit d'un brouillon deja enregistre)
				var nodeToFill = tab.nodeClickedId;
				if (nodeToFill) {
					uriNode = dom.byId(nodeToFill+'_value');
					if (uriNode && uriNode.value) {
						var array = uriNode.value.split("_");
						if (array.includes("temp")) {
							if (this.isDraft == 0){
								uriNode.value = "";
								var node = dom.byId(nodeToFill+'_is_draft');
								if (node) {
									node.value = "0"
								}
							}
						}
					}
					if (this.isDraft == 0){
						node = dom.byId(nodeToFill);
						node.classList.remove("contribution_draft");
						let label = query('label.etiquette', node.parentNode);
						if (dom.byId(node.parentNode.id+"_etiquette_draft")){
							domConstruct.destroy(dom.byId(node.parentNode.id+"_etiquette_draft"));
						}
					}
				}
			},
			
			setClosableTab : function() {
				this.getChildren().forEach(lang.hitch(this, function(tab){
					tab.set('title', tab.get('title').replace(this.rightArrow, ''));
					if (this.getIndexOfChild(tab) != (this.getChildren().length -1)) {
						tab.set({closable : false});
						if(this.getChildren().length > 1){
							this.applyOverlay(tab);
							tab.set('title', tab.get('title') + this.rightArrow);
						}
					}else if(this.getIndexOfChild(tab) != 0){
						tab.set({closable : true});
					}else{
						this.applyOverlay(tab, false);
						
						//Si une position Y est sauvee, on remet l'utilisateur au bon endroit dans le formulaire
						if(this.scrollPosition > 0){
							window.scrollTo(0, this.scrollPosition);
							this.scrollPosition = 0;
						}
					}
				}));
				
			},
			
			selectChild : function(page,animate) {
				this.inherited(arguments);
				if(this.getIndexOfChild(page) != (this.getChildren().length -1)){
					this.applyOverlay(page);
				}else{
					this.applyOverlay(page, false);
				}
			},
			
			applyOverlay: function(widget, attr_disabled = true) {
				let nodeChildren = widget.domNode.children;
				for (let node of nodeChildren) {
					if (node.nodeName == 'FORM') {
						this.disableInputForm(node, attr_disabled)
					}
					if (node.attributes.widgetid) {
						var formList = query("form", node).forEach(lang.hitch(this, function(node) {
							this.disableInputForm(node, attr_disabled)
						}));
					}
				}
			},
			disableInputForm: function(form, attr_disabled){
				for (let formNode of form){
					switch (formNode.nodeName) {
						case 'BUTTON':
						case 'FIELDSET':
						case 'INPUT':
						case 'OPTGROUP':
						case 'SELECT':
						case 'TEXTAREA':
							formNode.disabled = attr_disabled;
							if ('INPUT' == formNode.nodeName && true == attr_disabled) {
								formNode.title = pmbDojo.messages.getMessage("contribution","onto_contribution_input_title");
							}else{
								formNode.title = '';
							}
							break;
						default:
							break;
					}
				}
				var elt = query("div[id='el0Parent']",form );
				if (elt[0]) {
					if (true == attr_disabled) {
						if (0 == elt[0].childElementCount) {
							domConstruct.create('h3', {id:'overlayLib', class:'overlayLibelle',innerHTML: pmbDojo.messages.getMessage("contribution","onto_contribution_form_inprogress")}, elt[0]);
						}
					}else{
						var overlayLib = query("h3[id='overlayLib']",form );
						if (0 != overlayLib.length) {
							domConstruct.destroy(overlayLib[0]);
						}
					}
				}
			},
			eltClicked : function(data) {
				if (data.tabId){
					var myDijit = registry.byId(data.tabId);
					if (myDijit && !myDijit.closable){
						return;
					}
				}
				//todo : a revoir quand on aura des entites diffrentes des autorites ou des notices
				var type = (data.id_authority ? "authority" : (data.type == "bulletin" ? "bulletin" : "record"));
				var url = "./ajax.php?module=selectors&what=contribution&action=save_in_store";
				var selector_data = {
						type,
						id : (data.id_authority ? data.id_authority : data.id)
				};
				selector_data = JSON.stringify(selector_data);
				xhr.post(url,{
					handleAs : "json",
					data : {
						selector_data
					}
				}).then(lang.hitch(this, function(response){
					//on empeche le changement de page tant que l'utilisateur n'a pas re-enregistre
					if (typeof unloadOn == 'function'){
						unloadOn()
					}
					var lastChild = this.getChildren()[this.getChildren().length - 1];
					if (lastChild.closable) {
						topic.publish('Contribution', 'savedForm', {widgetId: lastChild.id, response});
					}
				}), function(err){
					console.log(err);
				});
				
			},
			getEditUrl : function(data, is_draft = false) {
				var nodeToFill = registry.byId(data.widgetId).nodeClickedId;
				if (nodeToFill) {
					var editButton = dom.byId(nodeToFill+'_edit');
					if(editButton) {
						this.usedScenario = domAttr.get(editButton, "data-linked_scenario"); 
					}
				}
				if (data.response.uri) {
					var url = "./ajax.php?module=selectors&what=contribution&action=get_edit_url";
					var selector_data = {
							entity_uri : data.response.uri,
							scenario : this.usedScenario,
							is_draft: is_draft,
							sub : this.paramsURL.sub
					};
					selector_data = JSON.stringify(selector_data);
					xhr.post(url,{
						handleAs : "text",
						data : {
							selector_data
						}
					}).then(lang.hitch(this, function(response){
						if (editButton) {
							var typeEntite = dom.byId(nodeToFill+'_type');
							if(response) {
								domAttr.set(nodeToFill+'_edit', 'data-form_url', response);
								domAttr.set(nodeToFill+'_edit', 'type', 'button');
								domAttr.set(nodeToFill+'_sel', 'type', 'hidden');
							} else {
								domAttr.set(nodeToFill+'_edit', 'type', 'hidden');
								domAttr.set(nodeToFill+'_sel', 'type', 'button');
							}
						}
					}), function(err){
						console.log(err);
					});
				}
			},
			removeEditButton : function(data){
				var nodeToFill = registry.byId(data.widgetId).nodeClickedId;
				if (nodeToFill) {
					domAttr.set(nodeToFill+'_edit', 'data-form_url', '');
					domAttr.set(nodeToFill+'_edit', 'type', 'hidden');
					var selButton = dom.byId(nodeToFill+'_sel');
					if (selButton && selButton.type == "hidden") {
						selButton.type = "button";
					}
				}
			},
			closeSubTab: function(tabId) {
				var ContentContribution = registry.byId(tabId);
				if (ContentContribution) {
					ContentContribution.destroyDescendants();
					this.removeChild(ContentContribution);
				}
			},
		})
});
