// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_nomenclature.js,v 1.56.20.2 2025/04/28 12:52:45 rtigero Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_family", "apps/nomenclature/nomenclature_musicstand", "apps/nomenclature/nomenclature_instrument", "apps/nomenclature/nomenclature_instruments_list", "apps/nomenclature/nomenclature_workshop", "dojo/_base/lang", "dojo/json", "dojo/topic", "dijit/registry"], function (declare, Family, Musicstand, Instrument, Instruments_list, Workshop, lang, JSON, topic, registry) {
	/*
	 *Classe nomenclature_nomenclature. Classe contenant les familles d'instruments
	 */
	return declare(null, {

		families: null,
		abbreviation: "",
		family_definition_in_progress: false,
		musicstand_definition_in_progress: false,
		instrument_definition_in_progress: false,
		other_instrument_definition_in_progress: false,
		instrument: null,
		other_instrument: null,
		current_family: -1,
		current_musicstand: -1,
		musicstand_effective: 1,
		musicstand_part: 0,
		exotic_instruments_list: null,
		indefinite_character: 0,
		workshops: null,
		record_formation: null,
		workshops_abbreviation: "",
		hash: null,
		divisble_effective: null,
		errors: [],

		constructor: function (abbreviation, families_tree, indefinite_character, workshops_tree, exotics_tree, record_formation, families_notes) {
			tree = families_tree;
			if (!families_notes) families_notes = new Array();
			this.init_families(tree, families_notes);
			this.indefinite_character = indefinite_character || "~";
			this.set_record_formation(record_formation);
			this.exotic_instruments_list = new Instruments_list();
			this.exotic_instruments_list.set_nomenclature(this);
			this.init_workshops(workshops_tree);
			this.init_exotics_instruments(exotics_tree);
			this.set_abbreviation(abbreviation);
		},

		set_hash: function (hash) {
			this.hash = hash + "_nomenclature";
		},

		get_hash: function () {
			if (!this.hash) {
				this.set_hash(this.record_formation.get_hash());
			}
			return this.hash;
		},

		init_families: function (tree, notes) {
			this.families = new Array();
			for (var i = 0; i < tree.length; i++) {
				var family = new Family();
				family.set_name(tree[i].name);
				family.set_id(tree[i].id);
				family.set_nomenclature(this);
				//family.set_hash(this.get_hash());
				for (j = 0; j < tree[i].musicstands.length; j++) {
					var musicstand = new Musicstand();
					musicstand.set_id(tree[i].musicstands[j].id);
					musicstand.set_name(tree[i].musicstands[j].name);
					if (tree[i].musicstands[j].std_instrument) {
						musicstand.set_standard_instrument(new Instrument(tree[i].musicstands[j].std_instrument.code, tree[i].musicstands[j].std_instrument.name));
					}
					musicstand.set_divisable(tree[i].musicstands[j].divisable);
					musicstand.set_used_by_workshops(tree[i].musicstands[j].used_by_workshops);
					family.add_musicstand(musicstand);
				}
				if (notes[tree[i].id]) {
					family.set_note(notes[tree[i].id]);
				}
				this.add_family(family);
			}
		},

		get_record_formation_hash: function () {
			return this.record_formation.get_hash();
		},

		init_workshops: function (tree) {
			this.workshops = new Array();
			for (var i = 0; i < tree.length; i++) {
				var workshop = new Workshop(i, i + 1);
				workshop.set_label(tree[i].label);
				workshop.set_defined(tree[i].defined);
				workshop.set_id(tree[i].id);
				workshop.set_nomenclature(this);
				for (j = 0; j < tree[i].instruments.length; j++) {
					var instrument = new Instrument(tree[i].instruments[j].code, tree[i].instruments[j].name);
					instrument.set_effective(tree[i].instruments[j].effective);
					instrument.set_order(tree[i].instruments[j].order);
					instrument.set_standard(tree[i].instruments[j].standard);
					instrument.set_workshop(workshop);
					instrument.set_id_workshop_instrument(tree[i].instruments[j].id_workshop_instrument);
					workshop.instruments_list.add_instrument(instrument);
				}
				this.add_workshop(workshop);
			}
		},

		init_exotics_instruments: function (tree) {
			for (var i = 0; i < tree.length; i++) {
				var instrument = new Instrument(tree[i].code, tree[i].name);
				instrument.set_effective(tree[i].effective);
				instrument.set_order(tree[i].order);
				instrument.set_id_exotic_instrument(tree[i].id_exotic_instrument);
				if (tree[i].other)
					for (var j = 0; j < tree[i].other.length; j++) {
						var other_instrument = new Instrument(tree[i].other[j].code, tree[i].other[j].name);
						other_instrument.set_order(tree[i].other[j].order);
						other_instrument.set_id_exotic_instrument(tree[i].other[j].id_exotic_instrument);
						instrument.add_other_instrument(other_instrument);
					}
				this.exotic_instruments_list.add_instrument(instrument);
			}
		},

		set_abbreviation: function (abbreviation) {
			this.abbreviation = abbreviation.trim();
			this.analyze();
		},

		get_abbreviation: function () {
			return this.abbreviation;
		},

		set_families: function (families) {
			this.families = families;
		},

		get_families: function () {
			return this.families;
		},

		set_workshops: function (workshops) {
			this.workshops = workshops;
		},

		get_workshops: function () {
			return this.workshops;
		},

		reinit_analyze: function () {
			this.family_definition_in_progress = false;
			this.musicstand_definition_in_progress = false;
			this.instrument_definition_in_progress = false;
			this.other_instrument_definition_in_progress = false;
			this.instrument = null;
			this.other_instrument = null;
			this.current_family = -1;
			this.current_musicstand = -1;
			this.musicstand_effective = 1;
			this.errors = [];
		},

		/**
		 * Analyse de l'abreviation et mise en place des familles, des instruments et des workshops
		 * @param {*} partial 
		 * @param bool quiet Permet d'analyser sans modifier les propri�t�s, utile pour v�rifier uniquement la coh�rence de l'abbr�viation
		 */
		analyze: function (partial = false, quiet = false) {
			var partial = partial || false;
			var state = "START";
			this.reinit_analyze();

			for (var i = 0; i < this.abbreviation.length; i++) {
				/**
				 * Commentaire utile pour le debug: 
				 * console.log('STATE: ', state, 'i: ', i, 'carac: ', this.abbreviation[i]);
				 */
				//Hack pour que les espaces ne soient pas pris en compte
				//console.log('STATE: ', state, 'i: ', i, 'carac: ', this.abbreviation[i]);
				if (this.abbreviation[i] === " ") {
					continue;
				}
				var carac = this.abbreviation[i];

				switch (state) {

					case "START":
					case "NEW_FAMILY":
						//on attend un chiffre ou le caract�re ind�fini
						if (!isNaN(carac) || carac == this.indefinite_character) {
							if (this.family_definition_in_progress) {
								state = "ERROR";
								this.errors.push({
									'position': i,
									'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_family_def')
								});
							} else {
								if (!this.get_next_family()) {
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_end_family_def')
									});
								} else {
									if (this.musicstand_definition_in_progress) {
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_musicstand_def')
										});
									} else {
										if (!this.get_next_musicstand()) {
											state = "ERROR";
											this.errors.push({
												'position': i,
												'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_end_musicstand_def')
											});
										} else {
											this.musicstand_effective = carac;
											state = "MUSICSTAND";
										}
									}
								}
							}
						} else {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_no_numeric')
							});
						}
						break;
					case "MUSICSTAND":
						if (!this.musicstand_definition_in_progress) {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_no_musicstand_def')
							});
						} else {
							if (!isNaN(carac)) {
								if (this.musicstand_effective != this.indefinite_character) {
									this.musicstand_effective += carac;
								} else {
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze')
									});
								}
							} else {
								switch (carac) {
									case this.indefinite_character:
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_musicstand_undetermined')
										});
										break;
									//Fin de la famille
									case '-':
										if (this.finalize_current_family(quiet)) {
											state = "NEW_FAMILY";
										} else {
											state = "ERROR";
											this.errors.push({
												'position': i,
												'msg': this.families[this.current_family].get_error_message()
											});
										}
										break;
									//Fin de pupitre
									case '.':
										if (this.finalize_current_musicstand(quiet)) {
											state = "NEW_MUSICSTAND";
										} else {
											state = "ERROR";
											this.errors.push({
												'position': i,
												'msg': this.families[this.current_family].get_musicstand(this.current_musicstand).get_error_message()
											});
										}
										break;
									case '[':
										state = "NEW_INSTRUMENT";
										break;
									case ']':
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_no_musicstand_def')
										});
										break;
									default:
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_illegal_character')
										});
										break;
								}
							}
						}
						break;
					case "NEW_MUSICSTAND":
						//Pupitre en cours de definition, on a un probleme ?!
						if (this.musicstand_definition_in_progress) {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_musicstand_def')
							});
						} else {
							if (!isNaN(carac)) {
								if (!this.get_next_musicstand()) {
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_end_musicstand_def')
									});
								} else {
									this.musicstand_effective = carac;
									state = "MUSICSTAND";
								}
							} else if (carac == this.indefinite_character) { //Cas d'un ateliers ind�fini
								if (!this.get_next_musicstand()) {
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_end_musicstand_def')
									});
								} else {
									this.musicstand_effective = carac;
									state = "MUSICSTAND";
								}
								/**
								 * TODO: new musicstand indefinite effective
								 */
							} else {
								state = "ERROR";
								this.errors.push({
									'position': i,
									'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_no_numeric')
								});
							}
						}
						break;
					case "NEW_INSTRUMENT":
						if (this.instrument_definition_in_progress) {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_instrument_def')
							});
						} else {
							switch (carac) {
								case ']':
								case '.':
								case '-':
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze')
									});
									break;
								case '[':
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_musicstand_detail_def')
									});
									break;
								default:
									if (this.families[this.current_family].get_musicstand(this.current_musicstand).get_divisable()) {
										if (!isNaN(carac)) {
											this.instrument = this.get_standard_instrument();
											this.instrument.set_effective(carac);
											this.instrument.set_indefinite_effective(false);
										} else {
											switch (carac) {
												case this.indefinite_character:
													this.instrument = this.get_standard_instrument();
													this.instrument.set_effective(0);
													this.instrument.set_indefinite_effective(true);
													break;
												default:
													this.instrument = this.get_no_standard_instrument();
													this.instrument.set_code(carac);
													break;

											}
										}
										state = "INSTRUMENT_FROM_DIVISABLE";
									} else if (!isNaN(carac)) {
										this.instrument = this.get_standard_instrument();
										//on regarde le pupitre...
										if (this.families[this.current_family].get_musicstand(this.current_musicstand).get_divisable()) {
											this.musicstand_part++;
											this.instrument.set_effective(carac);
											this.instrument.set_part(this.musicstand_part);
										}
										state = "INSTRUMENT_STANDARD";
									} else {
										this.instrument = this.get_no_standard_instrument();
										this.instrument.set_code(carac);
										state = "INSTRUMENT_NO_STANDARD";
									}
									break;
							}
						}
						break;
					case "INSTRUMENT_FROM_DIVISABLE":
						if (!this.instrument_definition_in_progress) {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_no_instrument_def')
							});
						} else {
							if (!isNaN(carac)) {
								if (this.instrument.is_standard()) { //Si l'instrument est standard et que l'on a un entier, alors il s'agit de l'effectif
									this.instrument.set_effective(this.instrument.get_effective() + carac);
								} else { //Si l'instrument est d�j� d�clar� en tant qu'instrument non standard alors on concat�ne le caract�re � son code
									this.instrument.set_code(this.instrument.get_code() + carac);
								}
							} else {
								switch (carac) {
									case '-':
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_close_musicstand_detail')
										});
										break;
									case '[':
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_musicstand_detail_def')
										});
										break;
									case '.':
										if (this.finalize_current_instrument(quiet)) {
											state = "NEW_INSTRUMENT";
										} else {
											state = "ERROR";
											this.errors.push({
												'position': i,
												'msg': this.instrument.error_message
											});
										}
										break;
									case ']':
										state = "MUSICSTAND";
										break;
									default:
										/**
										 * On a un caract�re donc, c'est un instrument non standard (2Vn2 par exemple)
										 */
										if (this.instrument.is_standard()) {
											/**
											 * L'instrument est encore d�clar� en tant que standard 
											 * donc on �crase le code et on le passe en non standard
											 */
											this.instrument.set_standard(false);
											this.instrument.set_code(carac);
										} else {
											/**
											 * On concat�ne le code 
											 */
											this.instrument.set_code(this.instrument.get_code() + carac);
										}
										break;
								}
							}
						}
						break;
					case "INSTRUMENT_STANDARD":
						if (!this.instrument_definition_in_progress) {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_no_instrument_def')
							});
						} else {
							if (!isNaN(carac)) {
								if (this.families[this.current_family].get_musicstand(this.current_musicstand).get_divisable()) {
									this.instrument.set_effective(this.instrument.get_effective() + carac);
								}
							} else {
								switch (carac) {
									case '-':
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_close_musicstand_detail')
										});
										break;
									case '[':
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_musicstand_detail_def')
										});
										break;
									case '.':
										if (this.finalize_current_instrument(quiet)) {
											state = "NEW_INSTRUMENT";
										} else {
											state = "ERROR";
											this.errors.push({
												'position': i,
												'msg': this.instrument.error_message()
											});
										}
										break;
									case ']':
										state = "MUSICSTAND";
										break;
									case '/':
										state = "NEW_OTHER_INSTRUMENT";
										break;
								}
							}
						}
						break;
					case "INSTRUMENT_NO_STANDARD":
						if (!this.instrument_definition_in_progress) {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_no_instrument_def')
							});
						} else {
							switch (carac) {
								case '-':
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_close_musicstand_detail')
									});
									break;
								case '.':
									if (this.finalize_current_instrument(quiet)) {
										state = "NEW_INSTRUMENT";
									} else {
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': this.instrument.error_message()
										});
									}
									break;
								case '[':
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_musicstand_detail_def')
									});
									break;
								case ']':
									state = "MUSICSTAND";
									break;
								case '/':
									state = "NEW_OTHER_INSTRUMENT";
									break;
								default:
									this.instrument.set_code(this.instrument.get_code() + carac);
									break;
							}
						}
						break;
					case "NEW_OTHER_INSTRUMENT":
						if (this.other_instrument_definition_in_progress) {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_already_other_instrument_def')
							});
						} else {
							switch (carac) {
								case '/':
								case ']':
								case '.':
								case '-':
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze')
									});
									break;
								default:
									this.other_instrument = this.get_other_instrument();
									this.other_instrument.set_code(this.other_instrument.get_code() + carac);
									state = "OTHER_INSTRUMENT";
									break;
							}
						}
						break;
					case "OTHER_INSTRUMENT":
						if (!this.other_instrument_definition_in_progress) {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_no_other_instrument_def')
							});
						} else {
							switch (carac) {
								case '-':
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_analyze_close_musicstand_detail')
									});
									break;
								case '.':
									if (this.finalize_current_instrument(quiet)) {
										state = "NEW_INSTRUMENT";
									} else {
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': this.instrument.error_message()
										});
									}
									break;
								case ']':
									state = "MUSICSTAND";
									break;
								case '/':
									this.finalize_current_other_instrument(quiet);
									state = "NEW_OTHER_INSTRUMENT";
									break;
								default:
									this.other_instrument.set_code(this.other_instrument.get_code() + carac);
									break;
							}
						}
						break;
					case "ERROR":
					default:
						continue;
						break;
				}
			}
			if (state == "ERROR") {
				if (!quiet) {
					topic.publish("nomenclature", "error_analyze", {
						hash: this.get_hash(),
						error: this.errors
					});
				}
			} else {
				if (!this.finalize_current_family(quiet)) {
					error.push({
						'position': i,
						'msg': this.families[this.current_family].get_error_message()
					});
					if (!quiet) {
						topic.publish("nomenclature", "error_analyze", {
							hash: this.get_hash(),
							error: this.errors
						});
					}
				} else {
					if (!quiet) {
						topic.publish("nomenclature", "end_analyze", {
							hash: this.get_hash()
						});
					}
				}
			}
		},
		is_valid: function () {
			this.analyze(false, true);
			if (this.errors.length > 0) {
				return false;
			}
			return true;
		},
		get_standard_instrument: function () {
			if (!this.instrument_definition_in_progress) {
				this.instrument_definition_in_progress = true;
				return lang.clone(this.families[this.current_family].get_musicstand(this.current_musicstand).get_standard_instrument());
			}
		},
		get_no_standard_instrument: function () {
			if (!this.instrument_definition_in_progress) {
				this.instrument_definition_in_progress = true;
				var no_std_inst = new Instrument("", "");
				no_std_inst.set_standard(false);
				return no_std_inst;
			}
		},
		get_other_instrument: function () {
			if (!this.other_instrument_definition_in_progress) {
				this.other_instrument_definition_in_progress = true;
				var no_std_inst = new Instrument("", "");
				no_std_inst.set_standard(false);
				return no_std_inst;
			}
		},
		finalize_current_family: function (quiet = false) {
			this.finalize_current_musicstand(quiet);
			if (!this.families[this.current_family].check()) {
				this.error_message = this.families[this.current_family].get_musicstand(this.current_musicstand).get_error_message();
				return false;
			}
			this.family_definition_in_progress = false;
			return true;
		},
		finalize_current_musicstand: function (quiet = false) {
			if (this.musicstand_definition_in_progress) {
				if (this.finalize_current_instrument(quiet)) {
					//on regarde si le seul instrument est standard et dispose d'un effectif > 1
					//console.log(this.families[this.current_family].get_musicstand(this.current_musicstand).get_instruments());
					if ((!this.families[this.current_family].get_musicstand(this.current_musicstand).get_used_by_workshops())
						&& (this.families[this.current_family].get_musicstand(this.current_musicstand).get_instruments().length == 1)
						&& (this.families[this.current_family].get_musicstand(this.current_musicstand).get_instruments()[0].get_effective() > 1)
						&& (!this.families[this.current_family].get_musicstand(this.current_musicstand).get_divisable())) {
						var instrument = this.families[this.current_family].get_musicstand(this.current_musicstand).get_instruments()[0];
						if (instrument.is_standard()) {
							this.families[this.current_family].get_musicstand(this.current_musicstand).set_instruments(new Array());
							for (var j = 0; j < instrument.get_effective(); j++) {
								var current = lang.clone(this.families[this.current_family].get_musicstand(this.current_musicstand).get_standard_instrument());
								current.set_order(j + 1);
								current.set_effective(1);
								if (!quiet) {
									this.families[this.current_family].get_musicstand(this.current_musicstand).add_instrument(current);
								}
							}
						}
					}
					if (this.musicstand_effective == this.indefinite_character) {
						this.families[this.current_family].get_musicstand(this.current_musicstand).set_effective(0);
						this.families[this.current_family].get_musicstand(this.current_musicstand).set_indefinite_effective(true);
					} else {
						this.families[this.current_family].get_musicstand(this.current_musicstand).set_effective(this.musicstand_effective);
						this.families[this.current_family].get_musicstand(this.current_musicstand).set_indefinite_effective(false);
					}
					if (!this.families[this.current_family].get_musicstand(this.current_musicstand).check()) {
						return false;
					}
					//r�initialisation
					this.musicstand_effective = 1;
					this.musicstand_definition_in_progress = false;
				} else {
					this.error_message = this.instrument.error_message();
					return false;
				}
			}
			return true
		},
		finalize_current_instrument: function (quiet = false) {
			if (this.instrument_definition_in_progress) {
				this.finalize_current_other_instrument(quiet);
				if (this.instrument.check()) {
					if (!quiet) {
						this.families[this.current_family].get_musicstand(this.current_musicstand).add_instrument(this.instrument, true);
					}
				} else {
					return false;
				}
				this.instrument = null;
				this.instrument_definition_in_progress = false;
			} else if (parseInt(this.musicstand_effective) > 0 || this.musicstand_effective == this.indefinite_character) {
				if (this.families[this.current_family].get_musicstand(this.current_musicstand).get_used_by_workshops()) {
					this.instrument = new Instrument("", "", "");
				} else {
					//cas ou seul l'effectif est d�fini, on prend alors l'instrument standard avec l'effectif correspondant
					this.instrument = lang.clone(this.families[this.current_family].get_musicstand(this.current_musicstand).get_standard_instrument());
				}
				if (this.musicstand_effective == this.indefinite_character) {
					this.instrument.set_effective(0);
					this.instrument.set_indefinite_effective(true);
				} else {
					this.instrument.set_effective(this.musicstand_effective);
				}

				this.instrument_definition_in_progress = true;
				this.finalize_current_instrument(quiet);
			}
			return true;
		},
		finalize_current_other_instrument: function (quiet = false) {
			if (this.other_instrument_definition_in_progress) {
				if (!quiet) {
					this.instrument.add_other_instrument(this.other_instrument);
				}
				this.other_instrument = null;
				this.other_instrument_definition_in_progress = false
			}
			return true;
		},
		get_next_family: function () {
			if (this.families.length > this.current_family) {
				this.family_definition_in_progress = true;
				this.current_family++;
				this.current_musicstand = -1;
				return true;
			} else {
				this.family_definition_in_progress = false;
			}
			return false;
		},
		get_next_musicstand: function () {
			if (this.families[this.current_family].get_musicstands().length > this.current_musicstand) {
				this.musicstand_definition_in_progress = true;
				this.musicstand_effective = 1;
				this.current_musicstand++;
				this.musicstand_part = 0;
				return true;
			} else {
				this.musicstand_definition_in_progress = false;
				return false;
			}
		},
		check: function () {

		},
		/**
		 * Tri tableau des instrument selon l'order d�fini en property
		 */
		sort_array: function (a, b) {
			if (a.get_order() < b.get_order()) {
				return -1;
			}
			if (a.get_order() == b.get_order()) {
				return 0;
			}
			if (a.get_order() > b.get_order()) {
				return 1;
			}
		},
		add_family: function (family) {
			if (this.families == null) {
				this.families = new Array();
			}
			this.families.push(family);
		},
		add_workshop: function (workshop) {
			if (this.workshops == null) {
				this.workshops = new Array();
			}
			this.workshops.push(workshop);
		},
		delete_workshop: function (order) {
			for (var i = 0; i < this.workshops.length; i++) {
				if (this.workshops[i].get_order() == order) {
					this.workshops.splice(i, 1);
					break;
				}
			}
			for (var i = 0; i < this.workshops.length; i++) {
				if (this.workshops[i].get_order() > order) {
					this.workshops[i].set_order(this.workshops[i].get_order() - 1);
				}
			}
		},
		calc_abbreviation: function () {
			var abbreviation = "";
			for (var i = 0; i < this.families.length; i++) {
				this.families[i].calc_abbreviation();
				abbreviation += this.families[i].get_abbreviation();
				if (i < this.families.length - 1)
					abbreviation += " - ";
			}
			this.abbreviation = abbreviation;
		},

		set_workshops_abbreviation: function (workshops_abbreviation) {
			this.workshops_abbreviation = workshops_abbreviation.trim();
		},

		get_workshops_abbreviation: function () {
			return this.workshops_abbreviation;
		},
		calc_workshops_abbreviation: function () {
			var workshops_abbreviation = "";
			var range_instruments = new Array();
			for (var i = 0; i < this.workshops.length; i++) {
				for (var j = 0; j < this.workshops[i].instruments_list.instruments.length; j++) {
					if (range_instruments[this.workshops[i].instruments_list.instruments[j]['code']]) {
						range_instruments[this.workshops[i].instruments_list.instruments[j]['code']] = range_instruments[this.workshops[i].instruments_list.instruments[j]['code']] + this.workshops[i].instruments_list.instruments[j]['effective'];
					} else {
						range_instruments[this.workshops[i].instruments_list.instruments[j]['code']] = this.workshops[i].instruments_list.instruments[j]['effective'];
					}
				}
			}
			for (instrument_code in range_instruments) {
				if (workshops_abbreviation != "") workshops_abbreviation += " / ";
				workshops_abbreviation += range_instruments[instrument_code] + " " + registry.byId('nomenclature_datastore').get_instrument_name(instrument_code);
			}
			this.workshops_abbreviation = workshops_abbreviation;
		},
		get_record_formation: function () {
			return this.record_formation;
		},

		set_record_formation: function (record_formation) {
			this.record_formation = record_formation;
		},
		get_hidden_field_name: function (name) {
			if (name)
				return this.record_formation.get_hidden_field_name() + '[' + name + ']';
			else
				return this.record_formation.get_hidden_field_name();
		},
	});
});