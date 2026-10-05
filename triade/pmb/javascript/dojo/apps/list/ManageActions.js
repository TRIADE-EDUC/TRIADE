// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ManageActions.js,v 1.5.6.2 2024/06/25 06:38:38 dgoron Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/dom-construct",
        "dojo/dom-style",
        "dojo/request/xhr",
        "dojo/ready"
], function(declare, lang, request, query, on, domAttr, dom, domConstruct, domStyle, xhr, ready){
	return declare(null, {
		objects_type:null,
		actions:null,
		name_selected_objects:null,
		constructor: function(objects_type, actions, name_selected_objects) {
			this.objects_type = objects_type;
			this.actions = actions;
			this.name_selected_objects = name_selected_objects;
			if(dom.byId(this.objects_type+'_selection_action_configuration_edit')) {
				this.initEventsActionEdit();
			}
		},
		addEventsOnSelectionActions: function() {
			//TODO : + tard - deplacer les events de list_ui ici
		},
		initEventsActionEdit: function() {
			var selectorAvailableEditableColumns = dom.byId(this.objects_type+'_available_editable_columns');
			on(selectorAvailableEditableColumns, 'change', lang.hitch(this, this.getSelectionColumnEditionContent, selectorAvailableEditableColumns));
			
			var buttonAvailableEditableColumns = dom.byId(this.objects_type+'_selection_action_configuration_button_edit');
			on(buttonAvailableEditableColumns, 'click', lang.hitch(this, this.applyOnSelectionAction, 'edit'));
		},
		getSelectionColumnEditionContent: function(selectorAvailableEditableColumns) {
			xhr('./ajax.php?module=ajax&categ=list&sub=actions&action=get_selection_column_edition_content&objects_type='+this.objects_type+'&property='+selectorAvailableEditableColumns.value, {
				sync: false,
			}).then(lang.hitch(this, 
					function(response){
						var domNodeValues = dom.byId(this.objects_type+'_selection_action_configuration_values_edit');
						domNodeValues.innerHTML = response;
						domStyle.set(domNodeValues, 'display', 'inline-block');
						
						var domNodeButton = dom.byId(this.objects_type+'_selection_action_configuration_container_edit');
						domStyle.set(domNodeButton, 'display', 'inline-block');
						if(dom.byId(this.objects_type+'_'+selectorAvailableEditableColumns.value)) {
							var domNodeEditableColumn = dom.byId(this.objects_type+'_'+selectorAvailableEditableColumns.value);
							if(domNodeEditableColumn.getAttribute('completion')) {
								ajax_pack_element(dom.byId(this.objects_type+'_'+selectorAvailableEditableColumns.value));
							}
						}
					})
			);
		},
		getAction: function(name) {
			var currentAction = {};
			this.actions.forEach(function(action) {
				if(action.name == name) {
					currentAction = action;
				}
			});
			return currentAction;
		},
		createSelectedObjectsForm: function(href, selection) {
			var selected_objects_form = domConstruct.create('form', {
				action : href,
				name : this.objects_type+'_selected_objects_form',
				id : this.objects_type+'_selected_objects_form',
				method : 'POST'
			});
			selection.forEach(lang.hitch(this, function(selected_option) {
				var selected_objects_hidden = domConstruct.create('input', {
					type : 'hidden',
					name : this.name_selected_objects+'[]',
					value : selected_option
				});
				domConstruct.place(selected_objects_hidden, selected_objects_form);
			}));
			var objects_type_hidden = domConstruct.create('input', {
				type : 'hidden',
				name : 'objects_type',
				value : this.objects_type
			});
			domConstruct.place(objects_type_hidden, selected_objects_form);
			
			var selectorAvailableEditableColumns = dom.byId(this.objects_type+'_available_editable_columns');
			var available_editable_columns_hidden = domConstruct.create('input', {
				type : 'hidden',
				name : this.objects_type+'_available_editable_columns',
				value : selectorAvailableEditableColumns.value
			});
			domConstruct.place(available_editable_columns_hidden, selected_objects_form);
			
			//Inputs fields
			var values = document.querySelectorAll("input[name='"+this.objects_type+"_"+selectorAvailableEditableColumns.value+"']");
			if(values.length) {
				values.forEach(element => {
					var value_hidden = null;
					switch(element.getAttribute('type')) {
						case 'checkbox':
							if(element.checked) {
								value_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : this.objects_type+'_'+selectorAvailableEditableColumns.value+'[]',
									value : element.value
								});
							}
							break;
						case 'radio':
							if(element.checked) {
								value_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : this.objects_type+'_'+selectorAvailableEditableColumns.value,
									value : element.value
								});
							}
							break;
						default :
							value_hidden = domConstruct.create('input', {
								type : 'hidden',
								name : this.objects_type+'_'+selectorAvailableEditableColumns.value,
								value : element.value
							});
							break;
					}
					if(value_hidden) {
						domConstruct.place(value_hidden, selected_objects_form);
					}
				});
			} else {
				//Selectors fields
				var values = document.querySelectorAll("select[name='"+this.objects_type+"_"+selectorAvailableEditableColumns.value+"']");
				if(values.length) {
					values.forEach(element => {
						var value_hidden = null;
						value_hidden = domConstruct.create('input', {
							type : 'hidden',
							name : this.objects_type+'_'+selectorAvailableEditableColumns.value,
							value : element.value
						});
						if(value_hidden) {
							domConstruct.place(value_hidden, selected_objects_form);
						}
					});
				}
			}
			domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
		},
		applyOnSelectionAction: function(name) {
			var action = this.getAction(name);
			var selection = new Array();
			query('.'+this.objects_type+'_selection:checked').forEach(function(node) {
				selection.push(node.value);
			});
			if(selection.length) {
				var confirm_msg = '';
				if(action.link.confirm) {
					confirm_msg = action.link.confirm;
				}
				if(!confirm_msg || confirm(confirm_msg)) {
					if(action.link.href) {
						this.createSelectedObjectsForm(action.link.href, selection);
						dom.byId(this.objects_type+'_selected_objects_form').submit();
					}
					if(action.link.openPopUp) {
						openPopUp(action.link.openPopUp+'&selected_objects='+selection.join(','), action.link.openPopUpTitle); return false;
					}
					if(action.link.onClick) {
						action.link.onClick(selection); return false;
					}
					if(action.link.showConfiguration) {
						this.createSelectedObjectsForm(action.link.showConfiguration, selection);
						dom.byId(this.objects_type+'_selected_objects_form').submit();
					}
				}
			} else {
				alert('No row has been selected !');
			}
		},
	});
});