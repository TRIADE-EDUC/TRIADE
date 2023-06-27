
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ParametersRefactor.js,v 1.3 2018-07-09 09:42:21 vtouchard Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/ready",
        "dojo/dom-construct",
        "dojo/dom-style",
        "apps/pmb/PMBDialog",
        "dojo/request/xhr",
        "dojo/dom-form",
        "dojo/dom-class",
], function(declare, lang, request, query, on, domAttr, dom, ready, domConstruct, domStyle, PMBDialog, xhr,domForm, domClass){
	return declare(null, {
		input: null,
		elements: null,
		constructor: function() {
//			expandAll();
			var hmenu = query('div[class="hmenu"]')[0];
			var filterContainer = domConstruct.create('div', {id: 'fast_filter', style:{
				border: '1px solid black',
			    margin: '14px',
			    padding: '20px',
			    backgroundColor: '#e5e5e5',
			}}, hmenu, 'after');
			domConstruct.create('h3', {innerHTML:pmbDojo.messages.getMessage('admin_parameters', 'admin_param_edit_quick_filter')}, 'fast_filter', 'last');
			this.input = domConstruct.create('input', {type:'text', id:'fast_filter_input', placeholder:pmbDojo.messages.getMessage('admin_parameters', 'admin_param_edit_input_placeholder')}, 'fast_filter', 'last');
			this.input.focus();
			on(this.input, 'keyup', lang.hitch(this, this.launchSearch));
			this.elements = query('tr', dom.byId('contenu'));
			this.applyPopupEvent();
		},
		launchSearch: function(){
			var inputValue = this.input.value.toLowerCase();
			var parentTh = null;
			this.elements.forEach(element => {
				if(element.getAttribute('data-search')){
					if(JSON.parse(element.getAttribute('data-search')).search_value.indexOf(inputValue) == -1){
						domStyle.set(element, 'display', 'none');
					}else{
						domStyle.set(element, 'display', 'table-row');
						parentTh = null;
					}
				}else{
					if(parentTh && (parentTh.previousElementSibling != null)){ 
						domStyle.set(parentTh, 'display', 'none');
					}
					parentTh = element;
					domStyle.set(parentTh, 'display', 'table-row');
				}
				var childs = query('tr[class]', element.parentElement);
				var countHidden = 0;
				
				childs.forEach(child => {
					if(child.style && child.style.display == "none"){
						countHidden++;
					}
				});
				var parentNode = element.parentElement.parentElement.parentElement;
				if(countHidden == (childs.length)){
					domStyle.set(parentNode, 'display', 'none');
					domStyle.set(parentNode.id.replace('Child', 'Parent'), 'display', 'none');
				}else{
					domStyle.set(parentNode, 'display', 'block');
					domStyle.set(parentNode.id.replace('Child', 'Parent'), 'display', 'block');
				}
			});
			if(inputValue == ""){
				collapseAll();
			}
		},
		applyPopupEvent: function(){
			this.elements.forEach(element => {
				if(element.firstElementChild && element.firstElementChild.nodeName != 'TH'){
					on(element, 'click', lang.hitch(this, this.openPopup, element.getAttribute('data-param-id')));	
				}
			});
		},
		openPopup: function(elementID){
			var dialog = new PMBDialog({
				title: pmbDojo.messages.getMessage('admin_parameters', 'admin_param_edit_popup_title'),
				href: './ajax.php?module=admin&categ=param&action=modif&form_ajax=1&id_param='+elementID,
			});
			dialog.onLoad = lang.hitch(dialog, function(){
//				var button = query('input[type="button"]', this.containerNode);
//				var submitButton = query('input[type="submit"]', this.containerNode)[0];
//				domAttr.set(submitButton, 'type', 'button');
				var form = query('form', this.containerNode)[0];
				var buttonCancel = query('input[onclick]', this.containerNode);
				if(buttonCancel.length){
					domAttr.remove(buttonCancel[0], 'onclick');
					on(buttonCancel[0], 'click', lang.hitch(this, function(){
						this.hide();
					}));
				}
				on(form, 'submit', lang.hitch(this, function(form, e){
					var formURL = (domAttr.get(form, 'action').split("#")[0]+'&form_ajax=1&module=admin').replace('admin.php', 'ajax.php');
					e.preventDefault();
					console.log(formURL);
					xhr(formURL,{
						data: JSON.parse(domForm.toJson(form)),
						handleAs: "json",
						method:'POST',
					}).then(lang.hitch(this, function(response){
						if (response) {
							var line = query('tr[data-param-id="'+response.param_id+'"]')[0];
							var valueCell = query('td[class="ligne_data"]', line)[0];
							var commentCell = line.children[line.children.length-1];
							valueCell.innerHTML = response.param_value;
							commentCell.innerHTML = response.param_comment;
							domClass.add(line, 'justmodified');
							dialog.hide();
						}
					}));
					return false;
				},form));
			});
			dialog.show();
		}
	});
});
/**
 * 
 */