// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ButtonAddAuthority.js,v 1.2 2021/08/24 13:41:52 qvarin Exp $

define([
	'dojo/_base/declare',
	'dijit/form/Button',
    'dojo/dom',
	'dojo/_base/lang',
	'dojo/query!css3',
	'dojo/dom-attr',
	'dojo/on',
	'dojo/dom-construct',
], function(declare, Button, dom, lang, query, domAttr, on, domConstruct) {
	return declare(Button, {
		
		elementName: '',
		
		elementOrder: 0,
		
		parentNode: null,
		
		orderNode: null,
		
		postCreate: function () {
			this.inherited(arguments);
			
			var parentNode = dom.byId(this.elementName);
			if (parentNode) {
				this.parentNode = parentNode;
			}
			
			var orderNode = dom.byId(this.elementName + "_new_order");
			if (orderNode) {
				this.orderNode = orderNode;
			}
		},
		
		getElementOrder: function() {
			if (this.orderNode) {
				var order = parseInt(this.orderNode.value);
				this.elementOrder = order;
				return order;
			}
			return parseInt(elementOrder);
		},
		
		onClick: function () {
			
			var currentOrder = this.getElementOrder();
			var newOrder = currentOrder+1;
			
			if (this.parentNode) {	
				
				var init_ajax = true;			
				
				// Container
				var oldContainer = dom.byId(this.elementName + "_" + currentOrder);
				var newContainer = lang.clone(oldContainer);
				newContainer.id = this.elementName + "_" + newOrder;
				
				this.cleanContainer(newContainer);
				
				// Prefix
				var oldPrefixId = this.elementName + "_" + currentOrder;
				var prefixId = newContainer.id;
				var prefixName = this.elementName + "["+ newOrder +"]";
	
				// Relation Type
				var selectRelationType = query("select[id='"+ oldPrefixId +"_relation_type_authority"+"']", newContainer);
				if (selectRelationType && selectRelationType.length > 0) {
					selectRelationType[0].id = prefixId +"_relation_type_authority";
					selectRelationType[0].name = prefixName +"[relation_type_authority]";
				}
				
				// Image Plus
				var img = query("img[id='"+ oldPrefixId +"_img_plus']", newContainer);
				if (img && img.length > 0) {
					img[0].id = prefixId +"_img_plus";
					domAttr.set(img[0], "data-prefix", newContainer.id)
				}
				
				// Display Label
				var inputDisplayLabel = query("input[id='"+ oldPrefixId +"_display_label']", newContainer);
				if (inputDisplayLabel && inputDisplayLabel.length > 0) {
					inputDisplayLabel[0].id = prefixId +"_display_label";
					inputDisplayLabel[0].name = prefixName +"[display_label]";
					inputDisplayLabel[0].value = "";
					
					init_ajax &= true;
				} else {
					init_ajax &= false;					
				}
				
				// Hidden Type
				var inputType = query("input[id='"+ oldPrefixId +"_type']", newContainer);
				if (inputType && inputType.length > 0) {
					inputType[0].id = prefixId +"_type";
					inputType[0].name = prefixName +"[type]";
					
					// Parfois on a 2 input
					if (inputType[1]) {
						inputType[1].id = prefixId +"_type";
						inputType[1].name = prefixName +"[type]";
					}
				}
				
				// Hidden Value
				var inputValue = query("input[id='"+ oldPrefixId +"_value']", newContainer);
				if (inputValue && inputValue.length > 0) {
					inputValue[0].id = prefixId +"_value";
					inputValue[0].name = prefixName +"[value]";
					inputValue[0].value = "";
					
					// Init Autocomplete
					domAttr.set(inputDisplayLabel[0], "autfield",  prefixId +"_value");
					init_ajax &= true;
				} else {
					init_ajax &= false;					
				}
				
				// Delete
				var inputDelete = query("input[id='"+ oldPrefixId +"_del']", newContainer);
				if (inputDelete && inputDelete.length > 0) {
					inputDelete[0].id = prefixId +"_del";
					inputDelete[0].onclick = null;
					domAttr.remove(inputDelete[0], "onclick");
					if (typeof onto_remove_selector_value == "function") {						
						on(inputValue[0], "click", lang.hitch(inputDelete[0], onto_remove_selector_value, 'author_66565_has_linked_authority', newOrder));
					}
				}
				
				// Hidden Div
				var divCommentArea = query("div[id='"+ oldPrefixId +"_comment_area"+"']", newContainer);
				if (divCommentArea && divCommentArea.length > 0) {
					divCommentArea[0].id = prefixId +"_comment_area";
				}
				
				// Start Date
				var inputStartDate = query("input[id='"+ oldPrefixId +"_start_date']", newContainer);
				if (inputStartDate && inputStartDate.length > 0) {
					inputStartDate[0].id = prefixId +"_start_date";
					inputStartDate[0].name = prefixName +"[start_date]";
					inputStartDate[0].value = "";
				}
				
				// End Date
				var inputEndDate = query("input[id='"+ oldPrefixId +"_end_date']", newContainer);
				if (inputEndDate && inputEndDate.length > 0) {
					inputEndDate[0].id = prefixId +"_end_date";
					inputEndDate[0].name = prefixName +"[end_date]";
					inputEndDate[0].value = "";
				}
				
				// Comment
				var textareaComment = query("textarea[id='"+ oldPrefixId +"_comment']", newContainer);
				if (textareaComment && textareaComment.length > 0) {
					textareaComment[0].id = prefixId +"_comment";
					textareaComment[0].name = prefixName +"[comment]";
					textareaComment[0].value = "";
				}
				
				if (newContainer.firstElementChild.nodeName == "DIV") {					
					domConstruct.place(this.domNode, newContainer.firstElementChild, "last");
				} else {
					domConstruct.place(this.domNode, newContainer, "last");					
				}
				
				this.parentNode.appendChild(newContainer);
				this.orderNode.value = newOrder;
				this.elementOrder = newOrder;
				
				if (init_ajax && typeof ajax_pack_element == "function") {
					ajax_pack_element(inputDisplayLabel[0]);
				}
			}			
		},
		
		cleanContainer: function(container) {
			// Remove dojo widget
			query("span[widgetid='"+this.elementName+"_add_linked_authority']", container).forEach(lang.hitch(this, function(widget) {
				domConstruct.destroy(widget);					
			}));
			
			// Remove script
			query("script", container).forEach(lang.hitch(this, function(script) {
				domConstruct.destroy(script);					
			}));
		}
	});
});