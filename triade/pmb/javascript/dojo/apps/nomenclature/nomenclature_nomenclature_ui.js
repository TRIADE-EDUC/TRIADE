// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_nomenclature_ui.js,v 1.59.8.1 2025/04/25 09:25:43 rtigero Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_nomenclature", "apps/nomenclature/nomenclature_family_ui", "apps/nomenclature/nomenclature_workshops_ui", "dojo/on", "dojo/dom-construct", "dojo/_base/lang", "dojo/dom", "dojo/topic", "dojo/dom-style", "apps/nomenclature/nomenclature_exotic_instruments_ui", "dijit/registry", "dijit/_WidgetBase", "dojo/request/xhr", "dijit/ProgressBar", "apps/pmb/PMBDialog"], function (declare, Nomenclature, Family_ui, Workshops_ui, on, domConstruct, lang, dom, topic, domStyle, Exotic_instruments_ui, registry, _WidgetBase, xhr, ProgressBar, Dialog) {
	/*
	 *Classe nomenclature_nomenclature_ui. Classe g�n�rant la partie du formulaire li�e a une nomenclature
	 */
	return declare("nomenclature_nomenclature_ui", [_WidgetBase], {

		/**
		 * 
		 * La classe va prendre en param�tre une instance de l'objet dojo nomenclature
		 * Elle va la parser, instancier les classes ad�quates, et ces classes vont g�n�rer le formulaire.
		 */
		nomenclature: null,
		dom_node: null,
		families: null,
		input_abbrege: null,
		families_node: null,
		sync_from_abbreviation_allowed: false,
		sync_from_details_allowed: false,
		total_families: 0,
		exotic_instruments_ui: null,
		workshops_ui: null,
		workshops_node: null,
		families_ready: false,
		workshops_flag_ready: false,
		exotic_instruments_flag_ready: false,
		main_node: null,
		span_abbreviation: null,
		hidden_abbr: null,
		dialog_sub_manifestations: null,
		total_instruments: 0,
		constructor: function (params) {
			if (arguments[0].nomenclature_abbr) {
				this.nomenclature = new Nomenclature(arguments[0].nomenclature_abbr, arguments[0].nomenclature_tree, arguments[0].nomenclature_indefinite_character, arguments[0].workshop_tree, arguments[0].instruments);
			}
			this.families = new Array();
			this.own(topic.subscribe('family_ui', lang.hitch(this, this.handle_events)));
			this.own(topic.subscribe('workshop_ui', lang.hitch(this, this.handle_events)));
			this.own(topic.subscribe('exotic_instruments_ui', lang.hitch(this, this.handle_events)));
			this.own(topic.subscribe("instrument_ui", lang.hitch(this, this.handle_events)));
			this.own(topic.subscribe("nomenclature", lang.hitch(this, this.handle_events)));
			this.own(topic.subscribe("workshops_ui", lang.hitch(this, this.handle_events)));
		},

		handle_events: function (evt_type, evt_args) {
			switch (evt_type) {
				case "error_analyze":
					if (evt_args.hash == this.nomenclature.get_hash()) {
						this.show_analize_error(evt_args.error);
					}
				case "instru_changed":
					if (evt_args.nomenclature_hash == this.nomenclature.get_hash()) {
						this.allow_sync_from_details();
					}
					break;
				case "family_ready":
					if (evt_args.nomenclature_hash == this.nomenclature.get_hash()) {
						this.family_ready();
					}
					break;
				case "family_changed":
					if (evt_args.nomenclature_hash == this.nomenclature.get_hash()) {
						this.allow_sync_from_details();
					}
					break;
				case "workshops_delete":
					if (evt_args.nomenclature_hash == this.nomenclature.get_hash()) {
						if (this.workshops_ui.workshops.length) {
							var workshops_indice_max = (this.workshops_ui.workshops.length) - 1;
							for (var i = workshops_indice_max; i >= 0; i--) {
								this.workshops_ui.delete_workshop_event(this.nomenclature.workshops[i].get_order());
							}
							this.workshops_ui.generate_inputs();
							this.workshops_ui.maj_abbreviation();
						}
					}
					break;
				case "workshops_changed":
					if (evt_args.nomenclature_hash == this.nomenclature.get_hash()) {
						if (evt_args.is_undefined) {
							if (this.workshops_ui.workshops.length == 0) {
								this.workshops_ui.add_workshop(true);
							}
						} else if (evt_args.effective) {
							if (evt_args.effective > this.workshops_ui.workshops.length) {
								for (var i = this.workshops_ui.workshops.length; i < evt_args.effective; i++) {
									this.workshops_ui.add_workshop();
								}
							} else if (evt_args.effective < this.workshops_ui.workshops.length) {
								for (var i = (this.workshops_ui.workshops.length) - 1; i >= evt_args.effective; i--) {
									this.workshops_ui.delete_workshop_event(this.nomenclature.workshops[i].get_order());
								}
							}
						}
					}
					break;
				case "exotic_instruments_ready":
					if (evt_args.nomenclature_hash == this.nomenclature.get_hash()) {
						this.exotic_instruments_ready();
					}
					break;
				case "workshops_ready":
					if (evt_args.nomenclature_hash == this.nomenclature.get_hash()) {
						this.workshops_ready();
					}
					break;
				case "submanifestation_created":
					if (evt_args.hash.indexOf(this.nomenclature.get_hash()) != -1) {
						this.update_progress_bar(evt_args);
					}
					break;
				case "drag_and_drop_end":
					if (evt_args.hash.indexOf(this.nomenclature.get_hash()) != -1) {
						// l'abbreviation a changé on sync et on save
						this.sync_from_details();
						this.ajax_save();
						topic.publish('nomenclature_ui', 'sync_and_save_end', { hash: this.nomenclature.get_hash() });
						this.reorder_children();
					}
					break;
			}
		},
		reorder_children: function () {
			xhr("./ajax.php?module=ajax&categ=nomenclature&sub=nomenclature_record_formations&action=reorder_children&id_parent=" + this.nomenclature.record_formation.get_record(), {
				handleAs: "json",
				method: "GET",
				sync: true
			});
		},
		show_analize_error: function (error, purge = true) {
			if (purge) {
				this.purge_instruments();
			}
			domConstruct.empty(this.error_node);

			var abbr = "";
			for (var i = 0; i < this.nomenclature.get_abbreviation().length; i++) {
				if (error[0].position == i) {
					abbr += "<span style='color:red;font-weight:bold;'>" + this.nomenclature.get_abbreviation()[i] + "</span>";
				} else {
					abbr += this.nomenclature.get_abbreviation()[i];
				}
			}
			domConstruct.create('div', {
				class: "row",
				style: "height:40px;",
				innerHTML: "<div class='colonne10'><img align='left' src='" + pmbDojo.images.getImage('error.gif') + "'></div><div class='colonne80'><b>" + registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_for_analyze') + " : </b>" + abbr + "<br>" + error[0].msg + "</div>"
			}, this.error_node);
		},

		buildRendering: function () {
			this.inherited(arguments);
			this.build_form();
		},

		build_form: function () {
			/***
			 * TODO: Create collapsable nodes
			 */

			/*******************************/
			//console.log('node id', this.dom_node.id);
			domConstruct.create('div', { class: 'row' }, this.get_dom_node());
			var noeud_princ = domConstruct.create('div', {
				id: this.get_dom_node().id + '_nomenclature',
				class: 'notice-parent'
			}, this.get_dom_node());
			/*
			 * Cr�ation d'un code type "pmb" permettant de d�plier les familles en cliquant sur une image
			 */
			var img_plus = domConstruct.create('img', {
				id: this.get_dom_node().id + '_nomenclatureImg',
				class: 'img_plus',
				hspace: '3',
				border: '0',
				onclick: 'expandBase(\'' + this.get_dom_node().id + '_nomenclature\', true); return false;',
				title:'d\351tail',
				src: pmbDojo.images.getImage('plus.gif')
			}, noeud_princ);

			var span = domConstruct.create('span', { class: 'notice-heada', innerHTML: registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_label') + ' -' }, noeud_princ);
			this.span_abbreviation = domConstruct.create('span', { innerHTML: ' ' + this.nomenclature.get_abbreviation() }, span);
			//this.abbreviation_node = domConstruct.create('span', {innerHTML:' '+this.family.get_abbreviation()}, span);
			this.main_node = domConstruct.create('div', {
				id: this.get_dom_node().id + '_nomenclatureChild',
				startOpen: "Yes",
				class: 'notice-child',
				callback: "recalc_recept",
				style: {
					marginBottom: '6px',
					display: 'none',
					width: '94%',
				}
			}, this.get_dom_node());

			/*******************************/



			var dom_child = domConstruct.create('div', {
				id: this.get_dom_node().id + "_nomenclature_control"
			}, this.get_main_node());

			this.input_abbrege = domConstruct.create('input', {
				id: this.get_dom_node().id + '_input_abbrege',
				class: 'saisie-80em',
				type: 'text',
				value: this.nomenclature.get_abbreviation()
			}, dom_child);

			var button_insert_tilde = domConstruct.create('input', {
				id: this.get_dom_node().id + '_button_insert_tilde',
				value: '~',
				type: 'button'
			}, dom_child);

			var button_sync_details = domConstruct.create('input', {
				id: this.get_dom_node().id + '_button_sync_details',
				value: registry.byId('nomenclature_datastore').get_message('nomenclature_js_sync_from_abrege'),
				type: 'button',
				'disabled': "disabled"
			}, dom_child);

			var button_sync_abbr = domConstruct.create('input', {
				id: this.get_dom_node().id + '_button_sync_abbr',
				value: registry.byId('nomenclature_datastore').get_message('nomenclature_js_sync_from_detail'),
				type: 'button',
				'disabled': "disabled"
			}, dom_child);
			if (this.nomenclature.record_formation.get_record()) {
				var save_ajax_button = domConstruct.create('input', {
					id: this.get_dom_node().id + '_save_ajax_button',
					value: registry.byId('nomenclature_datastore').get_message('nomenclature_js_save_ajax'),
					type: 'button',
				}, dom_child);

				var button_create_all = domConstruct.create('input', {
					id: this.get_dom_node().id + '_button_create_all',
					value: registry.byId('nomenclature_datastore').get_message('nomenclature_js_create_all_submanifestations'),
					type: 'button',
				}, dom_child);
				this.own(on(button_create_all, 'click', lang.hitch(this, this.create_all_submanifestations)));
				this.own(on(save_ajax_button, 'click', lang.hitch(this, this.ajax_save)));
			}
			var button_copy_periart_abbr = domConstruct.create('input', {
				id: this.get_dom_node().id + '_button_copy_periart_abbr',
				value: registry.byId('nomenclature_datastore').get_message('nomenclature_js_copy_periart_abbr'),
				type: 'button'
			}, dom_child);
			this.error_node = domConstruct.create('div', {
				id: this.get_dom_node().id + '_error_node'
			}, dom_child);

			this.families_node = domConstruct.create('div', {
				id: this.get_dom_node().id + '_family_node'
			}, dom_child);

			this.workshops_node = domConstruct.create('div', {
				id: this.get_dom_node().id + '_workshop_node'
			}, dom_child);

			this.own(on(button_insert_tilde, 'click', lang.hitch(this, this.insert_tilde)));
			this.own(on(this.input_abbrege, 'keyup', lang.hitch(this, this.allow_sync_from_abbrege)));
			this.own(on(this.input_abbrege, 'blur', lang.hitch(this, this.clean_input_abbrege)));
			this.own(on(button_sync_details, 'click', lang.hitch(this, this.sync_from_abbrege)));
			this.own(on(button_sync_abbr, 'click', lang.hitch(this, this.sync_from_details)));
			this.own(on(button_copy_periart_abbr, 'click', lang.hitch(this, this.copy_periart_abbr)));

			/** Cr�ation des inputs hidden en vue de l'enregistrement d'une formation **/
			this.hidden_abbr = domConstruct.create('input', { type: 'hidden', name: this.nomenclature.get_hidden_field_name('abbr'), value: this.nomenclature.get_abbreviation() }, dom_child);
			this.create_families_part();
			this.init_exotic_instruments();
			this.init_workshops();
		},

		allow_sync_from_abbrege: function (evt) {
			if (evt.target.value != this.nomenclature.get_abbreviation()) {
				this.sync_from_abbreviation_allowed = true;
				dom.byId(this.get_dom_node().id + '_button_sync_details').disabled = false;
			} else {
				this.sync_from_abbreviation_allowed = false;
				dom.byId(this.get_dom_node().id + '_button_sync_details').disabled = true;
			}

		},

		allow_sync_from_details: function () {
			this.sync_from_details_allowed = true;
			dom.byId(this.get_dom_node().id + '_button_sync_abbr').disabled = false;
		},

		sync_from_details: function (button) {
			domConstruct.empty(this.error_node);
			this.nomenclature.calc_abbreviation();

			var abbr = this.nomenclature.get_abbreviation();
			var input = dom.byId(this.get_dom_node().id + '_input_abbrege');


			input.value = abbr;
			this.maj_abbreviation();
			if (!this.nomenclature.is_valid()) {
				let errors = this.nomenclature.errors;
				this.show_analize_error(errors, false);
			}

			this.sync_from_abbreviation_allowed = false;
			this.sync_from_details_allowed = false;

			dom.byId(this.get_dom_node().id + '_button_sync_details').disabled = true;
			dom.byId(this.get_dom_node().id + '_button_sync_abbr').disabled = true;
		},

		sync_from_abbrege: function () {
			this.clean_input_abbrege();
			if (confirm(registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_ui_confirm_sync'))) {
				this.purge_instruments();
				domConstruct.empty(this.error_node);
				// le setter d�clenche l'analyse
				this.nomenclature.set_abbreviation(this.input_abbrege.value);
				this.maj_abbreviation();
				if (!this.nomenclature.is_valid()) {
					let errors = this.nomenclature.errors;
					this.show_analize_error(errors, false);
					return false;
				}
				this.sync_from_abbreviation_allowed = false;
				this.sync_from_details_allowed = false;
				dom.byId(this.get_dom_node().id + '_button_sync_details').disabled = true;
				dom.byId(this.get_dom_node().id + '_button_sync_abbr').disabled = true;
				this.collapse_all_family();
			}
		},

		create_families_part: function () {
			for (var i = 0; i < this.nomenclature.families.length; i++) {
				var params = {
					id: this.nomenclature.families[i].get_hash(),
					family: this.nomenclature.families[i],
					dom_node: this.families_node
				};
				this.families.push(new Family_ui(params));
			}
		},

		init_workshops: function () {
			var params = {
				nomenclature: this.nomenclature,
				dom_node: this.workshops_node
			};
			this.workshops_ui = new Workshops_ui(params);
		},

		purge_instruments: function () {
			for (var i = 0; i < this.families.length; i++) {
				this.families[i].purge_instruments();
			}
			this.total_families = 0;
			/**
			 * TODO: ajout de la purge des ateliers
			 */
			var workshops_indice_max = (this.workshops_ui.workshops.length) - 1;
			for (var i = workshops_indice_max; i >= 0; i--) {
				this.workshops_ui.delete_workshop_event(this.nomenclature.workshops[i].get_order());
			}

			this.workshops_ui.generate_inputs();
			this.workshops_ui.maj_abbreviation();
		},

		set_dom_node: function (dom_node) {
			this.dom_node = dom_node;
		},
		get_dom_node: function () {
			return this.dom_node;
		},
		get_nomenclature: function () {
			return this.nomenclature;
		},
		set_nomenclature: function (nomenclature) {
			this.nomenclature = nomenclature;
		},
		family_ready: function () {
			this.total_families++;
			var families = this.nomenclature.get_families();
			if (this.total_families == families.length) {
				this.families_ready = true;
				this.sync_from_details_allowed = false;
				this.sync_from_abbreviation_allowed = false;
				dom.byId(this.get_dom_node().id + '_button_sync_details').disabled = true;
				dom.byId(this.get_dom_node().id + '_button_sync_abbr').disabled = true;
				this.check_flags();
			}
		},
		workshops_ready: function () {
			this.workshops_flag_ready = true;
			this.check_flags();
		},
		exotic_instruments_ready: function () {
			this.exotic_instruments_flag_ready = true;
			this.check_flags();
		},
		init_exotic_instruments: function () {
			var params = {
				instruments_list: this.nomenclature.exotic_instruments_list,
				dom_node: this.families_node
			};
			this.exotic_instruments_ui = new Exotic_instruments_ui(params);
		},
		check_flags: function () {
			if (this.families_ready && this.workshops_flag_ready && this.exotic_instruments_flag_ready) {
				init_drag();
				document.body.onkeypress = validation;
			}
		},
		get_main_node: function () {
			return this.main_node;
		},

		set_main_node: function (main_node) {
			this.main_node = main_node;
		},
		maj_abbreviation: function () {
			this.span_abbreviation.innerHTML = ' ' + this.input_abbrege.value;
			this.hidden_abbr.value = this.input_abbrege.value;
		},
		check_validate: function () {
			if (this.sync_from_abbreviation_allowed == true || this.sync_from_details_allowed == true || this.nomenclature.errors.length) {
				return false;
			}
			return true;
		},
		focus_on_error: function () {
			alert(registry.byId('nomenclature_datastore').get_message('nomenclature_js_submit_not_sync'));
			this.expand_nomenclature_node();
			topic.publish('nomenclature_ui', 'error_on_submit', { hash: this.nomenclature.get_hash() });
			this.input_abbrege.focus();
		},
		expand_nomenclature_node: function () {
			var recomposed_id = this.get_dom_node().id + '_nomenclature';
			expandBase(recomposed_id);
		},
		create_all_submanifestations: function () {
			if (this.check_instrument_presence() && confirm(registry.byId('nomenclature_datastore').get_message('nomenclature_js_confirm_create_all_submanifestations'))) {
				if (this.ajax_save()) {
					this.total_instruments = 0;
					this.init_progress_bar();
					for (var i = 0; i < this.families.length; i++) {
						for (var j = 0; j < this.families[i].musicstands.length; j++) {
							for (var h = 0; h < this.families[i].musicstands[j].instruments.length; h++) {
								this.total_instruments++;
								this.families[i].musicstands[j].instruments[h].create_child();
							}
						}
					}
					for (var k = 0; k < this.exotic_instruments_ui.instruments_list_ui.instruments.length; k++) {
						this.total_instruments++;
						this.exotic_instruments_ui.instruments_list_ui.instruments[k].create_child();
					}
					for (var k = 0; k < this.workshops_ui.workshops.length; k++) {
						for (var l = 0; l < this.workshops_ui.workshops[k].instruments_list_ui.instruments.length; l++) {
							this.total_instruments++;
							this.workshops_ui.workshops[k].instruments_list_ui.instruments[l].create_child();
						}
					}
					this.reorder_children();
				}
			}
		},
		init_progress_bar: function () {
			if (!this.dialog_sub_manifestations) {
				this.dialog_sub_manifestations = new Dialog({
					title: registry.byId('nomenclature_datastore').get_message('nomenclature_js_create_submanifestations_popup_title'),
					content: '<div id="progressBarContainer"></div>' +
						'<div style="text-align:center;">' +
						'<span id="handledSubManifestationsProgressBar">0</span> ' + registry.byId('nomenclature_datastore').get_message('nomenclature_js_create_submanifestations_popup_body') + ' <span id="totalSubManifestationsProgressBar">0</span><br/>' +
						registry.byId('nomenclature_datastore').get_message('nomenclature_js_create_submanifestations_popup_new') + ' : <span id="createdSubManifestationsProgressBar">0</span>' +
						'</div>'

				});
				var hideSave = this.dialog_sub_manifestations.hide;
				this.dialog_sub_manifestations.hide = lang.hitch(this, function () {
					lang.hitch(this.dialog_sub_manifestations, hideSave)();
					this.dialog_sub_manifestations.destroyRecursive();
					this.dialog_sub_manifestations = null;
				});
			}
			var myProgressBar = new ProgressBar({
				style: "width: 300px",
				id: 'subManifestationsProgressBar',
				subManifestationsHandled: 0,
				subManifestationsCreated: 0,
			}).placeAt(dom.byId('progressBarContainer')).startup();
			this.dialog_sub_manifestations.show();
		},

		update_progress_bar: function (evt_data) {
			var progress_bar = registry.byId('subManifestationsProgressBar');
			progress_bar.set('subManifestationsHandled', parseInt(progress_bar.get('subManifestationsHandled')) + 1);
			if (evt_data.is_new) {
				progress_bar.set('subManifestationsCreated', parseInt(progress_bar.get('subManifestationsCreated')) + 1);
				dom.byId('createdSubManifestationsProgressBar').innerHTML = progress_bar.get('subManifestationsCreated');
			}
			progress_bar.set('value', (progress_bar.get('subManifestationsHandled') / this.total_instruments) * 100);
			dom.byId('totalSubManifestationsProgressBar').innerHTML = this.total_instruments;
			dom.byId('handledSubManifestationsProgressBar').innerHTML = progress_bar.get('subManifestationsHandled');
		},
		ajax_save: function () {
			var return_ajax_save = false;
			if (this.nomenclature.record_formation.get_record()) {
				if (!this.check_validate()) {
					this.focus_on_error();
				} else {
					var record_formation_hash = this.nomenclature.record_formation.get_hash();
					var args = '&' + record_formation_hash + '[num_record]=' + this.nomenclature.record_formation.get_record();
					args += '&' + record_formation_hash + '[num_formation]=' + this.nomenclature.record_formation.formation.get_id();
					args += '&' + record_formation_hash + '[num_type]=' + (this.nomenclature.record_formation.get_type() ? this.nomenclature.record_formation.get_type().id : '0');
					args += '&' + record_formation_hash + '[label]=' + this.nomenclature.record_formation.get_label();
					args += '&' + record_formation_hash + '[abbr]=' + this.nomenclature.get_abbreviation();
					args += '&' + record_formation_hash + '[notes]=';
					args += '&formation_hash=' + record_formation_hash;
					args += '&record_formation_id=' + this.nomenclature.record_formation.get_id();
					for (var i = 0; i < this.families.length; i++) {
						args += '&' + record_formation_hash + '[families_notes][' + i + ']=' + this.families[i].family.get_note();
					}
					args += '&' + record_formation_hash + '[order]=' + this.nomenclature.record_formation.get_order();
					var json_data = this.exotic_instruments_ui.instruments_list_ui.generate_json_data();
					for (var i = 0; i < json_data.length; i++) {
						args += '&' + json_data[i].name + '=' + json_data[i].value;
					}
					for (i = 0; i < this.workshops_ui.workshops.length; i++) {
						args += '&' + record_formation_hash + '[workshops][' + this.workshops_ui.workshops[i].indice + '][label]=' + this.workshops_ui.workshops[i].workshop.get_label();
						args += '&' + record_formation_hash + '[workshops][' + this.workshops_ui.workshops[i].indice + '][id]=' + this.workshops_ui.workshops[i].workshop.get_id();
						args += '&' + record_formation_hash + '[workshops][' + this.workshops_ui.workshops[i].indice + '][order]=' + this.workshops_ui.workshops[i].workshop.get_order();
						args += '&' + record_formation_hash + '[workshops][' + this.workshops_ui.workshops[i].indice + '][defined]=' + this.workshops_ui.workshops[i].workshop.get_defined();
						json_data = this.workshops_ui.workshops[i].instruments_list_ui.generate_json_data();
						for (var j = 0; j < json_data.length; j++) {
							args += '&' + json_data[j].name + '=' + json_data[j].value;
						}
					}
					xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_formation&action=save_nomenclature&id_parent=" + this.nomenclature.record_formation.get_record(), {
						handleAs: "json",
						method: "POST",
						data: args,
						sync: true
					}).then(lang.hitch(this, this.save_done), function (err) { console.log(err); }).then(function (save_done_return) {
						if (save_done_return) {
							return_ajax_save = true;
						} else {
							return_ajax_save = false;
						}
					});
					return return_ajax_save;
				}
			}
			return return_ajax_save;
		},
		save_done: function (data) {
			nomenclature_id = parseInt(data.nomenclature_id);
			if (parseInt(nomenclature_id)) {
				dom.byId(this.nomenclature.record_formation.get_hash() + '_nomenclature_id').value = nomenclature_id;
				this.nomenclature.record_formation.set_id(nomenclature_id);
				for (var key in data.exotic_instruments) {
					var instru = dom.byId(this.nomenclature.record_formation.get_hash() + '_instruments_' + data.exotic_instruments[key].order + '_id_exotic_instrument');
					if (instru) {
						instru.value = data.exotic_instruments[key].id_exotic_instrument;
						for (var other_key in data.exotic_instruments[key].other) {
							var other_instru = dom.byId(this.nomenclature.record_formation.get_hash() + '_instruments_' + data.exotic_instruments[key].order + '_other_' + data.exotic_instruments[key].other[other_key].order + '_id_exotic_instrument');
							if (other_instru) {
								other_instru.value = data.exotic_instruments[key].other[other_key].id_exotic_instrument;
							}
						}
					}
				}
				for (var i = 0; i < data.workshops.length; i++) {
					var workshop = dom.byId(this.nomenclature.record_formation.get_hash() + '_workshops_' + data.workshops[i].order + '_id');
					if (workshop) {
						workshop.value = data.workshops[i].id;
						for (var j = 0; j < this.workshops_ui.workshops.length; j++) {
							if (data.workshops[i].order == this.workshops_ui.workshops[j].workshop.get_order()) {
								this.workshops_ui.workshops[j].workshop.set_id(data.workshops[i].id);
								break;
							}
						}
						for (var j = 0; j < data.workshops[i].instruments.length; j++) {
							var instru = dom.byId(this.nomenclature.record_formation.get_hash() + '_workshops_' + data.workshops[i].order + '_instruments_' + data.workshops[i].instruments[j].order + '_id_workshop_instrument');
							if (instru) {
								instru.value = data.workshops[i].instruments[j].id_workshop_instrument;
							}
						}
					}
				}
				topic.publish('dGrowl', registry.byId('nomenclature_datastore').get_message('nomenclature_js_save_ajax_succeeded'));
				return true;
			} else {
				alert(registry.byId('nomenclature_datastore').get_message('nomenclature_js_save_ajax_failed'));
			}
			return false;
		},

		collapse_all_family: function () {
			for (var i = 0; i < this.families.length; i++) {
				if (this.families[i].get_ajax_dispatched()) {
					expandBase(this.families[i].get_dom_node().id + '_nomenclature_form_family_' + this.families[i].family.get_id(), true);
					this.families[i].set_ajax_dispatched(false);
				}
			}
		},

		check_instrument_presence: function () {
			for (var i = 0; i < this.families.length; i++) {
				for (var j = 0; j < this.families[i].musicstands.length; j++) {
					for (var h = 0; h < this.families[i].musicstands[j].instruments.length; h++) {
						return true;
					}
				}
			}
			for (var k = 0; k < this.exotic_instruments_ui.instruments_list_ui.instruments.length; k++) {
				return true;
			}
			for (var k = 0; k < this.workshops_ui.workshops.length; k++) {
				return true;
			}
			return false;
		},

		clean_input_abbrege: function () {
			this.input_abbrege.value = this.input_abbrege.value.trim();
			this.input_abbrege.value = this.input_abbrege.value.replace(new RegExp(String.fromCharCode(8776), 'g'), '~');
			this.input_abbrege.value = this.input_abbrege.value.replace(new RegExp(String.fromCharCode(8212), 'g'), '-');
			this.input_abbrege.value = this.input_abbrege.value.replace(new RegExp("\\s*-\\s*", 'g'), ' - ');
		},

		copy_periart_abbr: function () {
			this.input_abbrege.value = this.input_abbrege.value.replace(new RegExp('~', 'g'), String.fromCharCode(8776));
			this.input_abbrege.value = this.input_abbrege.value.replace(new RegExp('\\s*-\\s*', 'g'), String.fromCharCode(8212));
			try {
				this.input_abbrege.select();
				var copy_success = document.execCommand('copy');
				if (copy_success) {
					topic.publish('dGrowl', registry.byId('nomenclature_datastore').get_message('nomenclature_js_copy_periart_abbr_copy_success'));
				}
			} catch (e) {
				prompt(registry.byId('nomenclature_datastore').get_message('nomenclature_js_copy_periart_abbr_prompt'), this.input_abbrege.value);
			}
			this.clean_input_abbrege();
		},

		insert_tilde: function () {
			var curpos = this.input_abbrege.selectionStart;
			var before = this.input_abbrege.value.substr(0, curpos);
			var after = this.input_abbrege.value.substr(curpos);
			this.input_abbrege.value = before + '~' + after;
			this.input_abbrege.focus();
			this.input_abbrege.setSelectionRange(curpos + 1, curpos + 1);
		}
	});
});