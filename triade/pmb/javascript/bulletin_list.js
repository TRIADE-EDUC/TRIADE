/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bulletin_list.js,v 1.1 2016-10-18 13:58:36 ngantier Exp $ */

		 
function check_all_bulletins(elem, list_id) {
	if(list_id != "") {
		list_id=list_id.split(',');
		while (list_id.length>0) {
			id=list_id.shift();
			if(elem.checked) {
				if (document.getElementById('checkbox_bulletin['+id+']')) document.getElementById('checkbox_bulletin['+id+']').checked=true;
				elem.title=msg_unselect_all;
			} else {
				if (document.getElementById('checkbox_bulletin['+id+']')) document.getElementById('checkbox_bulletin['+id+']').checked=false;
				elem.title=msg_select_all;
			}	
		}
	}
}

function check_if_bulletins_checked(list_id, type) {
	var hasChecked = false;
	if(list_id != "") {
		list_id=list_id.split(',');
		while (list_id.length>0) {
			id=list_id.shift();
			if(document.getElementById('checkbox_bulletin['+id+']').checked) {
				hasChecked = true;
				break;
			}
		}
	}
	if(hasChecked) {
		return true;
	} else {
		alert(msg_have_select_bulletin);
		return false;
	}
}

function get_bulletins_checked(list_id) {
	if(list_id != "") {
		list_id=list_id.split(',');
		bulletin=new Array();
		while (list_id.length>0) {
			id=list_id.shift();
			if(document.getElementById('checkbox_bulletin['+id+']').checked) {
				bulletin.push(id);
			}
		}
		return bulletin.join(",");
	} else {
		return "";
	}
}
