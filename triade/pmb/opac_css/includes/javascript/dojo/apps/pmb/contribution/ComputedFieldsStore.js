// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ComputedFieldsStore.js,v 1.20 2022/05/30 13:17:26 qvarin Exp $

define([
	'dojo/_base/declare',
	'dojo/store/Memory',
	'dojo/request',
	'dojo/_base/lang',
	'dojo/on',
	'dojo/query',
	'dojo/topic',
	'dojo/Deferred',
	'dojo/dom',
	'dojo/dom-attr',
	'dojo/promise/all',
	'dijit/registry',
], function(declare, Memory, request, lang, on, query, topic, Deferred, dom, domAttr, promiseAll, registry) {
	return declare(Memory, {
		
		fieldsToModify: null,
		
		entitiesAlreadyRetrieved: [],

		fieldsValue: {},
		
		constructor: function() {
			topic.subscribe("form/change", lang.hitch(this, function(fieldNum){
				if (typeof this.fieldsToModify[fieldNum] != 'undefined') {
					this.updateComputedFields(this.fieldsToModify[fieldNum]);
				}
			}));
			this.deferred = new Deferred();
			request.get('ajax.php?module=ajax&categ=contribution&sub=computed_fields&what=get_fields&area_id='+this.areaId, {
				handleAs: 'json',
				sync: true
			}).then(lang.hitch(this, function(data){
				this.data = data;
				this.initFieldsToModify();
			}));
		},
		
		initFieldsToModify: function() {
			this.fieldsToModify = [];
			for (var field of this.data) {
				for (var fieldUsed of field.fields_used) {
					var fieldNum = fieldUsed.field_num;
					if (fieldUsed.field_num.indexOf("prop_") === 0) {
						var hyphenPos = fieldUsed.field_num.indexOf("-");
						fieldNum = fieldUsed.field_num.substr(5, hyphenPos-5);
					}
					if (typeof this.fieldsToModify[fieldNum] == "undefined") {
						this.fieldsToModify[fieldNum] = [];
					}
					this.fieldsToModify[fieldNum].push(field.field_num);
				}
			}
		},
		
		initFormFields: function(nodeId) {
			this.init(nodeId);
			query('[data-pmb-uniqueid]', nodeId).forEach(lang.hitch(this, function(node){
				this.computeField(domAttr.get(node, 'data-pmb-uniqueid'));
			}));
		},
		
		updateComputedFields: function(fieldsNum) {
			for (var field_num of fieldsNum) {
				this.computeField(field_num);
			}
		},
		
		computeField: function(fieldNum) {
			var field = this.query({field_num: fieldNum});
			if (!field.length) {
				return false;
			}
			var deferred = null;
			var deferredList = [];
			var aliases = [];
			for (var fieldUsed of field[0].fields_used) {
				deferred = new Deferred();
				deferredList.push(deferred);
				aliases.push(fieldUsed.alias);
				if ((fieldUsed.field_num.indexOf("env_") === 0) || (fieldUsed.field_num.indexOf("empr_") === 0)) {
					var data = fieldUsed.value ?? {};
					data.uniqueId = fieldUsed.field_num;
					deferred.resolve(data);
				} else if (fieldUsed.field_num.indexOf("prop_") === 0) {
					var hyphenPos = fieldUsed.field_num.indexOf("-");
					var fieldName = fieldUsed.field_num.substr(5, hyphenPos-5);
					var entityPropertyName = fieldUsed.field_num.substr(hyphenPos+1);
					var hyphenPos2 = entityPropertyName.indexOf("-");
					var entityName = entityPropertyName.substr(0, hyphenPos2);
					var propertyName = entityPropertyName.substr(hyphenPos2+1);
					var subDeferred = new Deferred();
					topic.publish("form/getValues", fieldName, subDeferred);
					var returnValues = {
							uniqueId: fieldUsed.field_num,
							value: '',
							displayLabel: ''
					}
					subDeferred.then(lang.hitch(this, function(data) {
						if (!data.value) {
							deferred.resolve(returnValues);
							return false;
						}
						if ((typeof this.entitiesAlreadyRetrieved[entityName] != 'undefined') && (typeof this.entitiesAlreadyRetrieved[entityName][data.value] != 'undefined')) {
							if (this.entitiesAlreadyRetrieved[entityName][data.value][propertyName]) {
								returnValues.value = this.entitiesAlreadyRetrieved[entityName][data.value][propertyName].value;
								returnValues.displayLabel = this.entitiesAlreadyRetrieved[entityName][data.value][propertyName].display_label;
							}
							deferred.resolve(returnValues);
							return true;
						}
						request('./ajax.php?module=ajax&categ=contribution&sub=computed_fields&what=get_entity_data&entity_id='+data.value+'&entity_type='+entityName, {
							handleAs: 'json'
						}).then(lang.hitch(this, function(result){
							if (typeof this.entitiesAlreadyRetrieved[entityName] == 'undefined') {
								this.entitiesAlreadyRetrieved[entityName] = [];
							}
							this.entitiesAlreadyRetrieved[entityName][data.value] = result;
							if (result[propertyName]) {
								returnValues.value = result[propertyName].value;
								returnValues.displayLabel = result[propertyName].display_label;
							}
							deferred.resolve(returnValues);
						}));
					}));
				} else {
					topic.publish("form/getValues", fieldUsed.field_num, deferred);
					setTimeout(function() {
						if (!deferred.isResolved()) {
							var message = pmbDojo.messages.getMessage("contribution", "contribution_area_computed_fileds_error");
							console.error(message.replace("%s", fieldUsed.field_num));
							deferred.reject(returnValues);
						}
					}, 3000);
				}
			}
			
			promiseAll(deferredList).then(lang.hitch(this, function(results){
				var fieldNodes = query('[data-pmb-uniqueid="'+fieldNum+'"]');
				if (!fieldNodes.length) {
					return false;
				}
				fieldNodes.forEach(lang.hitch(this, function (fieldNode) {
					var functionToExec = new Function(aliases.join(), field[0].template);
					var fieldContent = functionToExec.apply(fieldNode, results);
					
					var fieldValueNode = dom.byId(fieldNode.id + '_0_value');
					var fieldValueTabNode = query('[name="'+fieldNode.id + '[0][value][]"]');
					
					if (fieldValueTabNode && fieldValueTabNode[0]) {
						this.updateFieldValue(fieldValueTabNode[0], fieldContent);	
					}
					
					if (fieldValueNode) {
						this.updateFieldValue(fieldValueNode, fieldContent);	
					}
					
					var fieldDisplayLabelNode = registry.byId(fieldNode.id + '_0_display_label');
					if (fieldDisplayLabelNode) {
						fieldDisplayLabelNode.updateTemplate();
						fieldDisplayLabelNode.updateDisplayLabel();
					}
				}));
			}));
		},
		
		init : function (contentPaneNodeId) {
			const additionnalDataNode = this.getElementByIdFromContentPane('additionnal_data', contentPaneNodeId);
			if (!additionnalDataNode) {
				return false;
			}
			
			var formId = additionnalDataNode.form.id;
			this.fieldsValue[formId] = {};
			const additionnalData = JSON.parse(additionnalDataNode.value);
			if (additionnalData && additionnalData.computed_fields_data) {
				this.fieldsValue[formId] = additionnalData.computed_fields_data;
			}
			
			for (var field of this.data) {				
				const queryCollection = query('[data-pmb-uniqueid="' + field.field_num + '"]');
				queryCollection.forEach(lang.hitch(this, function(node) {
					const fieldValueNode = dom.byId(node.id + '_0_value');
					const fieldValueTabNode = query('[name="' + node.id + '[0][value][]"]');
					
					if (fieldValueTabNode && fieldValueTabNode[0] && !this.fieldsValue[formId][fieldValueTabNode[0].id]) {
						this.fieldsValue[formId][fieldValueTabNode[0].id] = fieldValueTabNode[0].value;						
					}
					if (fieldValueNode && !this.fieldsValue[formId][fieldValueNode.id]) {
						this.fieldsValue[formId][fieldValueNode.id] = fieldValueNode.value;						
					}
				}));
			}
			topic.subscribe('contribution/submit', lang.hitch(this, this.saveData, contentPaneNodeId))
		},

		saveData : function (contentPaneNodeId) {
			var additionnalDataNode = this.getElementByIdFromContentPane('additionnal_data', contentPaneNodeId);
			if (!additionnalDataNode) {
				return false;
			}
			
			const formId = additionnalDataNode.form.id;
			var additionnalData = JSON.parse(additionnalDataNode.value);
			additionnalData.computed_fields_data = this.fieldsValue[formId];
			additionnalDataNode.value = JSON.stringify(additionnalData);
		},

		updateFieldValue : function (domNode, newValue) {
			const formId = domNode.form.id;
			
			if (!domNode.name) {
				// Je sais pas pourquoi, mais le champ n'a pas de name
				return false;
			}

			if (typeof newValue == "undefined") {
				// La nouvelle valeur est incorrecte
				return false;
			}
			
			if (domNode.value != this.fieldsValue[formId][domNode.id]) {
				// Champ modifié par l'utilisateur
				return false;
			}
			
			if (domNode.value == newValue) {
				// La valeur n'a pas changée
				return false;				
			}
			
			domNode.value = newValue;
			this.fieldsValue[formId][domNode.id] = newValue;
			const uniqueid = this.getUniqueidByField(domNode)
			if (uniqueid) {
				// Le champ a été modifiés, on emit un "form/change".			
				topic.publish("form/change", uniqueid);
			}
			return true;
		},
		
		getElementByIdFromContentPane: function(nodeId, contentPane) {
			const queryCollection = query('#' + nodeId, contentPane);
			return queryCollection[0] ?? undefined;
		},
		
		getUniqueidByField: function(domNode) {
			if (!domNode.nodeName || domNode.nodeName == "FORM" || domNode.nodeName == "#document") {			
				return undefined;
			}
			
			if (domNode.attributes && domNode.attributes['data-pmb-uniqueid']) {
				return domNode.attributes['data-pmb-uniqueid'].value;	
			}
			return domNode.parentElement ? this.getUniqueidByField(domNode.parentElement) : undefined;
		}
	});
});