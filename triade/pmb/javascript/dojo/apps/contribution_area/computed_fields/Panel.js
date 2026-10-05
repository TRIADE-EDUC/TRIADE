// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Panel.js,v 1.16 2022/05/24 08:53:14 rtigero Exp $

define(['dojo/_base/declare', 
        'dijit/layout/ContentPane', 
        'dojo/request/xhr', 
        'dojo/dom',
        'dojo/dom-construct',
        'dojo/dnd/Target',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/Deferred',
        'dojo/query',
        'dojo/dom-attr',
        'dojo/json'
], function(declare, ContentPane, xhr, dom, domConstruct, dndTarget, on, lang, topic, Deferred, query, domAttr, dojoJson){
	return declare(ContentPane, {
		modified: false,
		field: {},
		constructor: function() {
			this.inherited(arguments)
			topic.subscribe('dblClick', lang.hitch(this, this.display))
		},
		
		display: function(item, node, evt) {
			if (item.type != 'property') {
				return false;
			}
			if (this.confirmUnload()) {
				this.modified = false;
				this.destroyDescendants(false);
				var promise = this.getFieldFromDb(item, node, evt);
				promise.then(lang.hitch(this, function() {
					var container = domConstruct.create('div', {innerHTML: '<h3>' + this.getBreadcrumb(node) + '</h3>'}, this.domNode);
					domConstruct.create('input', {type: 'hidden', name: 'computed_fields_id', id: 'computed_fields_id', value: this.field.id}, container);
					domConstruct.create('input', {type: 'hidden', name: 'computed_fields_field_num', id: 'computed_fields_field_num', value: this.field.field_num}, container);
					domConstruct.create('label', {innerHTML : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_computed_fields_used')}, container);
					domConstruct.create('br', {}, container);
					
					var computed_fields_used = domConstruct.create('div', {id: 'computed_fields_used', style:'border: 1px solid #f0f0f0; min-width: 120px; min-height: 40px;'}, container);
					if (this.field.fields_used.length) {
						this.field.fields_used.forEach(field => {
							this.addOption({value: field.field_num, id: (field.id ? field.id : 0), innerHTML: field.label, alias: field.alias}, computed_fields_used);
						});
					}
					this.own(this.setDndTarget(computed_fields_used));
					
					domConstruct.create('br', {}, container);
					domConstruct.create('label', {innerHTML : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_computed_fields_generated')}, container);
					domConstruct.create('br', {}, container);
					var ComputedFieldsGenerated = domConstruct.create('textarea', {id: 'computed_fields_template', value: this.field.template}, container);
					domConstruct.create('br', {}, container);
					pmbDojo.aceManager.initEditor('computed_fields_template', 'javascript');
					
					var saveButton = domConstruct.create('input', {type: 'button',
						"class": 'bouton',
						value: pmbDojo.messages.getMessage('contribution_area', 'contribution_area_computed_fields_save')
					}, container);
					this.own(on(saveButton, 'click', lang.hitch(this, this.save, item.uniqueId)));
					
					var deleteButton = domConstruct.create('input', {type: 'button',
						"class": 'bouton',
						value: pmbDojo.messages.getMessage('contribution_area', 'contribution_area_computed_fields_delete')
					}, container);
					this.own(on(deleteButton, 'click', lang.hitch(this, this.deleteField, item.uniqueId)));
					
					this.startup();
				}));
			}
		},
		
		getBreadcrumb: function(node) {
			var label = '';
			for (var element of node.tree.path) {
				if (element.type != 'root') {
					if (label) {
						label = label + ' > ';
					}
					label = label + element.name;
				}
			}
			return label;
		},
		
		confirmUnload: function() {
			if (this.modified) {
				return confirm(pmbDojo.messages.getMessage('contribution_area', 'contribution_area_computed_fields_confirm'));
			}
			return true;
		},
		
		getFieldFromDb: function(item, node, evt) {
			this.field = {};
			var deferred = new Deferred();
			xhr('./ajax.php?module=modelling&categ=computed_fields&sub=get_data&field_num=' + item.uniqueId).then(
				lang.hitch(this, function(data){
					this.field = JSON.parse(data);
					deferred.resolve('success');
				})
			);
			return deferred.promise;
		},
		
		save: function(field_num) {
			var fields_used = [];
			query('#computed_fields_used > div').forEach(function(node) {
				var field_used = {
						field_num: domAttr.get(node, 'data-pmb-field-num'),
						id: domAttr.get(node, 'data-pmb-field-id'),
						label: query('.fieldUsedLabel', node)[0].innerHTML,
						alias: query('.fieldUsedAlias', node)[0].value
				};
				fields_used.push(field_used);
			});
			xhr.post('./ajax.php?module=modelling&categ=computed_fields&sub=save&field_num=' + field_num, {
				data: {
					computed_field_area_num: dom.byId('contribution_area_num').value,
					computed_field_id: dom.byId('computed_fields_id').value,
					computed_field_field_num: dom.byId('computed_fields_field_num').value,
					computed_field_template: dom.byId('computed_fields_template').value,
					computed_field_fields_used: dojoJson.stringify(fields_used)
				}
			}).then(lang.hitch(this, function(data) {
				var data = JSON.parse(data);
				this.field.id = data.id;
				if(dom.byId('computed_fields_id')){
					dom.byId('computed_fields_id').value = data.id;
				}
				this.modified = false;
				topic.publish("dGrowl", pmbDojo.messages.getMessage('frbr', 'frbr_save_done'), {'sticky' : false, 'duration' : 5000, 'channel' : 'info'});
			}))
			
		},
		
		deleteField : function(field_num) {
			if (confirm(pmbDojo.messages.getMessage('contribution_area', 'contribution_area_computed_fields_confirm_delete'))) {
				xhr.post('./ajax.php?module=modelling&categ=computed_fields&sub=delete&field_num=' + field_num, {
					data: {
						computed_field_id: dom.byId('computed_fields_id').value,
					}
				}).then(lang.hitch(this, function() {
					this.modified = false;
					topic.publish("dGrowl", pmbDojo.messages.getMessage('contribution_area', 'contribution_area_computed_fields_deleted'), {'sticky' : false, 'duration' : 5000, 'channel' : 'info'});
					this.destroyDescendants(false);
				}))
			}
		},
		
		setDndTarget: function (field) {
			
			var target = new dndTarget(field);
			
			target.update = function() {
				return true;
			}
			// Vérification ci-dessous, correction pour le update
			target.onDndSourceOver = function() {
				return true;
			}
			target.checkItemAcceptance = function() {
				return true;
			}
			
			target.checkAcceptance = function(source, nodes) {
				var item = source.tree.selectedItem;
				if (source.tree.isLeaf(item)) return true;
				return false;
			}

			target.onDndDrop = (source,nodes,copy,target) => {
				var item = source.tree.selectedItem;
				var text = item.name;
				var options = this.getOptions(field.id);
				if (options.indexOf(item.uniqueId) == -1) {
					this.addOption({value: item.uniqueId, innerHTML: this.getBreadcrumb(source), id: 0, alias: 'alias_' + (options.length+1)}, field);
					this.modified = true;
				}
			}
			return target;
		},
		
		addOption: function(option, parent) {
			var item = domConstruct.create('div', {id: option.value, "data-pmb-field-num": option.value, "data-pmb-field-id": option.id}, parent);
			domConstruct.create('span', {"class": 'fieldUsedLabel', innerHTML: option.innerHTML}, item);
			domConstruct.create('input', {type: 'text', "class": 'fieldUsedAlias', id: option.value + '_alias', value: option.alias}, item);
			var greenCheck = domConstruct.create('span', {"class": 'greenCheck', onclick: lang.hitch(this, this.addInTemplate, option.value)}, item);
			domConstruct.create('img', {src: './images/tick.gif', alt: 'inserer'}, greenCheck);
			var redCross = domConstruct.create('span', {"class": 'redCross', onclick: lang.hitch(this, this.removeOption, option.value)}, item);
			domConstruct.create('img', {src: './images/trash.png', alt: 'supprimer'}, redCross);
		},
		
		removeOption: function(id) {
			domConstruct.destroy(id);
		},
		
		addInTemplate: function(id) {
			var alias = dom.byId(id + '_alias').value + '.value';
			pmbDojo.aceManager.getEditor('computed_fields_template').insert(alias);
			pmbDojo.aceManager.getEditor('computed_fields_template').focus();
		},
		
		getOptions: function(parentId) {
			var options = [];
			for (var child of document.getElementById(parentId).children) {
				options.push(domAttr.get(child, 'data-pmb-field-num'));
			}
			return options;
		}
	})
})