// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ManageSearch.js,v 1.10 2022/07/26 10:44:59 dgoron Exp $

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
		constructor: function(objects_type) {
			this.objects_type = objects_type;
			if(dom.byId(this.objects_type+'_search_img')) {
				on(dom.byId(this.objects_type+'_search_img'), 'click', lang.hitch(this, this.contentShow));
			}
			var filtersNodes = document.querySelectorAll("."+this.objects_type+"_search_content_filters_delete");
			if(filtersNodes.length) {
				for(var i=1; i<=filtersNodes.length; i++) {
					var filter = dom.byId(this.objects_type+'_search_content_filter_delete_'+i);
					if(filter) {
						on(filter, 'click', lang.hitch(this, this.filterDelete, i, filter.getAttribute('data-property')));
					}
				}
			}
			if(dom.byId(this.objects_type+'_add_filter')) {
				var addFilter = dom.byId(this.objects_type+'_add_filter');
				on(addFilter, 'change', lang.hitch(this, this.filterAdd));
			}
			if(dom.byId(this.objects_type+'_applied_sort_more')) {
				on(dom.byId(this.objects_type+'_applied_sort_more'), 'click', lang.hitch(this, this.appliedSortMore));
			}
			var nodes = document.querySelectorAll("."+this.objects_type+"_applied_sort_delete");
			if(nodes.length) {
				for(var i=1; i<=nodes.length; i++) {
					on(dom.byId(this.objects_type+'_applied_sort_delete_'+i), 'click', lang.hitch(this, this.appliedSortDelete, i));
				}
			}
		},
		contentShow: function() {
			var domNode = dom.byId(this.objects_type+'_search_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_search_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_search_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		},
		existsOthersFilters: function(data) {
			for (filter in data) {
				if(filter.startsWith('empty_') == false) {
					return true;
				}
			}
			return false;
		},
		filterDelete: function(ind, property) {
			var selectedFilters = dom.byId(this.objects_type+'_selected_filters');
			if(selectedFilters) {
				var data = JSON.parse(selectedFilters.value);
				delete data[property];
				if(!this.existsOthersFilters(data)) {
					data = {};
				}
				var domNodeAddFilter = dom.byId(this.objects_type+'_add_filter');
				if(domNodeAddFilter) {
					var number = parseInt(domAttr.get(domNodeAddFilter, 'data-filters-number'));
					number--;
					domAttr.set(domNodeAddFilter, 'data-filters-number', number);
					var options = domNodeAddFilter.childNodes;
					for(i = 0; i < options.length; i++) {
			            if(options[i].value == property) {
			            	domAttr.remove(options[i], 'disabled');
							domStyle.set(options[i], 'display', '');
			                break;
			            }
			        }
				}
				selectedFilters.value = JSON.stringify(data);
			}
			var domNode = dom.byId(this.objects_type+'_search_content_filter_delete_'+ind);
			domNode.parentNode.parentNode.innerHTML = '';
			
			var jsonFilters = dom.byId(this.objects_type+'_json_filters');
			if(jsonFilters) {
				xhr('./ajax.php?module=ajax&categ=list&sub=options&action=filter_delete&objects_type='+this.objects_type+'&filter_property='+property, {
					sync: false,
				}).then(lang.hitch(this, 
						function(response){
							var data = JSON.parse(jsonFilters.value);
							delete data[property];
							jsonFilters.value = JSON.stringify(data);
						})
				);
			}
		},
		filterAdd: function(property) {
			var domNode = dom.byId(this.objects_type+'_add_filter');
			var property = domNode[domNode.selectedIndex].value;
			var label = domAttr.get(domNode[domNode.selectedIndex], 'data-property-code');
			var number = domAttr.get(domNode, 'data-filters-number');
			xhr('./ajax.php?module=ajax&categ=list&sub=options&action=get_search_filter_selector&objects_type='+this.objects_type+'&filter_property='+property+'&filter_label='+encodeURIComponent(label), {
				sync: false,
			}).then(lang.hitch(this, 
					function(response){
						var domNodeAddFilter = dom.byId(this.objects_type+'_add_filter');
						var selectedFilters = dom.byId(this.objects_type+'_selected_filters');
						if(selectedFilters) {
							var data = JSON.parse(selectedFilters.value);
							var property = domNodeAddFilter[domNodeAddFilter.selectedIndex].value;
							var label = domAttr.get(domNodeAddFilter[domNodeAddFilter.selectedIndex], 'data-property-code');
							data[property] = label;
							selectedFilters.value = JSON.stringify(data);
							domAttr.set(domNodeAddFilter[domNodeAddFilter.selectedIndex], 'disabled', 'disabled');
							domStyle.set(domNodeAddFilter[domNodeAddFilter.selectedIndex], 'display', 'none');
						}
						var domNodeContentFilters = dom.byId(this.objects_type+'_search_content_filters');
						domConstruct.place(response, domNodeContentFilters);
						
						var number = parseInt(domAttr.get(domNodeAddFilter, 'data-filters-number'));
						number++;
						domAttr.set(domNodeAddFilter, 'data-filters-number', number);
						
						//Evenement pour la suppression du filtre
						var filter = dom.byId(this.objects_type+'_search_content_filter_delete_'+number);
						if(filter) {
							on(filter, 'click', lang.hitch(this, this.filterDelete, number, filter.getAttribute('data-property')));
						}
						preLoadScripts(domNodeContentFilters);
					})
			);
		},
		appliedSortMore: function() {
			var domNode = dom.byId(this.objects_type+'_applied_sort_more_content');
			var number = domAttr.get(domNode, 'data-applied-sort-number');
			// Limitons à 3 critères pour le moment
			if(number >= 3) {
				alert(pmbDojo.messages.getMessage('list', 'list_ui_sort_by_max_reached'));
				return;
			}
			xhr('./ajax.php?module=ajax&categ=list&sub=options&action=get_search_order_selector&objects_type='+this.objects_type+'&id='+number, {
				sync: false,
			}).then(lang.hitch(this, 
					function(response){
						var domNode = dom.byId(this.objects_type+'_applied_sort_more_content');
						var number = domAttr.get(domNode, 'data-applied-sort-number');
						domNode.innerHTML += response;
						number++;
						domAttr.set(domNode, 'data-applied-sort-number', number); 
					})
			);
		},
		appliedSortDelete: function(ind) {
			var domNode = dom.byId(this.objects_type+'_applied_sort_'+ind);
			domNode.innerHTML = '';
		}
	});
});