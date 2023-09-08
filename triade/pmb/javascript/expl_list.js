/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_list.js,v 1.2 2015-08-05 15:43:32 dgoron Exp $ */

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
