// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DsiForm.js,v 1.5 2023/03/28 10:42:51 qvarin Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/dom",
        "dojo/request",
        "dojo/dom-form",
        "apps/search_dsi/EntityForm",
],
function(declare, lang, dom, request, domForm, EntityForm) {
	return declare(EntityForm, {
		uniqueId: "",
		loadSetDialog : function(params, evt) {
			this.uniqueId = params.uniqueId;
			let search_data_node = dom.byId('idRMCSelector_data_' + this.uniqueId);
			let search_data = "";
			if(search_data_node) {
				search_data = search_data_node.value
			}

			let path = './ajax.php?module='+params.module;
			path += '&what='+params.what;
			path += '&action='+params.action;
			path += '&caller=dsi';
			path += '&no_search=1&class_name='+this.className;
			path += '&search_data='+search_data;
			path += '&method=saveAdvancedSearch';
			path += '&entity_type=' + params.entity_type;
			path += '&entity_id=' + params.entity_id;

			if (params.id_champ) {
				path += '&id_champ=' + params.id_champ;
			} else {
				path += '&id_champ=';
			}

			if (params.what == "ontology") {
				path += '&element=concept';
			}

			if (params.authperso_id > 0) {
				path += '&authperso_id=';
				path += params.authperso_id;
			}

			if (path) {
				this.loadDialog(params, evt, path, true);
			}
		},

		saveAdvancedSearch : function(params) {

			if (!params.formId) {
				return false;
			}

			enable_operators();
			let myForm = dom.byId(params.formId);
			myForm.action = "./ajax.php?module=dsi&categ=search&sub=get_data_search";
			let formData = JSON.parse(domForm.toJson(myForm));
			request.post(myForm.action, {
				data: {
					...formData,
					entity_type: params.entity_type,
				},
				handleAs: 'json'
			}).then(lang.hitch(this, function (data) {
				if (Object.keys(data).length > 0) {
					this.hideDialog(params);
					this.updateSetDom(data);
				} else {
					alert(data.message);
				}
			}), function (err) {
				alert(pmbDojo.messages.getMessage('search_universes', 'search_segment_set_not_save'));
			});
		},

		updateSetDom : function(data) {
			let return_data = {};
			if (typeof data.human_query !== 'undefined') {
				dom.byId('idRMCSelector_human_' + this.uniqueId).innerHTML = data.human_query;
				return_data.human_query = data.human_query;
			}
			if (typeof data.search_serialize !== 'undefined') {
				dom.byId('idRMCSelector_search_serialize_' + this.uniqueId).value = data.search_serialize;
				return_data.search_serialize = data.search_serialize;
			}
			if (typeof data.search !== 'undefined') {
				return_data.search = data.search;
				dom.byId('idRMCSelector_data_' + this.uniqueId).value = data.search;
				let event = new CustomEvent('changeRMCData_' + this.uniqueId, {
					detail: {
						data: return_data
					}
				});
				window.dispatchEvent(event);
			}
		},
	});
});