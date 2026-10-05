// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FavoritesRefactor.js,v 1.3 2020/10/14 10:15:57 btafforeau Exp $

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
			var parammenu = query('div[class~="parammenu"]')[0];
			var filterContainer = domConstruct.create('div', {id: 'fast_filter', style:{
				border: '1px solid black',
			    margin: '14px',
			    padding: '20px',
			    backgroundColor: '#e5e5e5',
			}}, parammenu, 'after');
			domConstruct.create('h3', {innerHTML:pmbDojo.messages.getMessage('admin_parameters', 'admin_param_edit_quick_filter')}, 'fast_filter', 'last');
			this.input = domConstruct.create('input', {type:'text', id:'fast_filter_input', placeholder:pmbDojo.messages.getMessage('admin_parameters', 'admin_param_edit_input_placeholder')}, 'fast_filter', 'last');
			this.input.focus();
			on(this.input, 'keyup', lang.hitch(this, this.launchSearch));
			this.elements = query('tr', dom.byId('favoritesContent'));
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
				}
				var childs = query('tr', element.parentElement);
				var countHidden = 0;
				
				childs.forEach(child => {
					if(child.style && child.style.display == "none"){
						countHidden++;
					}
				});
				
				var parentNode = element.parentElement.parentElement.parentElement;
				if(countHidden == (element.parentElement.childElementCount)){
					domStyle.set(parentNode, 'display', 'none');
					if(parentNode.id) {
						domStyle.set(parentNode.id.replace('Child', 'Parent'), 'display', 'none');
					}
				}else{
					domStyle.set(parentNode, 'display', 'block');
					if(parentNode.id) {
						domStyle.set(parentNode.id.replace('Child', 'Parent'), 'display', 'block');
					}
				}
			});
			if(inputValue == ""){
				collapseAll();
			} else {
				expandAll();
			}
		}
	});
});
/**
 * 
 */