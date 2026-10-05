// +-------------------------------------------------+
// + 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tabContainer.js,v 1.2.6.1 2025/01/17 07:46:15 dgoron Exp $


define([
	'dojo/_base/declare',
	'dojox/layout/ContentPane',
	'dojo/dom-construct',
	'dojo/on',
	'dojo/_base/lang',
	'dojo/query',
	'dojo/dom-attr',
	'dojo/dom-form',
	'dojo/request',
	'dojo/topic',
	'dojox/widget/Standby',
],
	function(declare, ContentPane, domConstruct, on, lang, query, domAttr, domForm, request, topic, Standby) {
		return declare([ContentPane], {
			iframe: null,
			standby: null,

			constructor: function() {
				topic.subscribe('openPopup', lang.hitch(this, this.handleEvents));
				on(window, 'message', lang.hitch(this, this.messageEvents));
				this.init();
			},
			
			messageEvents: function(evtArgs) {
				if (evtArgs && evtArgs.data) {
					if (this.isJSON(evtArgs.data)) {
						var args = JSON.parse(evtArgs.data);
						this.handleEvents("message", args.eventType, args);
					}
				}
			},
			
			isJSON: function (value) {
				try {
					JSON.parse(value);
				} catch (e) {
					return false;
				}
				return true;
			},
			
			handleEvents: function(evtClass, evtType, evtArgs) {
				switch (evtClass) {
					case 'message':
						switch (evtType) {
							case 'domChange':
								this.init();
								break;
						}
						break;
					case 'SearchController':
						switch (evtType) {
							case 'domChange':
								this.init();
								break;
						}
						break;
				}
			},

			init: function() {
				if (this.iframe) {
					var nodes = query('[data-pmb-evt]', this.iframe.contentDocument)
				} else {
					var nodes = query('[data-pmb-evt]', this.domNode)
				}
				if (nodes) {
					nodes.forEach((node) => {
						this.addEvent(node);
					});
				}
			},

			setIframeContent(url) {
				if (url) {
					this.removeContent();
					this.iframe = domConstruct.create('iframe', {
						seamless: '',
						'class': 'selectorsIframe',
						scrolling: 'no',
						style: {
							width: '100%',
							height: '100%'
						},
						src: url
					});
					this.set("content", this.iframe);
					this.initStandby();

					on(this.iframe, 'load', lang.hitch(this, function() {
						this.init();
						this.standby.hide();
					}));
				}
			},

			addEvent: function(node) {
				var data_pmb_evt = JSON.parse(domAttr.get(node, 'data-pmb-evt'));
				if (data_pmb_evt && typeof this[data_pmb_evt.method] == "function" && data_pmb_evt.parameters) {
					// on evite d'ajouter plusieur fois le même event
					if (!domAttr.get(node, "data-pmb-parsed")) {
						on(node, data_pmb_evt.type, lang.hitch(this, this[data_pmb_evt.method], data_pmb_evt.parameters));
						domAttr.set(node, "data-pmb-parsed", true);
					}
				}
			},

			saveAdvancedSearch: async function(data, event) {
				if (event.target.form) {
					this.initStandby();
					enable_operators();
					await request.post('./ajax.php?module=ajax&categ=extended_search&sub=get_data_search', {
						data: this.prepareData(event.target.form), // Ne pas utiliser domForm.json, l'opérateur AUTHORITY n'est pas envoyé
						handleAs: 'json'
					}).then(lang.hitch(this, function(response) {
						// response.search correspond a la recherche en json
						response.search = JSON.parse(response.search);
						topic.publish('tabContainer', 'tabContainer', 'sendToParent', { 
							'human_query': response.human_query, 
							'search': response.search, 
							'search_serialize': response.search_serialize, 
							'id_champ': data.id_champ 
						});
						this.standby.hide();
					}));
				}
			},
			
			prepareData: function(form) {
				/**
				 * Ne pas mettre le form dans le contructor
				 * ça prend pas en comptes les champs disabled.
				 */
				var data = new FormData();
				var elements = query("input, select, textarea", form);
				for( var i = 0; i < elements.length; ++i ) {
					var element = elements[i];
					var name = element.name;
					var value = element.value;
					if( name ) {
						if (element.nodeName == "INPUT" && element.type == "button") {
							continue;
						} else if (element.nodeName == "SELECT" && element.multiple) {
							for (var option of element.selectedOptions) {
								data.append(name, option.value);
							}
						} else if (element.nodeName == "INPUT" && ["checkbox", "radio"].includes(element.type)) {
							if (element.checked) {
								data.append(name, value);
							}
						} else {
							data.append(name, value);
						}
					}
				}
				
				return data;
			},

			initStandby: function(){
				if(!this.standby){
					this.standby = new Standby({
						target: this.domNode,
						image: pmbDojo.images.getImage('patience.gif')
					});
					document.body.appendChild(this.standby.domNode);
					this.standby.startup();
				}
				this.standby.show();
			},
			
			removeContent: function() {
				this.set("content", "");
				this.standby = null;
				this.iframe = null;
			},

			getElementById: function(id) {
				var results = [];
				if (this.iframe) {
					results = query('#' + id, this.iframe.contentDocument);
				} else {
					results = query('#' + id, this.domNode);
				}
				
				if (!results[0]) {
					return null;					
				} else {
					return results[0];
				}
			}
		});
	}
);