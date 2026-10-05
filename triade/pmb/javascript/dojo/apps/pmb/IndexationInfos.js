// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: IndexationInfos.js,v 1.13 2023/02/01 14:02:35 gneveu Exp $


define(["dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/request",
	"dojo/dom",
	"dojo/dom-construct",
	"dojo/on"
], function(declare, lang, request, dom, domConstruct, on) {

	return declare(null, {
		something: null,
		callback: false,
		indexationInProgress: true,
		constructor: function() {
			if (!dom.byId('indexation_infos') || (window.location.href.indexOf('circ.php') != -1)) {
				return;
			}
			this.call();
		},
		call: function() {
			if (false == this.indexationInProgress) {
				clearInterval(this.callback);
				this.callback = false;
				return;
			}
			request('./ajax.php?module=ajax&categ=indexation&sub=get_infos', {
				handleAs: 'json'
			}).then(lang.hitch(this, function(response) {
				var container = dom.byId('indexation_infos');
				domConstruct.empty(container);
				if (Object.keys(response).length) {
					domConstruct.create('h2', { 'class': 'indexation_title', innerHTML: pmbDojo.messages.getMessage('indexation', 'indexation_in_progress') }, container);
					for (var key in response) {
						domConstruct.create('label', { 'class': 'indexation_entity_label', innerHTML: response[key].label }, container);
						var ul = domConstruct.create('ul', { 'class': 'indexation_entity_ul' }, container);
						for (var i = 0; i < response[key].children.length; i++) {
							domConstruct.create('li', { 'class': 'indexation_entity_li', innerHTML: response[key].children[i].entity_label + response[key].children[i].nb }, ul);
						}
					}
				} else {
					this.indexationInProgress = false;
				}
			}));
		},

		initCall: function() {
			this.disableCall();
			if (window.document.hasFocus() && this.callback == false) {
				  this.callback = setInterval(lang.hitch(this, this.call), 30000);
			}
		},

		disableCall: function(evt) {
			if (typeof (window[1]) != "undefined" && window[1].location.href.indexOf('ajax.php') != -1) {
				on(window[1], 'blur', lang.hitch(this, this.disableCall));
				on(window[1], 'focus', lang.hitch(this, this.initCall));
			}
			if (this.callback && (window.document.hasFocus() == false)) {
				clearInterval(this.callback);
				this.callback = false;
			}
		}
	});
});