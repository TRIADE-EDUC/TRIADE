// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContributionFormEdit.js,v 1.16 2023/10/31 13:45:17 qvarin Exp $

define([
	'dojo/_base/declare',
	'dojo/_base/lang',
	'apps/pmb/gridform/FormEdit',
	'dojo/dom',
	'dojo/dom-attr',
	'dojo/query',
	'dojo/dom-style',
	'dojo/dom-construct',
	'apps/pmb/gridform/Zone',
	'dojo/request/xhr',
], function (declare, lang, FormEdit, dom, domAttr, query, domStyle, domConstruct, Zone, xhr) {
	return declare(FormEdit, {
		initialized: null,
		resetGrid: false,
		gridForm: {},
		constructor: function () {
			this.initialized = false;
			domConstruct.destroy(this.btnEdit);
		},
		saveCallback: function (response) {
			if (response.status) {
				if (this.btnSave && !this.resetGrid) {
					let returnURL = dom.byId('return_url').value;
					if (returnURL) {
						window.location = returnURL;
					}
				}

				if (this.resetGrid) {
					this.resetGrid = false;
				}
			} else {
				alert(pmbDojo.messages.getMessage('grid', 'grid_js_move_saved_error'));
			}
		},
		initializationCallback: function () {
			if (this.module == "catalog") {
				this.state = 'inedit';
			}
			this.btnEditCallback();
			this.initialized = true;
		},
		btnEditCallback: function (evt) {
			switch (this.state) {
				case 'std':
					this.state = 'inedit';
					let disableButtonsForm = query('#form-contenu input[type=button]');
					if (disableButtonsForm.length) {
						for (let i; i < disableButtonsForm.length; i++) {
							if (domAttr.get(disableButtonsForm[i], 'onclick')) {
								domAttr.remove(disableButtonsForm[i], 'onclick');
							}
							domStyle.set(disableButtonsForm[i], 'color', '#aaa');
						}
					}

					let disableImgsForm = query('#form-contenu img[class=img_plus]');
					if (disableImgsForm.length) {
						for (let i; i < disableImgsForm.length; i++) {
							disableImgsForm[i].remove();
						}
					}
					this.parseDom();
					if (this.btnEdit) {
						domAttr.set(this.btnEdit, 'value', pmbDojo.messages.getMessage('grid', 'grid_js_move_back'));
					}
					break;
				case 'inedit':
					this.state = 'std';
					this.unparseDom();
					this.getDatas();
					if (this.btnEdit) {
						domAttr.set(this.btnEdit, 'value', pmbDojo.messages.getMessage('grid', 'grid_js_move_edit_format'));
					}
					break;
			}
		},
		btnOriginFormatCallback: function (evt) {
			this.state = 'std';
			this.resetGrid = true;
			this.computedElements = new Array();
			const promise = new Promise((resolve, reject) => {
				resolve(this.inherited(arguments));
			});

			promise.then(() => {
				this.initialized = false;
				this.initializationCallback();
			});
		},
		removeJsDom: function () {
			var cleanElts = query('#zone-container > div');
			for (let i = 0; i < this.originalFormat.length; i++) {
				domConstruct.place(dom.byId(this.originalFormat[i].id), dom.byId('zone-container'), 'last');
				dom.byId(this.originalFormat[i].id).className = this.originalFormat[i].class;
			}

			for (let i = 0; i < cleanElts.length; i++) {
				if (cleanElts[i].getAttribute('movable') == null) {
					domConstruct.destroy(cleanElts[i]);
				}
			}
			this.zones = new Array();
			if (this.btnEdit) this.signalEditFormat = on(this.btnEdit, 'click', lang.hitch(this, this.btnEditCallback));
		},

		buildGrid: function (data) {
			// Suppression des noeuds contenant l'editeur HTML
			let text_areas_with_tinymce = this.removeTinyEditor();

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
			let alreadyPlaced = new Object();

			// On extrait la liste des champs du formulaire de contribution
			let currentElts = query('div[movable="yes"]', this.context);
			for (let i = 0; i < currentElts.length; i++) {
				let node = query('div#' + currentElts[i].id, this.context);
				if (node[0] && domAttr.has(node[0], "data-pmb-propertyname")) {
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
				for (var i = 0; i < this.savedScheme.length; i++) {
					let zone = lang.clone(this.savedScheme[i]);
					zone.elements = new Array();

					if (zone.nodeId == "el0") {
						indexDefaultGrid = grid.length;
					}

					for (let j = 0; j < this.savedScheme[i].elements.length; j++) {
						let element = this.savedScheme[i].elements[j];

						// On vérifi si le champs est toujours présent
						if (!this.gridForm[element?.propertyName?.toLowerCase()]) {
							continue;
						}

						let gridFormData = this.gridForm[element.propertyName.toLowerCase()];
						alreadyPlaced[element.propertyName.toLowerCase()] = true;

						let field = {
							"propertyName": element.propertyName,
							"nodeId": gridFormData.nodeId,
							"className": element.className,
							"disabled": element.disabled,
							"visible": element.visible,
						};

						zone.elements.push(field);
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
				if (!alreadyPlaced[propertyName.toLowerCase()]) {
					let field = {
						"propertyName": this.gridForm[propertyName].propertyName,
						"nodeId": this.gridForm[propertyName].nodeId,
						"className": this.gridForm[propertyName].className,
						"disabled": true
					};

					if (this.module == "catalog") {
						field.visible = true;
						field.disabled = false;
					}

					grid[indexDefaultGrid].elements.push(field);
				}
			}

			this.getDefaultZones();

			// On places tout les éléments dans l'ordre
			if (grid) {

				// Zone par défaut
				if (!this.getZoneFromId('el0')) {
					this.addDefaultDOMZone('el0');
				}

				// On créer toutes les autres zones
				if (this.originalZones.length) {
					for (let i = 0; i < this.originalZones.length; i++) {
						if (!this.getZoneFromId(this.originalZones[i].id)) {
							this.addDefaultDOMZone(this.originalZones[i].id);
						}
					}
				}

				// On contruit la grid
				for (let i = 0; i < grid.length; i++) {
					this.buildZone(grid[i], []);
				}
			}

			// Ajout des noeuds contenant l'editeur HTML
			this.addTinyEditor(text_areas_with_tinymce);

			// Traitement terminé, on nettoye
			this.cleanRecursiveElts(cleanElts, ' > div:not([movable="yes"])');

			activeElement.focus();

			if (!this.initialized) {
				this.initializationCallback();
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

				// Zone par défaut
				let objectZone = this.getZoneFromId('el0');
				if (!objectZone) {
					objectZone = this.addDefaultZone('el0');
				}

				if (!objectZone) {
					console.error('zone el0 not found');
				} else {
					for (let j = 0; j < currentElts.length; j++) {

						if (nodeIdPlaced.indexOf(currentElts[j].id) !== -1) {
							// Element deja place
							continue;
						}

						if (this.getZoneIdFromDOMElement(currentElts[j]) == 'el0') {
							objectZone.addField(currentElts[j], true, false);
							nodeIdPlaced.push(currentElts[j].id);
						}
					}
				}

				for (let i = 0; i < this.originalZones.length; i++) {

					let objectZone = this.getZoneFromId(this.originalZones[i].id);
					if (!objectZone) {
						objectZone = this.addDefaultZone(this.originalZones[i].id);
					}

					if (!objectZone) {
						console.error(`zone ${this.originalZones[i].id} not found`)
						continue;
					}

					for (let j = 0; j < currentElts.length; j++) {

						if (nodeIdPlaced.indexOf(currentElts[j].id) !== -1) {
							// Element deja place
							continue;
						}

						if (this.getZoneIdFromDOMElement(currentElts[j]) == this.originalZones[i].id) {
							if (!objectZone.getElementFromId(currentElts[j].id)) {
								objectZone.addField(currentElts[j], true, false);
								nodeIdPlaced.push(currentElts[j].id);
							}
						}
					}
				}
			}
			this.callZoneRefresher();
		},
		getStruct: function () {
			let JSONInformations = new Array();
			if (this.zones.length) {
				for (let i = 0; i < this.zones.length; i++) {
					let zoneInformations = this.zones[i].getJSONInformations();
					for (let j = 0; j < this.zones[i].elements.length; j++) {
						let node = query('div#' + this.zones[i].elements[j].nodeId, this.context);
						if (node && node[0] && domAttr.has(node[0], "data-pmb-propertyname")) {
							zoneInformations.elements[j].propertyName = domAttr.get(node[0], 'data-pmb-propertyName');
						}
					}
					JSONInformations.push(zoneInformations);
				}
			}
			if (!this.type) {
				let currentUrl = window.location;
				this.type = /categ=(\w+)&?/g.exec(currentUrl)[1];
				if (this.type == 'authperso') {
					let authPerso = /id_authperso=(\w+)&?/g.exec(currentUrl)[1];
					this.type += '_' + authPerso;
				}
			}
			return { zones: JSONInformations, genericType: this.type };
		},
		launchXhrSave: function (datas) {
			xhr("./ajax.php?module=modelling&categ=contribution_area&sub=form_grid&action=save", {
				handleAs: "json",
				method: 'post',
				data: 'datas=' + JSON.stringify(datas)
			}).then(lang.hitch(this, this.saveCallback));
		},
		getDatas: function () {
			if (!this.type) {
				let currentUrl = window.location;
				this.type = /categ=(\w+)&?/g.exec(currentUrl)[1];
				switch (this.type) {
					case 'authperso':
						let authPerso = /id_authperso=(\w+)&?/g.exec(currentUrl)[1];
						this.type += '_' + authPerso;
						break;
					case 'contribution_area':
						let formId = /form_id=(\w+)&?/g.exec(currentUrl)[1];
						this.type += '_form_' + formId;
						break;
				}
			}
			let returnedInfos = { genericType: this.type, genericSign: this.getSign() };
			xhr("./ajax.php?module=modelling&categ=contribution_area&sub=form_grid&action=get_datas", {
				handleAs: "json",
				method: 'post',
				data: 'datas=' + JSON.stringify(returnedInfos)
			}).then(lang.hitch(this, this.getDatasCallback));
		},
	});
});