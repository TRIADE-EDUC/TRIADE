// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_nomenclature_voices_ui.js,v 1.13.8.1 2025/04/25 09:25:43 rtigero Exp $

define(["dojo/_base/declare", "dojo/dom-construct", "dojo/topic", "apps/nomenclature/nomenclature_voices_list_ui", "dojo/on", "dojo/_base/lang", "dijit/_WidgetBase", "apps/nomenclature/nomenclature_nomenclature_voices", "dijit/registry", "dojo/dom", "dojo/request/xhr", "apps/pmb/PMBDialog", "dijit/ProgressBar"], function (declare, domConstruct, topic, Voices_list_ui, on, lang, _WidgetBase, Nomenclature_voices, registry, dom, xhr, Dialog, ProgressBar) {
	/*
	 *Classe nomenclature_nomenclature_voices_ui. Classe g�rant l'affichage d'une nomenclature de type voix
	 */
	return declare("nomenclature_nomenclature_voices_ui", [_WidgetBase], {

		nomenclature_voices: null, /** Instance du modele li� **/
		record_formation_ui: null, /** Instance de l'ui parent **/
		voices_list_ui: null,	  /** Instance de l'ui g�r�e par cette ui **/
		id: 0,
		events_handles: null,
		dom_node: null,
		span_abbreviation: null,
		hidden_abbr: null,
		sync_from_abbreviation_allowed: false,
		sync_from_details_allowed: false,
		voices_list_node: null,
		ajax_dispatched: false,

		constructor: function (params) {
			this.events_handles = new Array();
			this.events_handles.push(topic.subscribe('voices_list_ui', lang.hitch(this, this.handle_events)));
			this.events_handles.push(topic.subscribe('nomenclature_voices', lang.hitch(this, this.handle_events)));
			this.events_handles.push(topic.subscribe('voice_ui', lang.hitch(this, this.handle_events)));
		},

		buildRendering: function () {
			this.inherited(arguments);
			this.build_form();
		},

		handle_events: function (evt_type, evt_args) {
			switch (evt_type) {
				case 'voices_list_changed':
					if (evt_args.hash.indexOf(this.nomenclature_voices.get_hash()) != -1) {
						this.allow_sync_from_details()
					}
					break;
				case 'voices_list_reordered':
					if (evt_args.hash.indexOf(this.nomenclature_voices.get_hash()) != -1) {
						this.update_after_reord();
					}
					break;
				case 'error_analyze':
					if (evt_args.hash.indexOf(this.nomenclature_voices.get_hash()) != -1) {
						this.show_analize_error(evt_args.error);
					}
					break;
				case 'submanifestation_created':
					if (evt_args.hash.indexOf(this.nomenclature_voices.get_hash()) != -1) {
						this.update_progress_bar(evt_args);
					}
					break;
				case "drag_and_drop_end":
					if (evt_args.hash.indexOf(this.nomenclature_voices.get_hash()) != -1) {
						// l'abbreviation a changé on sync et on save
						this.sync_from_details();
						this.ajax_save();
							topic.publish('nomenclature_voices_ui', 'sync_and_save_end', { hash: this.nomenclature_voices.get_hash() });
						this.reorder_children();
					}
					break;
			}
		},
		reorder_children: function () {
			xhr("./ajax.php?module=ajax&categ=nomenclature&sub=nomenclature_record_formations&action=reorder_children&id_parent=" + this.nomenclature_voices.record_formation.get_record(), {
				handleAs: "json",
				method: "GET",
				sync: true
			});
		},

		build_form: function () {

			domConstruct.create('div', { class: 'row' }, this.get_dom_node());
			/** Cr�ation du noeud extensible **/
			var noeud_princ = domConstruct.create('div', {
				id: this.get_dom_node().id + '_nomenclature_voices',
				class: 'notice-parent'
			}, this.get_dom_node());

			var img_plus = domConstruct.create('img', {
				id: this.get_dom_node().id + '_nomenclature_voicesImg',
				class: 'img_plus',
				hspace: '3',
				border: '0',
				onclick: 'expandBase(\'' + this.get_dom_node().id + '_nomenclature_voices\', true); return false;',
				title:'d\351tail',
				src: pmbDojo.images.getImage('plus.gif')
			}, noeud_princ);
			this.own(on(img_plus, 'click', lang.hitch(this, this.ajax_dispatch)));
			var span = domConstruct.create('span', { class: 'notice-heada', innerHTML: registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_label') + ' ' }, noeud_princ);
			this.span_abbreviation = domConstruct.create('span', null, span);
			if (this.nomenclature_voices.get_abbreviation() && this.nomenclature_voices.get_abbreviation() != "") {
				this.span_abbreviation.innerHTML = '- ' + this.nomenclature_voices.get_abbreviation();
			}


			/** Cr�ation du noeud enfant, affich� lors de l'appui sur le bouton plus **/
			var noeuf_enfant = domConstruct.create('div', {
				id: this.get_dom_node().id + '_nomenclature_voicesChild',
				startOpen: "Yes",
				class: 'notice-child',
				callback: "recalc_recept",
				style: {
					marginBottom: '6px',
					display: 'none',
					width: '94%',
				}
			}, this.get_dom_node());

			var dom_child = domConstruct.create('div', {
				id: this.get_dom_node().id + "_nomenclature_control_voices"
			}, noeuf_enfant);

			this.input_abbrege = domConstruct.create('input', {
				id: this.get_dom_node().id + '_input_abbrege_voices',
				class: 'saisie-80em',
				type: 'text',
				value: this.nomenclature_voices.get_abbreviation()
			}, dom_child);

			var button_sync_details = domConstruct.create('input', {
				id: this.get_dom_node().id + '_button_sync_details_voices',
				value:'Sync depuis abbr\351g\351',
				type: 'button',
				'disabled': "disabled"
			}, dom_child);

			var button_sync_abbr = domConstruct.create('input', {
				id: this.get_dom_node().id + '_button_sync_abbr_voices',
				value:'Sync depuis d\351tails',
				type: 'button',
				'disabled': "disabled"
			}, dom_child);

			if (this.nomenclature_voices.record_formation.get_record()) {
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


			this.error_node = domConstruct.create('div', {
				id: this.get_dom_node().id + '_error_node_voices'
			}, dom_child);

			this.voices_list_node = domConstruct.create('div', {
				id: this.get_dom_node().id + '_voices_list_node'
			}, dom_child);

			this.own(on(this.input_abbrege, 'keyup', lang.hitch(this, this.allow_sync_from_abbrege)));
			on(button_sync_details, 'click', lang.hitch(this, this.sync_from_abbrege));
			on(button_sync_abbr, 'click', lang.hitch(this, this.sync_from_details));

			/** Cr�ation des inputs hidden en vue de l'enregistrement d'une formation **/
			this.hidden_abbr = domConstruct.create('input', { type: 'hidden', name: this.nomenclature_voices.get_hidden_field_name('abbr'), value: this.nomenclature_voices.get_abbreviation() }, dom_child);
			this.init_voices_list_ui();
		},

		init_voices_list_ui: function () {
			var obj = { voices_list: this.nomenclature_voices.voices_list, nomenclature_voices_ui: this, dom_node: this.get_voices_list_node() };
			this.voices_list_ui = new Voices_list_ui(obj);
		},

		get_id: function () {
			return this.id;
		},

		set_id: function (id) {
			this.id = id;
		},

		get_voices_list: function () {
			return this.voices_list;
		},

		set_voices_list: function (voices_list) {
			this.voices_list = voices_list;
		},

		get_record_formation_ui: function () {
			return this.record_formation_ui;
		},

		set_record_formation_ui: function (record_formation_ui) {
			this.record_formation_ui = record_formation_ui;
		},

		get_nomenclature_voices: function () {
			return this.nomenclature_voices;
		},

		set_nomenclature_voices: function (nomenclature_voices) {
			this.nomenclature_voices = nomenclature_voices;
		},

		get_voices_list_ui: function () {
			return this.voices_list_ui;
		},

		set_voices_list_ui: function (voices_list_ui) {
			this.voices_list_ui = voices_list_ui;
		},

		destroy: function () {
			for (var i = 0; i < this.events_handles.length; i++) {
				this.events_handles[i].remove();
			}
			this.nomenclature_voices = null;
			this.inherited(arguments);
		},

		get_dom_node: function () {
			return this.dom_node;
		},

		set_dom_node: function (dom_node) {
			this.dom_node = dom_node;
		},

		get_error_node: function () {
			return this.error_node;
		},

		set_error_node: function (error_node) {
			this.error_node = error_node;
		},

		get_voices_list_node: function () {
			return this.voices_list_node;
		},

		set_voices_list_node: function (voices_list_node) {
			this.voices_list_node = voices_list_node;
		},

		allow_sync_from_abbrege: function (evt) {
			if (evt.target.value != this.nomenclature_voices.get_abbreviation()) {
				this.sync_from_abbreviation_allowed = true;
				dom.byId(this.get_dom_node().id + '_button_sync_details_voices').disabled = false;
			} else {
				this.sync_from_abbreviation_allowed = false;
				dom.byId(this.get_dom_node().id + '_button_sync_details_voices').disabled = true;
			}

		},

		allow_sync_from_details: function () {
			this.sync_from_details_allowed = true;
			dom.byId(this.get_dom_node().id + '_button_sync_abbr_voices').disabled = false;
		},

		sync_from_details: function (button) {
			domConstruct.empty(this.error_node);
			this.nomenclature_voices.calc_abbreviation();
			var abbr = this.nomenclature_voices.get_abbreviation();
			var input = dom.byId(this.get_dom_node().id + '_input_abbrege_voices');
			this.sync_from_abbreviation_allowed = false;
			this.sync_from_details_allowed = false;

			dom.byId(this.get_dom_node().id + '_button_sync_details_voices').disabled = true;
			dom.byId(this.get_dom_node().id + '_button_sync_abbr_voices').disabled = true;
			input.value = abbr;
			this.maj_abbreviation();

			//On valide que l'abbreviation est correcte
			if (!this.nomenclature_voices.is_valid()) {
				let errors = this.nomenclature_voices.errors;
				this.show_analize_error(errors, false);
			}
		},
		sync_from_abbrege: function () {
			this.purge_voices();
			domConstruct.empty(this.error_node);
			this.sync_from_abbreviation_allowed = false;
			this.sync_from_details_allowed = false;
			dom.byId(this.get_dom_node().id + '_button_sync_details_voices').disabled = true;
			dom.byId(this.get_dom_node().id + '_button_sync_abbr_voices').disabled = true;
			this.nomenclature_voices.set_abbreviation(this.input_abbrege.value);
			this.nomenclature_voices.analyze();
			topic.publish('nomenclature_voices_ui', 'nomenclature_voices_sync_from_abrege', {
				hash: this.nomenclature_voices.get_hash(),
			});

			this.maj_abbreviation();

			//On valide que l'abbreviation est correcte
			if (!this.nomenclature_voices.is_valid()) {
				let errors = this.nomenclature_voices.errors;
				this.show_analize_error(errors, false);
			}
			/** TODO: Update List **/
		},

		maj_abbreviation: function () {
			this.span_abbreviation.innerHTML = '- ' + this.input_abbrege.value;
			this.hidden_abbr.value = this.input_abbrege.value;
		},
		show_analize_error: function (error, purge = true) {
			if (purge) {
				this.purge_voices();
			}
			domConstruct.empty(this.error_node);
			var abbr = "";
			for (var i = 0; i < this.nomenclature_voices.get_abbreviation().length; i++) {
				if (error[0].position == i) {
					abbr += "<span style='color:red;font-weight:bold;'>" + this.nomenclature_voices.get_abbreviation()[i] + "</span>";
				} else {
					abbr += this.nomenclature_voices.get_abbreviation()[i];
				}
			}
			domConstruct.create('div', {
				class: "row",
				style: "height:40px;",
				innerHTML: "<div class='colonne10'><img align='left' src='" + pmbDojo.images.getImage('error.gif') + "'></div><div class='colonne80'><b>" + registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_for_analyze') + " : </b>" + abbr + "<br>" + error[0].msg + "</div>"
			}, this.error_node);
			this.hidden_abbr.value = "";
			topic.publish('nomenclature_voices_ui', "hide_node", { hash: this.nomenclature_voices.get_hash() });
		},
		purge_voices: function () {
			this.voices_list_ui.purge_voices();
		},
		update_after_reord: function () {
			this.input_abbrege.value = this.nomenclature_voices.voices_list.get_abbreviation();
			this.hidden_abbr.value = this.input_abbrege.value;
			this.sync_from_abbreviation_allowed = false;
			this.sync_from_details_allowed = false;
			dom.byId(this.get_dom_node().id + '_button_sync_details_voices').disabled = true;
			dom.byId(this.get_dom_node().id + '_button_sync_abbr_voices').disabled = true;
		},
		check_validate: function () {
			if (this.sync_from_abbreviation_allowed == true || this.sync_from_details_allowed == true || this.nomenclature_voices.errors.length) {
				return false;
			}
			return true;
		},
		focus_on_error: function () {
			alert(registry.byId('nomenclature_datastore').get_message('nomenclature_js_submit_not_sync'));
			this.expand_nomenclature_node();
			topic.publish('nomenclature_voices_ui', 'error_on_submit', { hash: this.nomenclature_voices.get_hash() });
			this.input_abbrege.focus();
		},
		expand_nomenclature_node: function () {
			var recomposed_id = this.get_dom_node().id + '_nomenclature_voices';
			expandBase(recomposed_id);
		},
		ajax_save: function () {
			var return_ajax_save = false;
			if (this.nomenclature_voices.record_formation.get_record()) {
				if (!this.check_validate()) {
					this.focus_on_error();
				} else {
					var record_formation_hash = this.nomenclature_voices.record_formation.get_hash();
					var args = '&' + record_formation_hash + '[num_record]=' + this.nomenclature_voices.record_formation.get_record();
					args += '&' + record_formation_hash + '[num_formation]=' + this.nomenclature_voices.record_formation.formation.get_id();
					args += '&' + record_formation_hash + '[num_type]=' + (this.nomenclature_voices.record_formation.get_type() ? this.nomenclature_voices.record_formation.get_type().id : '0');
					args += '&' + record_formation_hash + '[label]=' + this.nomenclature_voices.record_formation.get_label();
					args += '&' + record_formation_hash + '[abbr]=' + this.nomenclature_voices.get_abbreviation();
					args += '&' + record_formation_hash + '[notes]=';
					args += '&formation_hash=' + record_formation_hash;
					args += '&record_formation_id=' + this.nomenclature_voices.record_formation.get_id();
					args += '&' + record_formation_hash + '[order]=' + this.nomenclature_voices.record_formation.get_order();

					xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_formation&action=save_nomenclature&id_parent=" + this.nomenclature_voices.record_formation.get_record(), {
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
				dom.byId(this.nomenclature_voices.record_formation.get_hash() + '_nomenclature_id').value = nomenclature_id;
				this.nomenclature_voices.record_formation.set_id(nomenclature_id);
				topic.publish('dGrowl', registry.byId('nomenclature_datastore').get_message('nomenclature_js_save_ajax_succeeded'));
				return true;
			} else {
				alert(registry.byId('nomenclature_datastore').get_message('nomenclature_js_save_ajax_failed'));
			}
			return false;
		},
		create_all_submanifestations: function () {
			if (this.voices_list_ui.voices_ui.length && confirm(registry.byId('nomenclature_datastore').get_message('nomenclature_js_confirm_create_all_submanifestations'))) {
				if (this.ajax_save()) {
					this.init_progress_bar();
					for (var i = 0; i < this.voices_list_ui.voices_ui.length; i++) {
						this.voices_list_ui.voices_ui[i].create_child();
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
						'<span id="handledSubManifestationsProgressBar">0</span> ' + registry.byId('nomenclature_datastore').get_message('nomenclature_js_create_submanifestations_popup_body') + ' <span id="totalSubManifestationsProgressBar">' + this.voices_list_ui.voices_ui.length + '</span><br/>' +
						registry.byId('nomenclature_datastore').get_message('nomenclature_js_create_submanifestations_popup_new') + ' : <span id="createdSubManifestationsProgressBar">0</span>' +
						'</div>'
					,
					hide: lang.hitch(this, function () {
						this.dialog_sub_manifestations.destroyRecursive();
						this.dialog_sub_manifestations = null;
					})
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
			progress_bar.set('value', (progress_bar.get('subManifestationsHandled') / this.voices_list_ui.voices_ui.length) * 100);
			dom.byId('handledSubManifestationsProgressBar').innerHTML = progress_bar.get('subManifestationsHandled');
		},
		ajax_dispatch: function () {
			if (!this.ajax_dispatched) {
				this.ajax_dispatched = true;
				topic.publish('nomenclature_voices_ui', 'nomenclature_voices_expanded', {
					hash: this.nomenclature_voices.get_hash(),
				});
			}
		}
	});
});