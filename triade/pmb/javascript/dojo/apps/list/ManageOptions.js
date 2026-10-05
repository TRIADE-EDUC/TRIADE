// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ManageOptions.js,v 1.9 2022/06/15 10:02:01 dgoron Exp $

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
		selectorAvailableColumns:null,
		selectorSelectedColumns:null,
		objects_type:null,
		constructor: function(objects_type) {
			this.objects_type = objects_type;
			this.selectorAvailableColumns = dom.byId(this.objects_type+'_available_columns');
			this.selectorSelectedColumns = dom.byId(this.objects_type+'_selected_columns');
			on(dom.byId(this.objects_type+'_options_img'), 'click', lang.hitch(this, this.contentShow));
			on(dom.byId(this.objects_type+'_options_move_available_to_selected'), 'click', lang.hitch(this, this.moveAvailableToSelected));
			on(dom.byId(this.objects_type+'_options_move_selected_to_available'), 'click', lang.hitch(this, this.moveSelectedToAvailable));
			on(dom.byId(this.objects_type+'_options_move_option_first'), 'click', lang.hitch(this, this.moveOptionFirst));
			on(dom.byId(this.objects_type+'_options_move_option_top'), 'click', lang.hitch(this, this.moveOptionTop));
			on(dom.byId(this.objects_type+'_options_move_option_bottom'), 'click', lang.hitch(this, this.moveOptionBottom));
			on(dom.byId(this.objects_type+'_options_move_option_last'), 'click', lang.hitch(this, this.moveOptionLast));
			on(dom.byId(this.objects_type+'_options_applied_group_more'), 'click', lang.hitch(this, this.appliedGroupMore));
			var nodes = document.querySelectorAll("."+this.objects_type+"_options_applied_group_delete");
			if(nodes.length) {
				for(var i=1; i<=nodes.length; i++) {
					on(dom.byId(this.objects_type+'_options_applied_group_delete_'+i), 'click', lang.hitch(this, this.appliedGroupDelete, i));
				}
			}
			if(dom.byId(this.objects_type+'_search_form')) {
				on(dom.byId(this.objects_type+'_search_form'), 'submit', lang.hitch(this, this.selectAll));
			}
			//Edition d'une liste perso
			if(dom.byId('list_dataset_form')) {
				on(dom.byId('list_dataset_form'), 'submit', lang.hitch(this, this.selectAll));
			}
		},
		contentShow: function() {
			var domNode = dom.byId(this.objects_type+'_options_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_options_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_options_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		},
		moveOptions: function(src, dest, where) {
			var total = src.options.length;
			var number = src.selectedOptions.length;
			if(number) {
				var selectedOptions = src.selectedOptions;
			    var selected = [];
			    for (var i = 0; i < selectedOptions.length; i++) {
		            selected.push(selectedOptions[i]);
			    }
				for(var selItem=0; selItem<=number; selItem++) {
					if(selected[selItem]) {
						var newOption = selected[selItem].cloneNode(true);
						newOption.selected="true";
						var index = selected[selItem].index; 
						src.removeChild(selected[selItem]);
						switch(where) {
							case 'first':
								dest.insertBefore(newOption, dest.options[selItem]);
								break;
							case 'top':
								var index_up = index-1;
								if(index_up < 0) {
									index_up = 0;
								}
								dest.insertBefore(newOption, dest.options[index_up]);
								break;
							case 'bottom':
								var index_down = index+1;
								if(index_down > (total-1)) {
									dest.appendChild(newOption);
								} else {
									dest.insertBefore(newOption, dest.options[index_down]);
								}
								break;
							case 'sort':
								dest.appendChild(newOption);
								this.sortSelectOptions(dest);
								break;
							default:
								dest.appendChild(newOption);
								break;
						}
					}
				}
			}
		},
		moveAvailableToSelected: function() {
			this.moveOptions(this.selectorAvailableColumns, this.selectorSelectedColumns);
		},
		moveSelectedToAvailable: function() {
			this.moveOptions(this.selectorSelectedColumns, this.selectorAvailableColumns, 'sort');
		},
		moveOptionFirst: function() {
			this.moveOptions(this.selectorSelectedColumns, this.selectorSelectedColumns, 'first');
		},
		moveOptionTop: function() {
			this.moveOptions(this.selectorSelectedColumns, this.selectorSelectedColumns, 'top');
		},
		moveOptionBottom: function() {
			this.moveOptions(this.selectorSelectedColumns, this.selectorSelectedColumns, 'bottom');
		},
		moveOptionLast: function() {
			this.moveOptions(this.selectorSelectedColumns, this.selectorSelectedColumns);
		},
		selectAll: function() {
			var selectedColumns = document.getElementById(this.objects_type+'_selected_columns');
	        for (var i = 0; i < selectedColumns.options.length; i++) { 
	        	selectedColumns.options[i].selected = true; 
	        }
		},
		sortSelectOptions: function(selector) {
			var options = new Array();
		    for (var i=0;i<selector.options.length;i++) {
		    	options[i] = new Array();
		    	options[i][0] = selector.options[i].text;
		    	options[i][1] = selector.options[i].value;
		    }
		    options.sort();
		    while (selector.options.length > 0) {
		    	selector.options[0] = null;
		    }
		    for (var i=0;i<options.length;i++) {
		        var op = new Option(options[i][0], options[i][1]);
		        selector.options[i] = op;
		    }
		    return;
		},
		appliedGroupMore: function() {
			var domNode = dom.byId(this.objects_type+'_options_applied_group_more_content');
			var number = domAttr.get(domNode, 'data-applied-group-number');
			// Limitons à 3 critères pour le moment
			if(number >= 3) {
				alert(pmbDojo.messages.getMessage('list', 'list_ui_options_group_by_max_reached'));
				return;
			}
			xhr('./ajax.php?module=ajax&categ=list&sub=options&action=get_applied_group_selector&objects_type='+this.objects_type+'&id='+number, {
				sync: false,
			}).then(lang.hitch(this, 
					function(response){
						var domNode = dom.byId(this.objects_type+'_options_applied_group_more_content');
						var number = domAttr.get(domNode, 'data-applied-group-number');
						domNode.innerHTML += response;
						number++;
						domAttr.set(domNode, 'data-applied-group-number', number); 
					})
			);
		},
		appliedGroupDelete: function(ind) {
			var domNode = dom.byId(this.objects_type+'_options_applied_group_'+ind);
			domNode.innerHTML = '';
		}
	});
});