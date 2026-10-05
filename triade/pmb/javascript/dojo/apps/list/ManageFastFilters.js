// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ManageFastFilters.js,v 1.5.6.1 2024/05/22 14:05:16 dgoron Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/dom-style",
        "dojo/request/xhr",
        "dojo/ready"
], function(declare, lang, request, query, on, domAttr, dom, domStyle, xhr, ready){
	return declare(null, {
		ajax_controller_url_base:null,
		objects_type:null,
		all_on_page:null,
		inputs: null,
		elements: null,
		request_timeout:null,
		constructor: function(ajax_controller_url_base, objects_type, all_on_page) {
			this.ajax_controller_url_base = ajax_controller_url_base;
			this.objects_type = objects_type;
			this.all_on_page = parseInt(all_on_page);
			this.parseDom();
			this.elements = document.querySelectorAll("."+this.objects_type+"_content_object_list");
		},
		getFastFilterClassFromDatatype: function(datatype) {
			switch(datatype) {
				case 'date':
					return this.objects_type+"_list_cell_fast_filter_date";
				default :
					return this.objects_type+"_list_cell_fast_filter";
			}
		},
		parseNodes: function(datatype, event_type) {
			var nodes = document.querySelectorAll("."+this.getFastFilterClassFromDatatype(datatype));
			if(nodes.length) {
				for(var i=0; i<nodes.length; i++) {
					if(nodes[i]) {
						if(this.all_on_page) {
							on(nodes[i], event_type, lang.hitch(this, this.launchSearchOnCurrentPage, nodes[i], datatype));
						} else {
							on(nodes[i], event_type, lang.hitch(this, this.launchSearchOnAllPage, nodes[i], datatype));
						}
					}
				}
			}
		},
		parseDom: function() {
			this.parseNodes('string', 'keyup');
			this.parseNodes('date', 'blur');
		},
		fetchSearchOnCurrentPage: function(node, datatype) {
			var parentTh = null;
			this.elements.forEach(element => {
				var cell = element.querySelectorAll("."+this.objects_type+"_list_cell_content_"+property);
				if(cell.length) {
					if(cell.item(0).textContent.toLowerCase().indexOf(inputValue) == -1){
						domStyle.set(element, 'display', 'none');
					}else{
						domStyle.set(element, 'display', 'table-row');
						parentTh = null;
					}
					var childs = query('tr[class]', element.parentElement);
					var countHidden = 0;
					childs.forEach(child => {
						if(child.style && child.style.display == "none"){
							countHidden++;
						}
					});
					var parentNode = element.parentElement.parentElement.parentElement;
				}
			});
			//Recopions la saisie sur les autres filtres de même propriété
			var filtersNodes = document.querySelectorAll("."+this.getFastFilterClassFromDatatype(datatype));
			if(filtersNodes.length) {
				for(var i=0; i<filtersNodes.length; i++) {
					if(filtersNodes[i]) {
						domAttr.set(filtersNodes[i], 'value', node.value);
					}
				}
			}
//			if(inputValue == ""){
//				collapseAll();
//			} else {
//				expandAll();
//			}
		},
		addSessionSearch: function(node) {
			var property = domAttr.get(node, 'data-property');
			var inputValue = node.value.toLowerCase();
			xhr.post(this.ajax_controller_url_base+'&action=add_session&fast_filter_property='+property+'&fast_filter_value='+encodeURIComponent(inputValue), {
				data: {
					object_type: this.objects_type
				},
			}).then(lang.hitch(this, 
					function(response){
						
					})
			);
		},
		updateSearchOnCurrentPage: function(node, datatype) {
			this.fetchSearchOnCurrentPage(node, datatype);
			this.addSessionSearch(node);
		},
		launchSearchOnCurrentPage: function(node, datatype){
			// Si on a une requete en cours, on l'arrete
			if (this.request_timeout != null) {
				clearTimeout(this.request_timeout);
				this.request_timeout = null;
			}
			// On attend 500ms avant de lancer la requete
			this.request_timeout = setTimeout(
				lang.hitch(this, this.updateSearchOnCurrentPage, node, datatype),
				500
			);
		},
		updateSearchOnAllPage: function(node, datatype) {
			var property = domAttr.get(node, 'data-property');
			var inputValue = node.value.toLowerCase();
			var indice = 0;
			if(dom.byId(this.objects_type+'_json_filters_'+indice)) {
				var filters = dom.byId(this.objects_type+'_json_filters_'+indice).value;
			} else if(dom.byId(this.objects_type+'_json_filters')) {
				var filters = dom.byId(this.objects_type+'_json_filters').value;
			} else {
				var filters = '';
			}
			if(dom.byId(this.objects_type+'_pager_'+indice)) {
				var pager = dom.byId(this.objects_type+'_pager_'+indice).value;
			} else if(dom.byId(this.objects_type+'_pager')) {
				var pager = dom.byId(this.objects_type+'_pager').value;
			} else {
				var pager = '';
			}
            if(dom.byId(this.objects_type+'_list_'+indice)) {
				var table = dom.byId(this.objects_type+'_list_'+indice);
			} else if(dom.byId(this.objects_type+'_list_0')) {
				var table = dom.byId(this.objects_type+'_list_0');
			} else {
				var table = dom.byId(this.objects_type+'_list');
			}
            table.innerHTML = '<tr><td><img src="'+pmbDojo.images.getImage('patience.gif')+'"></td></tr>';
			xhr.post(this.ajax_controller_url_base+'&action=list&fast_filter_property='+property+'&fast_filter_value='+encodeURIComponent(inputValue), {
				data: {
					object_type: this.objects_type,
					filters: filters,
					pager: pager,
				},
			}).then(lang.hitch(this, 
					function(response){
						table.innerHTML = response;
						this.parseDom();
						if(dom.byId(this.objects_type+"_list_cell_fast_filter_"+property)) {
							var input_node = dom.byId(this.objects_type+"_list_cell_fast_filter_"+property);
							if(input_node) {
								input_node.focus();
								if(input_node.value.length) {
									input_node.setSelectionRange(input_node.value.length, input_node.value.length);
							    }
							}
						}
					})
			);
		},
		launchSearchOnAllPage: function(node, datatype){
			// Si on a une requete en cours, on l'arrete
			if (this.request_timeout != null) {
				clearTimeout(this.request_timeout);
				this.request_timeout = null;
			}
			// On attend 500ms avant de lancer la requete
			this.request_timeout = setTimeout(
					lang.hitch(this, this.updateSearchOnAllPage, node, datatype),
				500
			);
		}
	});
});