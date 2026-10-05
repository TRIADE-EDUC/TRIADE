// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ButtonFunctions.js,v 1.2 2021/07/01 12:20:06 qvarin Exp $


define([
        'dojo/_base/declare',
        "dojo/ready", 
        "dojo/dom", 
        "dojo/dom-construct", 
        "dojo/dom-attr", 
        "dojo/number", 
        "dojo/query!css3", 
        "dojo/on", 
        "dijit/registry",
        "apps/contribution_area_form/datatypes/ResourceSelector",
        "apps/contribution_area_form/datatypes/MemorySelector",
        "dijit/form/TextBox",
        "dijit/form/Button",
        "dojo/_base/lang"
], function(declare, ready, dom, domConstruct, domAttr, number, query, on, registry, ResourceSelector, MemorySelector, TextBox, Button, lang){
	return declare(null, {
		formId : "",
		
		constructor : function(kwArgs) {
			
			lang.mixin(this, kwArgs);
			
			//add_resource_selector
			query("*[id $= \'_add_resource_selector\']", this.formId).forEach(lang.hitch(this,function(node) {
				var myWidget = registry.byId(node.id);	
				if (myWidget) {
					on(myWidget, "click", lang.hitch(this, this.onto_add_selector, myWidget.elementName, myWidget.elementOrder));
				}			
			}));
			
			//add_card
			query("*[id $= \'_add_card\']", this.formId).forEach(lang.hitch(this,function(node) {
				var myWidget = registry.byId(node.id);	
				if (myWidget) {
					on(myWidget, "click", lang.hitch(this, this.onto_add_card, myWidget.elementName, myWidget.elementOrder))
				}			
			}));
			
			//_add_text_link
			query("*[id $= \'_add_text_link\']", this.formId).forEach(lang.hitch(this,function(node) {
				var myWidget = node;
				if (myWidget && !myWidget.alreadyParsed) {
					myWidget.alreadyParsed = 1;
					on(myWidget, "click", lang.hitch(this, this.onto_add_text_link, myWidget))
				}
			}));
			
			//add
			query("*[id $= \'_add\']", this.formId).forEach(lang.hitch(this,function(node) {
				var myWidget = registry.byId(node.id);	
				if (myWidget) {
					on(myWidget, "click", lang.hitch(this, this.onto_add_card, myWidget.elementName, myWidget.elementOrder))
				}			
			}));
		},
		
		onto_add_selector : function(element_name,element_order) {
			var new_order_element = dom.byId(element_name+"_new_order");
			var lastElement = registry.byId(element_name+"_"+new_order_element.value+"_display_label");
			var new_order = number.parse(new_order_element.value)+1;
			new_order_element.value = new_order;
			
			var parent = dom.byId(element_name);
			var new_child="";
			
			//div container
			var new_container = domConstruct.create("div",{
				id : element_name + "_" + new_order, 
				"class" : "row"
			});			
			
			//div pour le memory store
			var memorySelector = new MemorySelector();			
		
			//input pour le sélecteur de ressource
			var resourceSelector = new ResourceSelector({
				id : element_name+"_"+new_order+"_display_label",
				name : element_name+"["+new_order+"][display_label]",
				store : memorySelector,
				searchAttr : 'datas', 
				labelAttr : 'datas', 
				valueNodeId : element_name + "_" + new_order +"_value",
				query : {
 					completion : lastElement.query.completion,
 					autexclude : lastElement.query.autexclude,
 					param1 : lastElement.query.param1,
 					param2 : lastElement.query.param2,
 					handleAs : "json"		
 				}
			});			
			resourceSelector.placeAt(new_container);
			console.log(resourceSelector);
			
			//input value
			var textBoxValue = new TextBox({
					type : "hidden",
					id : element_name+"_"+new_order+"_value",
					name : element_name+"["+new_order+"][value]",
					value : "",
			});
			textBoxValue.placeAt(new_container);
			
			//input type 
			var textBoxType = new TextBox({
					type : "hidden",
					id : element_name+"_"+new_order+"_type",
					name : element_name+"["+new_order+"][type]",
					value : domAttr.get(dom.byId(element_name+"_0_type"), 'value')
			});
			textBoxType.placeAt(new_container);					
			
			//button delete
			var buttonDelete = new Button({
				type : "button",
				"class" : "bouton_small",
				label : "X",
				"onclick" : "onto_remove_selector_value('"+element_name+"','"+new_order+"')"
			});
			buttonDelete.placeAt(new_container);
			
			parent.appendChild(new_container);
		},
		
		onto_add_card : function(element_name,max_card) {
			var new_order_element = dom.byId(element_name+"_new_order");
			var new_order = number.parse(new_order_element.value)+1;
 			new_order_element.value=new_order;

			var parent = dom.byId(element_name);
 			
 			//div container
			var new_container = domConstruct.create("div",{
				id : element_name + "_" + new_order, 
				"class" : "row"
			});	
			
			//input value
			var textBoxValue = new TextBox({
					type : "text",
					id : element_name+"_"+new_order+"_value",
					name : element_name+"["+new_order+"][value]",
					"class" : "saisie-80em",
					value : "",
			});
			textBoxValue.placeAt(new_container);
			
			//button delete
			var buttonDelete = new Button({
				id : element_name+"_"+new_order+"_del_card",
				type : "button",
				"class" : "bouton_small",
				label : "X",
				"onclick" : "onto_del_card('"+element_name+"','"+new_order+"')"
			});
			buttonDelete.placeAt(new_container);
			
			//input type 
			var textBoxType = new TextBox({
					type : "hidden",
					id : element_name+"_"+new_order+"_type",
					name : element_name+"["+new_order+"][type]",
					value : "",
			});
			textBoxType.placeAt(new_container);		
			
			parent.appendChild(new_container);
		},
		
		onto_add_text_link : function(myWidget) {
			var element_name = domAttr.get(myWidget, "data-element-name");
			var max_card = domAttr.get(myWidget, "data-element-order");
			
			var new_order_element = dom.byId(element_name+"_new_order");
			var old_order = number.parse(new_order_element.value);
			var new_order = number.parse(new_order_element.value)+1;
 			new_order_element.value = new_order;

			var parent = dom.byId(element_name);
 			
 			// Div container
			var new_container = domConstruct.create("div",{
				id : element_name + "_" + new_order, 
				"class" : "row contribution_area_flex"
			});	
			
			// Lien check
			var old_div = dom.byId(element_name+"_"+old_order+"_lien_check");
			if (old_div) {
				var new_div = lang.clone(old_div);
				new_div.id = element_name+"_"+new_order+"_lien_check";
				new_div.style.display = "inline";
				new_div.innerHTML = "";
				new_container.appendChild(new_div);
			}
			
			// Input value
			var old_input = dom.byId(element_name+"_"+old_order+"_value");
			if (old_input) {
				var new_input_value = lang.clone(old_input);
				new_input_value.id = element_name+"_"+new_order+"_value";
				new_input_value.name = element_name+"["+new_order+"][value]";
				new_input_value.value = "";
				new_container.appendChild(new_input_value);
			}

			// Button delete
			var old_button = dom.byId(element_name+"_"+old_order+"_del");
			if (old_button) {
				var new_id = element_name+"_"+new_order+"_del";
				var delete_button = lang.clone(old_button);
				delete_button.type = "button";
				delete_button.id = new_id;
				delete_button.onclick = "onto_remove_selector_value('"+element_name+"','"+new_order+"')";
				new_container.appendChild(delete_button);
			}
			
			// Button Open Link
			var old_button = dom.byId(element_name+"_"+old_order+"_open_link");
			if (old_button) {
				var new_button = lang.clone(old_button);
				new_button.type = "button";
				new_button.id = element_name+"_"+new_order+"_open_link";
				new_container.appendChild(new_button);
			}
			
			// Input type 
			var old_input = dom.byId(element_name+"_"+old_order+"_type");
			if (old_input) {
				var new_input_value = lang.clone(old_input);
				new_input_value.id = element_name+"_"+new_order+"_type";
				new_input_value.name = element_name+"["+new_order+"][type]";
				new_container.appendChild(new_input_value);
			}
			
			parent.appendChild(new_container);
			
			// Button search
			var old_button = dom.byId(element_name+"_"+old_order+"_search");
			if (old_button) {
			    var new_id = element_name+"_"+new_order+"_search";
			    var search_button = lang.clone(old_button);
			    search_button.id = new_id
			    new_container.appendChild(search_button);
			    topic.publish('ButtonFunction', 'addEventOnButton', {node: search_button});
			}
			
			// Button edit
			var new_id = element_name+"_"+new_order+"_edit";			
			var old_button = dom.byId(element_name+"_"+old_order+"_edit");			
			if (old_button){
				domConstruct.create("input", {
					type : "hidden",
					"class" : old_button.className,
					id: new_id,
					value : old_button.value,
					"data-edit_label": domAttr.get(old_button, "data-edit_label"),
					"data-form_url": domAttr.get(old_button, "data-form_url"),
					"data-linked_scenario": domAttr.get(old_button, "data-linked_scenario"),
					"data-form_property": domAttr.get(old_button, "data-form_property"),
				}, new_container);
				old_button.value += " / "+domAttr.get(old_button, "data-edit_label");
				topic.publish('ButtonFunction', 'addEventOnButton', {node: dom.byId(new_id)});
			}

			// Button add
			var old_button = dom.byId(element_name+"_"+old_order+"_sel");
			if (old_button) {
			    var new_id = element_name+"_"+new_order+"_sel";			
			    var add_button = lang.clone(old_button);
				add_button.type = "button";
			    add_button.id = new_id;
			    new_container.appendChild(add_button);
			    topic.publish('ButtonFunction', 'addEventOnButton', {node: add_button});
			}
			
			domConstruct.place(myWidget, new_container);
		},
		
//		onto_add_merge_property : function(element_name,element_order) {
//			var newOrder = parseInt(domAttr.get(dom.byId(element_name+'_new_order'), 'value'))+1;
//			var mergePropertiesTemplate = window[element_name+'_template'];
//			//Tempo 
//			mergePropertiesTemplate = mergePropertiesTemplate.replace(/&amp;/g, '&');
//			mergePropertiesTemplate = mergePropertiesTemplate.replace(/&quot;/g, '\\"');
//			
//			mergePropertiesTemplate = mergePropertiesTemplate.replace(/&lt;/g, '<');
//			mergePropertiesTemplate = mergePropertiesTemplate.replace(/&gt;/g, '>');

//			mergePropertiesTemplate = mergePropertiesTemplate.replace(/\[0\]/g, '['+newOrder+']');
//			mergePropertiesTemplate = mergePropertiesTemplate.replace(/_0_/g, '_'+newOrder+'_');
//			mergePropertiesTemplate = mergePropertiesTemplate.replace(/_0/g, '_'+newOrder);

//			mergePropertiesTemplate = mergePropertiesTemplate.replace(/new_order/g, 'new_order_'+newOrder);
//			console.log('mergePropertiesTemplate', mergePropertiesTemplate);
			
//			var div = domConstruct.place(mergePropertiesTemplate, dom.byId(element_name));
//			parser.parse(div);
//			domAttr.set(dom.byId(element_name+'_new_order'), 'value', newOrder);
//		}
	})
});