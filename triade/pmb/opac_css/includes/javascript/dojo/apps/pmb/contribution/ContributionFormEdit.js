// +-------------------------------------------------+
// + 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContributionFormEdit.js,v 1.7 2023/10/31 13:45:17 qvarin Exp $


define([
	'dojo/_base/declare',
	'apps/pmb/gridform/FormEdit',
	'dojo/query',
	'dojo/dom-attr',
	'dojo/_base/lang',
	'dojo/dom-construct',
	'dojo/dom-style'
],
	function (declare, FormEdit, query, domAttr, lang, domConstruct, domStyle) {
		return declare(FormEdit, {
			gridForm: {},
			buildGrid: function (data) {
				let activeElement = document.activeElement;

				// On stocke le premier niveau des enfants de zone-container des div non movable (nettoye apres traitement)
				let cleanElts = query('#zone-container > div:not([movable="yes"])', this.context);

				// Grille sauvegarder en base
				if (typeof data != 'undefined' && data != "") {
					let savedScheme = JSON.parse(data);
					if (savedScheme) {
						this.savedScheme = savedScheme;
					}
				}

				// grid final
				let grid = new Array();
				let indexDefaultGrid = null;

				// Liste des champs du formulaire
				this.gridForm = new Object();

				// Tableau des elements qu'on a déjà placés
				let tmp = new Object();

				// On extrait la liste des champs du formulaire de contribution
				let currentElts = query('div[movable="yes"]', this.context);
				for (let i = 0; i < currentElts.length; i++) {
					let node = query('div#' + currentElts[i].id, this.context);
					if (node && node[0] && domAttr.has(node[0], "data-pmb-propertyname")) {
						let propertyName = domAttr.get(node[0], "data-pmb-propertyname");
						if (propertyName) {
							this.gridForm[propertyName.toLowerCase()] = {
								"propertyName": propertyName,
								"nodeId": currentElts[i].id,
								"className": currentElts[i].className
							};
						} else {
							console.error(`div#${currentElts[i].id} attribute not found (data-pmb-propertyname)`)
						}
					}
				}

				// On ajoute les champs sauvegarder dans notre grid
				if (this.savedScheme) {
					for (let i = 0; i < this.savedScheme.length; i++) {
						let zone = lang.clone(this.savedScheme[i]);
						zone.elements = new Array();

						if (zone.nodeId == "el0") {
							indexDefaultGrid = grid.length;
						}

						for (let j = 0; j < this.savedScheme[i].elements.length; j++) {
							let element = this.savedScheme[i].elements[j];
							// On vérifi si le champs est toujours présent
							if (element.propertyName) {
								let gridFormData = this.gridForm[element.propertyName.toLowerCase()];
								if (gridFormData) {
									tmp[element.propertyName.toLowerCase()] = true;

									zone.elements.push({
										"propertyName": element.propertyName,
										"nodeId": gridFormData.nodeId,
										"className": element.className,
										"visible": element.visible,
										"disabled": element.disabled
									});
								}
							}
						}

						grid.push(zone);
					}
				}

				// Zone par défaut n'a pas été créer
				if (indexDefaultGrid == null) {
					indexDefaultGrid = grid.length;
					grid.push({
						"nodeId": "el0",
						"visible": true,
						"label": pmbDojo.messages.getMessage('grid', 'grid_js_move_default_zone'),
						"showLabel": false,
						"isExpandable": false,
						"elements": []
					});
				}

				// On ajoute dans la zone par défaut, les champs que l'on connait pas
				for (let propertyName in this.gridForm) {
					if (!tmp[propertyName.toLowerCase()]) {
						grid[indexDefaultGrid].elements.push({
							"propertyName": this.gridForm[propertyName].propertyName,
							"nodeId": this.gridForm[propertyName].nodeId,
							"className": this.gridForm[propertyName].className,
							"visible": true,
							"disabled": false
						});
					}
				}

				// On places tout les éléments dans l'ordre
				if (grid) {
					// On contruit la grid
					for (let i = 0; i < grid.length; i++) {
						this.buildZone(grid[i]);
					}
				}

				// Traitement terminé, on nettoye
				for (let i = 0; i < cleanElts.length; i++) {
					domConstruct.destroy(cleanElts[i]);
				}

				activeElement.focus();
			},
			buildZone: function (zone) {

				// On définis les paramètres de la zone
				let params = {
					isExpandable: zone.isExpandable,
					showLabel: zone.showLabel,
					visible: zone.visible,
					label: zone.label,
					nodeId: zone.nodeId
				};

				let parentNode = null;
				let domNode = null;

				// zone extensible ?
				if (params.isExpandable) {
					parentNode = domConstruct.create('div', {
						id: params.nodeId + 'Parent',
						class: 'parent'
					}, query('#zone-container', this.context)[0], 'last');

					let labelNode = domConstruct.create('h3', {
						innerHTML: params.label,
						style: {
							'display': 'inline'
						}
					}, parentNode, 'last');

					domConstruct.create('img', {
						src: pmbDojo.images.getImage('plus.gif'),
						class: 'img_plus',
						align: 'bottom',
						name: 'imEx',
						id: params.nodeId + 'Img',
						title: 'titre',
						border: '0',
						onClick: 'expandBase("' + params.nodeId + '", true); return false;'
					}, labelNode, 'before');

					domNode = domConstruct.create('div', {
						id: params.nodeId + 'Child',
						label: params.label,
						class: 'child'
					}, query('#zone-container', this.context)[0], 'last');

				} else {
					// Affichage du label
					if (params.showLabel) {
						parentNode = domConstruct.create('div', {
							id: params.nodeId + 'Parent',
							class: 'parent'
						}, query('#zone-container', this.context)[0], 'last');

						domConstruct.create('h3', {
							innerHTML: params.label
						}, parentNode, 'last');
					} else {
						parentNode = domConstruct.create('div', {
							id: params.nodeId + 'Parent',
							class: 'parent',
							innerHTML: '&nbsp;'
						}, query('#zone-container', this.context)[0], 'last');
					}
					domNode = domConstruct.create('div', {
						id: params.nodeId + 'Child',
						label: params.label
					}, query('#zone-container', this.context)[0], 'last');
				}

				// Zone invisible ?
				if (!params.visible) {
					domStyle.set(parentNode, 'display', 'none');
					domStyle.set(domNode, 'display', 'none');
				}

				domAttr.set(domNode, 'etirable', 'yes');

				if (params.visible) {
					domStyle.set(params.nodeId + 'Parent', 'display', 'block');
					if (params.isExpandable) {
						domStyle.set(params.nodeId + 'Child', 'display', 'none');
					} else {
						domStyle.set(params.nodeId + 'Child', 'display', 'inline-block');
					}
					domStyle.set(params.nodeId + 'Child', 'width', '100%');
				} else {
					domStyle.set(params.nodeId + 'Parent', 'display', 'none');
					domStyle.set(params.nodeId + 'Child', 'display', 'none');
				}

				// On build les elements de la zone
				let nbColumn = 1;
				let lastNbColumn = 1;
				let columnInProgress = 0;
				let parentDiv = null;

				for (let i = 0; i < zone.elements.length; i++) {

					if (columnInProgress == 0) {
						parentDiv = domConstruct.create('div', {
							class: 'container-div row contribution_input_size'
						}, domNode, 'last');
					}

					let results = query('#' + zone.elements[i].nodeId, this.context);
					if (results && results[0]) {
						let node = results[0];
						node.className = zone.elements[i].className ?? '';

						let result = /colonne([2-5]|_suite)/.exec(node.className);
						if (result) {
							if (result[1] == '_suite') {
								nbColumn = lastNbColumn;
							} else {
								nbColumn = result[1];
							}
						} else {
							nbColumn = 1;
						}

						if (columnInProgress && ((nbColumn != lastNbColumn) && i > 0)) {
							parentDiv = domConstruct.create('div', {
								class: 'container-div row contribution_input_size'
							}, domNode, 'last');
							columnInProgress = 0;
						}

						domConstruct.place(node, parentDiv, 'last');

						// element visible ?
						if (zone.elements[i].visible) {
							domStyle.set(node, 'display', 'block');
						} else {
							domStyle.set(node, 'display', 'none');
							if (zone.elements[i].disabled) {
								this.disableNodes(node);
							}
						}

						columnInProgress++;
						lastNbColumn = nbColumn;

						if (nbColumn == columnInProgress) {
							columnInProgress = 0;
							nbColumn = 1;
						}
					}
				}
			},
			parseDom: function () {

				// On extrait la liste des champs du formulaire
				let currentElts = query('div[movable="yes"]', this.context);

				// Tableau des noeuds deja place
				let nodeIdPlaced = new Array();

				if (this.savedScheme) {
					for (let i = 0; i < this.savedScheme.length; i++) {
						// On recupere les parametres de la zone
						let params = lang.clone(this.savedScheme[i]);
						delete params.elements;

						// On recupere l'id de la zone
						let nodeId = this.savedScheme[i].nodeId;

						// On creer la nouvelle zone
						let newerZone = new Zone(params, nodeId, this, this.context);

						newerZone.setVisible(params.visible);
						if (params.visible) {
							newerZone.addConnectStyle();
						}


						// On ajoute les champs a la zones
						if (this.savedScheme[i].elements && this.savedScheme[i].elements.length > 0) {
							let elements = this.savedScheme[i].elements;
							for (let j = 0; j < elements.length; j++) {
								// On recupere la div qui correspond au property name
								if (elements[j].propertyName) {
									let gridFormData = this.gridForm[elements[j].propertyName.toLowerCase()];
									if (gridFormData) {
										let domElt = query(`div#${gridFormData.nodeId}`, this.context);
										if (domElt && domElt[0]) {
											newerZone.addField(domElt[0], elements[j].visible, elements[j].disabled);
											nodeIdPlaced.push(domElt[0].id);
										} else {
											console.error(`div#${elements[j].nodeId} not found !`)
										}
									}
								}
							}
						}

						this.zones.push(newerZone);
						this.nbZones++;
					}
				}


				if (currentElts && currentElts.length > 0) {

					let objectZone = this.getZoneFromId('el0');
					if (!objectZone) {
						// On créer la zone par défaut
						objectZone = new Zone({
							label: pmbDojo.messages.getMessage('grid', 'grid_js_move_default_zone')
						}, 'el0', this, this.context);
						objectZone.addConnectStyle();

						this.zones.push(objectZone);
						this.nbZones++;
					}

					// On place les elements
					for (let i = 0; i < currentElts.length; i++) {

						if (nodeIdPlaced.indexOf(currentElts[i].id) !== -1) {
							// Element deja place
							continue;
						}

						objectZone.addField(currentElts[i], true, false);
					}

					this.callZoneRefresher();
				}
			}
		});
	});