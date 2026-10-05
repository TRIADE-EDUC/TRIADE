// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Translations.js,v 1.11.2.1 2024/04/30 07:44:54 dgoron Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-construct",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/dom-style"
], function(declare, lang, request, query, on, domConstruct, domAttr, dom, domStyle){
	return declare(null, {
		domNodeId:null,
		data:null,
		languages:null,
		constructor: function(domNodeId, data, languages) {
			this.domNodeId = domNodeId;
			this.data = JSON.parse(data);
			if(languages) {
				this.languages = JSON.parse(languages);
			}
			this.buildFields();
		},
		getDisplayButton: function(node) {
			var button = domConstruct.create('input');
			domAttr.set(button, 'type', 'button');
			domAttr.set(button, 'class', 'bouton');
			domAttr.set(button, 'value', pmbDojo.messages.getMessage('translation', 'translations'));
			on(button, 'click', lang.hitch(this, this.displayTranslations, node));
			return button;
		},
		getDisplayIcon: function(node) {
			var icon = domConstruct.create('img');
			if(base_path != '.') {
				domAttr.set(icon, 'src', pmbDojo.images.getImage('translate.png').replace('./', base_path+'/'));
			} else {
				domAttr.set(icon, 'src', pmbDojo.images.getImage('translate.png'));
			}
			domAttr.set(icon, 'title', pmbDojo.messages.getMessage('translation', 'translations'));
			domAttr.set(icon, 'alt', pmbDojo.messages.getMessage('translation', 'translations'));
			on(icon, 'click', lang.hitch(this, this.displayTranslations, node));
			return icon;
		},
		getTranslationLabel: function(node, language) {
			var div = domConstruct.create('div');
			domAttr.set(div, 'class', 'row');
			
			return domConstruct.place(domConstruct.create('label', { innerHTML : language.label, class:'etiquette', for: language.code+'_'+node.id}), div);
		},
		getTranslationField: function(node, lang) {
			var div_field = domConstruct.create('div');
			domAttr.set(div_field, 'class', 'row');
			
			var cloneNode = dojo.clone(node);
			domAttr.set(cloneNode, 'id', lang+'_'+node.id);
			domAttr.set(cloneNode, 'name', lang+'_'+node.name);
			if(this.data[domAttr.get(node, 'data-translation-fieldname')] && this.data[domAttr.get(node, 'data-translation-fieldname')][lang]) {
				domAttr.set(cloneNode, 'value', this.data[domAttr.get(node, 'data-translation-fieldname')][lang]);
			} else {
				domAttr.set(cloneNode, 'value', '');
			}
			domAttr.remove(cloneNode, 'data-translation-fieldname');
			domConstruct.place(cloneNode, div_field);
			return div_field;
		},
		hasDisplayTranslation: function(node, language) {
			if(language.is_current_lang == true) {
				if(this.data[domAttr.get(node, 'data-translation-fieldname')] && this.data[domAttr.get(node, 'data-translation-fieldname')][language.code]) {
					let translated_value = this.data[domAttr.get(node, 'data-translation-fieldname')][language.code];
					//Simulation d'un clone pour la comparaison des deux valeurs
					var cloneNode = dojo.clone(node);
					domAttr.set(cloneNode, 'value', translated_value);
					if(node.value == cloneNode.value) {
						return false;
					}
				} else {
					return false;
				}
			}
			return true;
		},
		getDisplayTranslations: function(node) {
//			this.getTranslations(domAttr.get(node, 'data-translation-tablename'), domAttr.get(node, 'node.data-translation-fieldname'));
			var translations = domConstruct.create('div');
			domAttr.set(translations, 'id', 'translations_'+node.id);
			domAttr.set(translations, 'class', 'row translations');
			domAttr.set(translations, 'style', 'display: none;');
			this.languages.forEach(lang.hitch(this, function(language) {
				if(this.hasDisplayTranslation(node, language)) {
					domConstruct.place(this.getTranslationLabel(node, language), translations);
					domConstruct.place(this.getTranslationField(node, language.code), translations);
				}
			}));
			return translations;
		},
		buildFields: function() {
			if(!this.languages) {
				this.getLanguages();
			}
			if(this.languages.length) {
				var nodes = dom.byId(this.domNodeId).querySelectorAll("[data-translation-fieldname]");
				for(var i=0; i<nodes.length; i++){
					var nodeIcon = domConstruct.place(this.getDisplayIcon(nodes.item(i)), nodes.item(i), "after");
					domConstruct.place(this.getDisplayTranslations(nodes.item(i)), nodeIcon, "after");
				}
			}
		},
		displayTranslations: function(node) {
			var translationsNode = dom.byId('translations_'+node.id);
			if(domStyle.get(translationsNode, 'display') == 'block') {
				domStyle.set(translationsNode, 'display', 'none');
			} else {
				domStyle.set(translationsNode, 'display', 'block');
			}
		},
		getLanguages: function() {
			if(!base_path) {
				base_path = '.';
			}
			request.get(base_path+'/ajax.php?module=ajax&categ=translations&action=get_languages', {
				handleAs:'json',
				sync: true
			}).then(lang.hitch(this, this.gotLanguages));
		},
		gotLanguages: function(data) {
			this.languages = data;
		},
//		getTranslations: function(num_field, table_name, field_name) {
//			request.get('./ajax.php?module=ajax&categ=translations&action=get_translations&num_field='+num_field+'&table_name='+table_name+'field_name='+field_name, {
//				handleAs:'json',
//				sync: true
//			}).then(lang.hitch(this, this.gotTranslations));
//		},
//		gotTranslations: function(data) {
////			this.translations = data;
//		},
	});
});