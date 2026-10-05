// +-------------------------------------------------+
// + 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tabController.js,v 1.1 2020/12/24 14:26:49 qvarin Exp $


define([
	'dojo/_base/declare',
	'dijit/layout/TabContainer',
	'dojo/topic',
	'dojo/_base/lang',
	'apps/pmb/tab/tabContainer',
	'dojo/on',
	'dojo/dom-geometry',
	'dojo/dom-construct',
	'dojo/dom',
	'dojo/query',
],
	function(declare, TabContainer, topic, lang, tab, on, geometry, domConstruct, dom, query) {
		return declare([TabContainer], {
			overlayDiv: [],

			constructor: function() {
				topic.subscribe('openPopup', lang.hitch(this, this.handleEvents));
				topic.subscribe('tabContainer', lang.hitch(this, this.handleEvents));
				on(window, 'message', lang.hitch(this, this.messageEvents));
			},

			handleEvents: function(evtClass, evtType, evtArgs) {
				switch (evtClass) {
					case 'openPopup':
						switch (evtType) {
							case 'buttonClicked':
								this.openTabs(evtArgs);
								break;
						}
						break;
					case 'message':
						switch (evtType) {
							case 'openPopup':
								this.openTabs(evtArgs);
								break;
						}
					case 'tabContainer':
						switch (evtType) {
							case 'saveAdvancedSearch':
								this.saveAdvancedSearch(evtArgs);
								break;
							case 'sendToParent':
								this.sendToParent(evtArgs);
								break;
						}
						break;
				}
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

			openTabs: async function(evtArgs) {

				let params = {
					style: 'width:100%; height:100%;',
					closable: true
				};

				if (evtArgs.title) {
					params.title = evtArgs.title;
				} else {
					params.title = pmbDojo.messages.getMessage('tab', 'new_tab');
				}

				if (!evtArgs.iframe) {
					params.href = evtArgs.url;
				}
				
				var newTab = new tab(params);

				if (evtArgs.iframe) {
					newTab.setIframeContent(evtArgs.url);
				}

				await this.addChild(newTab);
				
				var children = this.getChildren();
				if (children.length > 0) {
					var child = children[children.length - 2];
					if (child && child.params && child.params.closable) {
						child.set('closable', false)
					}
					
					this.selectChild(children[children.length - 1], true);
					
					if (child) {
						this.applyOverlay(child);
					}
				}
			},

			applyOverlay: function(widget) {
				var position = this.getContentBox(widget);
				
				if (!this.overlayDiv[widget.id]) {
					this.overlayDiv[widget.id] = domConstruct.create('div', {
						id: widget.id + '_overlayDiv',
						style: {
							position: 'absolute',
							backgroundColor: 'grey',
							opacity: 0.2,
							zIndex: 1000,
							top: position.y + 'px',
							left: position.x + 'px',
							width: position.w + 'px',
							height: position.h + 'px',
							cursor: 'not-allowed'
						},
						innerHTML: '<span></span>'
					});
					widget.domNode.appendChild(this.overlayDiv[widget.id]);
				} else {
					domStyle.set(this.overlayDiv[widget.id], 'top', position.y + 'px');
					domStyle.set(this.overlayDiv[widget.id], 'left', position.x + 'px');
					domStyle.set(this.overlayDiv[widget.id], 'width', position.w + 'px');
					domStyle.set(this.overlayDiv[widget.id], 'height', position.h + 'px');
				}
			},

			removeChild: function() {
				this.inherited(arguments);
				var children = this.getChildren();
				if (children.length > 0) {

					var child = children[children.length - 1];
					if (child.params && child.params.closable) {
						child.set('closable', true)
					}

					this.removeOverlay(child);
				}
			},

			removeOverlay: function(widget) {
				domConstruct.destroy(widget.id + '_overlayDiv');
				this.overlayDiv[widget.id] = null;
			},

			getContentBox: function(widget) {
				var position = {
					x: 0,
					y: 0,
					w: 0,
					h: 0,
				}

				if (!widget) {
					return position;
				} else {
					position = geometry.position(widget.domNode, true);
					if ((position.h == 0 || postion.w == 0) && widget._contentBox) {
						position.x = widget._contentBox.l;
						position.y = widget._contentBox.t;
						position.w = widget._contentBox.w;
						position.h = widget._contentBox.h;
					}
					return position;
				}
			},

			sendToParent: function(params) {

				if (params.id_champ) {
					var children = this.getChildren();
					var closetab = true;
					
					var tab = children[children.length - 2];
					
					if (tab && tab.iframe) {
						var input_search = tab.getElementById(params.id_champ + '_data');
						var input_search_json = tab.getElementById(params.id_champ + '_json');
						var human_query = tab.getElementById(params.id_champ + '_human');
						var input_human_query = tab.getElementById(params.id_champ + '_human_query');
					} else {
						var input_search = dom.byId(params.id_champ + '_data');
						var input_search_json = dom.byId(params.id_champ + '_json');
						var human_query = dom.byId(params.id_champ + '_human');
						var input_human_query = dom.byId(params.id_champ + '_human_query');
					}
					
					// Champs cacher qui contient la rmc
					if (input_search) {
						if (typeof params.search_serialize != "string") {
							params.search_serialize = JSON.stringify(params.search_serialize)
						}
						input_search.value = params.search_serialize;
					} else {
						closetab &= false;
					}
					
					// Champs cacher qui contient la rmc
					if (input_search_json) {
						if (typeof params.search != "string") {
							params.search = JSON.stringify(params.search)
						}
						input_search_json.value = params.search;
					} else {
						closetab &= false;
					}

					// Champs cacher qui contient la human query
					if (input_human_query) {
						if (typeof params.human_query != "string") {
							params.human_query = JSON.stringify(params.human_query)
						}
						input_human_query.value = params.human_query;
					} else {
						closetab &= false;
					}
					
					// Champs qui vas afficher la human query
					if (human_query) {
						human_query.innerHTML = params.human_query;
					} else {
						closetab &= false;
					}

					// Si closetab == false c'est qu'il y a un problème.
					// on ne récupère pas toutes les infos.
					if (closetab) {
						// on evite de fermer toutes les pages
						if (children[0] !== children[children.length - 1]) {
							this.removeChild(children[children.length - 1])
						}
					} else {
						console.error("Error: data could not be defined in the search fields. (tabController.js)")					
					}
				}
			}
		});
	}
);