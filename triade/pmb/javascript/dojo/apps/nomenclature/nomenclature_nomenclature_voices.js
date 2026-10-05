// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_nomenclature_voices.js,v 1.7.20.1 2025/04/25 09:25:43 rtigero Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_voices_list", "apps/nomenclature/nomenclature_voice", "dojo/topic", "dojo/_base/lang", "dijit/registry"], function (declare, Voices_list, Voice, topic, lang, registry) {
	/*
	 *Classe nomenclature_nomenclature_voices. Classe representant une nomenclature de type voix
	 */
	return declare(null, {

		abbreviation: null,
		voices_list: null,
		record_formation: null,
		hash: null,
		indefinite_character: null,
		id: 0,
		voice_definition_in_progress: false,
		current_voice_code: "",
		current_voice_effective: 0,
		current_voice_effective_indefinite: false,
		current_part_effective: 0,
		current_part_effective_indefinite: false,
		sub_voices_array: null,
		end_part: false,
		errors: [],

		constructor: function (abbreviation, indefinite_character, record_formation) {
			topic.subscribe('voices_list_ui', lang.hitch(this, this.handle_events));
			this.tab_voices = new Array();
			this.init_voices_list();
			this.set_record_formation(record_formation);
			this.set_abbreviation(abbreviation);
			var indefinite_char = indefinite_character || "~";
			this.set_indefinite_character(indefinite_char);

		},
		handle_events: function (evt_type, evt_args) {
			switch (evt_type) {
				case 'ui_ready':
					if (evt_args.hash.indexOf(this.get_hash()) != -1) {
						if (this.abbreviation != "")
							this.analyze();
					}
					break;
			}
		},
		get_id: function () {
			return this.id;
		},

		set_id: function (id) {
			this.id = id;
		},

		get_abbreviation: function () {
			return this.abbreviation;
		},

		set_abbreviation: function (abbreviation) {
			this.abbreviation = abbreviation.trim();
		},

		get_voices_list: function () {
			return this.voices_list;
		},

		set_voices_list: function (voices_list) {
			this.voices_list = voices_list;
		},

		get_indefinite_character: function () {
			return this.indefinite_character;
		},

		set_indefinite_character: function (indefinite_character) {
			this.indefinite_character = indefinite_character;
		},

		get_record_formation: function () {
			return this.record_formation;
		},

		set_record_formation: function (record_formation) {
			this.record_formation = record_formation;
		},

		set_hash: function (hash) {
			this.hash = hash + "_nomenclature_voices";
		},

		get_hash: function () {
			if (!this.hash) {
				this.set_hash(this.record_formation.get_hash());
			}
			return this.hash;
		},

		analyze: function (quiet = false) {
			this.reinit_analyze();
			var state = "START";
			this.sub_voices_array = new Array();
			var part_in_def = false;
			for (var i = 0; i < this.abbreviation.length; i++) {
				var carac = this.abbreviation[i];
				//console.log('state: ', state, 'carac actuel: ', carac, "erreur: ", error, "array subvoices", this.sub_voices_array);
				switch (state) {
					case "START":
						if (!isNaN(carac)) {
							this.current_voice_effective = carac;
							state = "VOICE_EFFECTIVE";
						} else if (this.is_letter(carac)) {
							this.current_voice_code = carac;
							this.current_voice_effective_indefinite = true;
							this.voice_definition_in_progress = true;
							state = "VOICE";
						} else if (carac == this.indefinite_character) {
							this.current_voice_effective_indefinite = true;
							this.voice_definition_in_progress = true;
							state = "VOICE";
						} else {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_invalid_char')
							});
						}
						break;
					case "NEW_VOICE":
						if (!isNaN(carac)) {
							this.current_voice_effective += carac;
							state = "VOICE_EFFECTIVE";
						} else if (this.is_letter(carac)) {
							this.current_voice_code = carac;
							this.current_voice_effective_indefinite = true;
							this.voice_definition_in_progress = true;
							state = "VOICE";
						} else if (carac == this.indefinite_character) {
							this.current_voice_effective_indefinite = true;
							this.voice_definition_in_progress = true;
							state = "VOICE";
						} else {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_invalid_char')
							});
						}
						break;
					case "VOICE":
						if (this.voice_definition_in_progress) {
							if (this.is_letter(carac)) {
								this.current_voice_code += carac;
							} else {
								switch (carac) {
									case ".":
										if (this.finalize_current_voice(quiet)) {
											state = "NEW_VOICE";
										} else {
											state = "ERROR";
											this.errors.push({
												'position': i,
												'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_incorrect_effective')
											});
										}
										break;
									case "[":
										part_in_def = true;
										state = "NEW_PART";
										break;
									default:
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_invalid_char')
										});
										break;
								}
							}
						} else {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_no_voice_in_def')
							});
						}
						break;
					case "VOICE_EFFECTIVE":
						if (!isNaN(carac)) {
							this.current_voice_effective += carac;
						} else if (this.is_letter(carac)) {
							this.current_voice_code += carac;
							this.voice_definition_in_progress = true;
							//Call got new voice;
							state = "VOICE";
						} else {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_letter_needed')
							});
						}
						break;

					case "NEW_PART":
						if (part_in_def) {
							if (!isNaN(carac)) {
								this.current_part_effective += carac;
								state = "PART_EFFECTIVE";
							} else {
								switch (carac) {
									case "~":
										this.current_part_effective_indefinite = true;
										state = "PART";
										break;
									default:
										state = "ERROR";
										this.errors.push({
											'position': i,
											'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_invalid_char')
										});
										break;
								}
							}
						} else {
							state = "ERROR";
							this.errors.push({
								'position': i,
								'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_no_part_in_def')
							});
						}
						break;
					case "PART":
						switch (carac) {
							case ".":
								this.finalize_part();
								state = "NEW_PART";
								break;
							case "]":
								//End part a true
								this.end_part = true;
								part_in_def = false;
								state = "VOICE";
								break;
							default:
								state = "ERROR";
								this.errors.push({
									'position': i,
									'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_specifics_chars_only')
								});
								break;
						}
						break;
					case "PART_EFFECTIVE":
						if (!isNaN(carac)) {
							this.current_part_effective += carac;
						} else {
							switch (carac) {
								case ".":
									this.finalize_part();
									state = "NEW_PART";
									break;
								case "]":
									//End prt a true
									this.end_part = true;
									part_in_def = false;
									state = "VOICE";
									break;
								default:
									state = "ERROR";
									this.errors.push({
										'position': i,
										'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_invalid_char')
									});
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
			if (this.errors.length == 0) {
				if (this.finalize_current_voice(quiet)) {
					if (!quiet) {
						topic.publish("nomenclature_voices", 'end_analyze', { hash: this.get_hash() });
					}
				} else {
					state = "ERROR";
					this.errors.push({
						'position': i,
						'msg': registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_error_analyze_incorrect_effective')
					});
					if (!quiet) {
						topic.publish("nomenclature_voices", "error_analyze", {
							hash: this.get_hash(),
							error: this.errors
						});
					}
				}
			} else if (!quiet) {
				topic.publish("nomenclature_voices", "error_analyze", {
					hash: this.get_hash(),
					error: this.errors
				});
			}
		},
		is_valid: function () {
			this.analyze(true);
			if (this.errors.length > 0) {
				return false;
			}
			return true;
		},
		finalize_current_voice: function (quiet = false) {
			if (this.voice_definition_in_progress) {
				if (this.end_part) {
					/**
					 * Todo -> if this current_voice_effective_indefinite = true
					 * Check if one of parts have undefinite effective (return false if true) 
					 *
					 * if effective isn't undefined -> check if sum  == current voice effective
					 * or
					 * if there is at least three parts and two of them have undefinite effective
					 */
					this.finalize_part();
					var flag = true;
					if (this.current_voice_effective_indefinite) {
						if (this.sub_voices_array.indexOf("~") != -1) {
							flag = false;
						}
					} else {
						var effective_voice = parseInt(this.current_voice_effective);
						var sub_voices_effective = 0;
						for (var i = 0; i < this.sub_voices_array.length; i++) {
							if (!isNaN(this.sub_voices_array[i])) {
								sub_voices_effective = sub_voices_effective + parseInt(this.sub_voices_array[i]);
							}
						}
						if (sub_voices_effective != effective_voice) {
							flag = true;
							if (this.sub_voices_array.length > 2) {
								var nb_undef = 0;
								for (var i = 0; i < this.sub_voices_array.length; i++) {
									if (this.sub_voices_array[i] == "~")
										nb_undef++;
								}
								if (nb_undef > 1)
									flag = false;
							}
						} else {
							flag = false;
						}
					}
					if (flag)
						return false;

					for (var i = 0; i < this.sub_voices_array.length; i++) {
						var te = new Voice(this.current_voice_code, "");
						if (this.sub_voices_array[i] == 0 || this.sub_voices_array[i] == "~") {
							te.set_indefinite_effective(true);
						}
						else {
							te.set_effective(this.sub_voices_array[i]);
						}
						if (!quiet) {
							this.voices_list.add_voice(te, true);
						}
					}
					this.sub_voices_array = new Array();
					this.end_part = false;
				} else {

					var te = new Voice(this.current_voice_code, "");
					if (this.current_voice_effective_indefinite) {
						te.set_indefinite_effective(true);
					}
					else {
						te.set_effective(this.current_voice_effective);
					}
					if (!quiet) {
						this.voices_list.add_voice(te, true);
					}
					this.voice_definition_in_progress = false;
				}

				this.current_voice_code = "";
				this.current_voice_effective = 0;
				this.current_voice_effective_indefinite = false;
				return true;
			}
		},
		finalize_part: function () {
			if (this.current_part_effective_indefinite == true) {
				//onsole.log('effectif indefini pour la partie, ', this.sub_voices_array);
				this.sub_voices_array.push("~");
			} else {
				this.sub_voices_array.push(this.current_part_effective);
			}
			this.current_part_effective = 0;
			this.current_part_effective_indefinite = false;
		},
		reinit_analyze: function () {
			this.tab_voices = new Array();
			this.voice_definition_in_progress = false;
			this.current_voice_code = "";
			this.current_voice_effective = 0;
			this.current_voice_effective_indefinite = false;
			this.current_part_effective = 0;
			this.current_part_effective_indefinite = false;
			this.end_part = false;
			this.sub_voices_array = null;
			this.errors = [];
		},
		is_letter: function (letter) {
			if (letter.match(/[a-z\s]/i)) {
				return true;
			}
			return false;
		},
		init_voices_list: function () {
			this.voices_list = new Voices_list();
			this.voices_list.set_nomenclature_voices(this);
		},

		get_hidden_field_name: function (name) {
			if (name)
				return this.record_formation.get_hidden_field_name() + '[' + name + ']';
			else
				return this.record_formation.get_hidden_field_name();
		},
		calc_abbreviation: function () {
			this.voices_list.calc_abbreviation();
			this.set_abbreviation(this.voices_list.get_abbreviation());
		},
	});
});