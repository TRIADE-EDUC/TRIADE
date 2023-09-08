// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ComputedFieldsStore.js,v 1.9 2019-02-26 09:00:16 apetithomme Exp $

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
	'dojo/promise/all'
], function(declare, Memory, request, lang, on, query, topic, Deferred, dom, domAttr, promiseAll) {
	return declare(Memory, {
		
		fieldsToModify: null,
		
		entitiesAlreadyRetrieved: [],
		
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
					var data = fieldUsed.value;
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
							console.error(fieldUsed.field_num + " n'a pas renvoy&eacute; de r&eacute;ponse");
							deferred.resolve(returnValues);
						}
					}, 3000);
				}
			}
			
			promiseAll(deferredList).then(function(results){
				var fieldNode = query('[data-pmb-uniqueid="'+fieldNum+'"]');
				if (!fieldNode.length) {
					return false;
				}
				
				var functionToExec = new Function(aliases.join(), field[0].template);
				var fieldContent = functionToExec.apply(fieldNode[0], results);

				var fieldValueNode = dom.byId(fieldNode[0].id + '_0_value');
				fieldValueNode.value = fieldContent;
			});
		}
	});
});