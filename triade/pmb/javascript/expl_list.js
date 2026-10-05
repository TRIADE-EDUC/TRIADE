/* +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_list.js,v 1.3 2020/04/30 07:48:45 dgoron Exp $ */

function check_all_expl(elem, expl_list_id) {
	if(expl_list_id != "") {
		list_id=expl_list_id.split(',');
		while (list_id.length>0) {
			id=list_id.shift();
			if(elem.checked) {
				if (document.getElementById('checkbox_expl['+id+']')) document.getElementById('checkbox_expl['+id+']').checked=true;
				elem.title=msg_unselect_all;
			} else {
				if (document.getElementById('checkbox_expl['+id+']')) document.getElementById('checkbox_expl['+id+']').checked=false;
				elem.title=msg_select_all;
			}	
		}
	}
}

function check_if_checked(expl_list_id,type) {
	var hasChecked = false;
	if(expl_list_id != "") {
		list_id=expl_list_id.split(',');
		while (list_id.length>0) {
			id=list_id.shift();
			if(document.getElementById('checkbox_expl['+id+']').checked) {
				hasChecked = true;
				break;
			}
		}
	}
	if(hasChecked) {
		return true;
	} else {
		if (type == 'transfer') {
			alert(msg_have_select_transfer_expl);
		} else {
			alert(msg_have_select_expl);
		}
		return false;
	}
}

function get_expl_checked(expl_list_id) {
	if(expl_list_id != "") {
		list_id=expl_list_id.split(',');
		expl=new Array();
		while (list_id.length>0) {
			id=list_id.shift();
			if(document.getElementById('checkbox_expl['+id+']').checked) {
				expl.push(id);
			}
		}
		return expl.join(",");
	} else {
		return "";
	}
}

function is_visible_section_from_location(selector_node, num_location) {
	var is_visible = false;
	if(selector_node) {
		var options = selector_node.options;
		for(var i=0; i<options.length; i++) {
			if(options[i].value == selector_node.value) {
				var num_locations = new Array();
				if(options[i].getAttribute('data-num-locations')) {
					num_locations = options[i].getAttribute('data-num-locations').split(',');
				}
				if(num_locations.indexOf(num_location) != -1) {
					is_visible = true;
				}
			}
		}
	}
	if(is_visible) {
		return true;
	} else {
		return false;
	}
}
function refresh_sections_from_location(selector_node, num_location, has_selected_item) {
	if(selector_node) {
		var options = selector_node.options;
		for(var i=0; i<options.length; i++) {
			var disabled = false;
			var num_locations = new Array();
			if(options[i].getAttribute('data-num-locations')) {
				num_locations = options[i].getAttribute('data-num-locations').split(',');
				if(num_locations.indexOf(num_location) == -1) {
					disabled = true;
				}
			} 
			if(disabled) {
				options[i].setAttribute('disabled', 'disabled');
				options[i].removeAttribute('selected');
			} else {
				options[i].removeAttribute('disabled');
				if(!has_selected_item) {
					selector_node.value = options[i].value;
					has_selected_item = true;
				}
			}
		}
	}
}
