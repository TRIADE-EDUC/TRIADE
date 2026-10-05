// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: VisitsStatistics.js,v 1.3 2017/12/28 10:11:08 apetithomme Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/ready"
], function(declare, lang, request, query, on, domAttr, dom, ready){
	return declare(null, {
		constructor: function() {
			query('.visits_statistics_button').forEach(lang.hitch(this, this.addEventOnButton));
			query('.visits_statistics_input').forEach(lang.hitch(this, this.addEventOnInput));
			ready(this, this.overrideSaveCircParam);
		},
		
		addEventOnButton: function(node) {
			on(node, 'click', lang.hitch(this, this.updateCounter));
		},
		
		addEventOnInput: function(node) {
			on(node, 'change', lang.hitch(this, this.counterChanged));
		},
		
		updateCounter: function(e) {
			var action = domAttr.get(e.target, 'action');
			var counterType = domAttr.get(e.target, 'counter_type');
			var input = dom.byId('visits_statistics_' + counterType + '_input');
			var databaseAction = '';
			if ((action == 'remove') && (parseInt(input.value) > 0)) {
				databaseAction = 'remove_visit';
				input.value = parseInt(input.value) - 1;
			}
			if (action == 'add') {
				databaseAction = 'add_visit';
				input.value = parseInt(input.value) + 1;
			}
			if (databaseAction) {
				this.updateDatabase(counterType, databaseAction);
			}
		},
		
		counterChanged: function(e) {
			var counterType = domAttr.get(e.target, 'counter_type');
			e.target.value = parseInt(e.target.value);
			if (e.target.value) {
				this.updateDatabase(counterType, 'update_visits', e.target.value);
			}
		},
		
		updateDatabase: function(counterType, action, value) {
			request.post('ajax.php?module=ajax&categ=visits_statistics&sub='+action, {
				data: {
					counter_type: counterType,
					value: value
				}
			});
		},
		
		overrideSaveCircParam: function() {
			if (typeof(save_circ_params) == 'function') {
				var old_save_circ_params = save_circ_params;
				save_circ_params = function() {
					old_save_circ_params();
					request.get('ajax.php?module=ajax&categ=visits_statistics&sub=get_data', {
						handleAs: 'json'
					}).then(function(data) {
						query('.visits_statistics_input').forEach(function(node) {
							var counter_type = domAttr.get(node, 'counter_type');
							if (data[counter_type]) {
								node.value = data[counter_type];
							} else {
								node.value = 0;
							}
						});
					});
				}
			}
		}
	});
});