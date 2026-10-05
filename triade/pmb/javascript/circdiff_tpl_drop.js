/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: circdiff_tpl_drop.js,v 1.2 2016/09/29 13:44:41 dgoron Exp $ */

function circdiff_highlight(obj) {
	obj.style.background="#DDD";
	
}
function circdiff_downlight(obj) {
	obj.style.background="";
}

function circdiffprint_circdiffprint(dragged,target){
	element_drop(dragged,target,'circdiffprint');
}

function circdiffprint_get_tab_order_value(element, i) {
	if(element.childNodes[i].getAttribute("id_circdiff")){
		return element.childNodes[i].getAttribute("id_circdiff");
	}
	return "";
}

function circdiffprint_update_order_callback(source,tab_circdiff) {
	document.getElementById('order_tpl').value = tab_circdiff.join(",");
	var id_serialcirc = source.getAttribute("id_serialcirc");	
	var url= "./ajax.php?module=edit&categ=serialcirc_diff&sub=up_order_circdiffprint&id_serialcirc="+id_serialcirc;	
	var action = new http_request();
	action.request(url,true,"&tablo="+tab_circdiff.join(","));
}