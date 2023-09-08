// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Store.js,v 1.10 2019-02-20 13:26:14 apetithomme Exp $


define(["dojo/_base/declare", 
        "dojo/store/Memory",
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/request'
], function(declare, Memory, lang, topic, request){
	return declare(Memory, {
		availableEntities: {},
		computedFields: [],
		environmentFields : [],
		emprFields: [],
		
		constructor: function() {
			this.data.push({type: 'root'});
			this.availableEntities = new Memory({data: this.availableEntities});
		},
		
		getChildren: function(object, node) {
			switch (object.type) {
				case "property":
					return [];
				case "root":
					var children = this.query({startScenario: 1});
					children.push({type : 'emprFields', name : this.emprFields['label'], id: "empr_environment"});
					for (var key in this.environmentFields) {
						children.push({type : 'environmentFieldsType', name : this.environmentFields[key]['label'], id: key});
					}
					return children;
				case "emprFields":
					var children = [];
					for (var key in this.emprFields.properties) {
						children.push({type : 'emprField', name : this.emprFields.properties[key], id: key, uniqueId : 'empr_'+key});
					}
					return children;
				case "environmentFieldsType":
					var children = [];
					for (var key in this.environmentFields[object.id].properties) {
						children.push({type : 'environmentField', name : this.environmentFields[object.id].properties[key], id: key, uniqueId : 'env_'+key});
					}
					return children;
				case "attachment":
					var scenario = this.query({parent: object.id})[0];
					var children = [
						{type : 'entityCreation', name : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_computed_fields_entity_creation'), linkedScenario: scenario.id, id: scenario.id + '_create'},
						{type : 'entitySelection', name : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_computed_fields_entity_selection'), linkedForm: object.parent, linkedProperty: object.propertyPmbName, id: scenario.id + '_select', entityType: scenario.entityType}
					];
					return children;
				case "entityCreation":
					return this.query({parent: object.linkedScenario});
				case "entitySelection":
					var children = [];
					request('./ajax.php?module=modelling&categ=computed_fields&sub=get_entity_properties&entity_type='+object.entityType, {
						handleAs: 'json',
						sync: true
					}).then(function(data){
						data.forEach(function(prop){
							children.push({
								type: 'property',
								name: prop.name,
								id: prop.id,
								uniqueId: 'prop_'+object.linkedForm+'_'+object.linkedProperty+'-'+object.entityType+'-'+prop.id,
								selection: true
							});
						});
					});
					return children;
				case "form":
					var children = this.query({parent: object.id});

					var properties = this.availableEntities.query({form_id: object.eltId, type: 'property'});
					var newProperty = {};
					properties.forEach(property => {
						newProperty = Object.assign(property);
						newProperty.uniqueId = object.id + '_' + newProperty.pmb_name;
						newProperty.alreadyComputed = false;
						if (this.computedFields.indexOf(newProperty.uniqueId) != -1) {
							newProperty.alreadyComputed = true;
						}
						children.push(newProperty);
					});
					return children;
				default:
					return this.query({parent: object.id});
			}
		}
	});
});
