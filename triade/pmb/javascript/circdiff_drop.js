/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: circdiff_drop.js,v 1.4 2016/09/29 13:44:41 dgoron Exp $ */

/*
 * Fonction pour trier la liste des destinataires en circulation de périodique
 */
function circdiffdrop_circdiffdrop(dragged,target){
	element_drop(dragged,target,'circdiffdrop');
}

function circdiffdrop_get_tab_order_value(element, i) {
	var id_circdiff = 0;
	if(element.childNodes[i].getAttribute("id_circdiff")){
		id_circdiff = element.childNodes[i].getAttribute("id_circdiff");
	}
	return id_circdiff;
}

function circdiffdrop_update_order_callback(source,tab_circdiff) {
	var url= "./ajax.php?module=catalog&categ=serialcirc_diff&sub=up_order_circdiff";	
	var action = new http_request();
	action.request(url,true,"&tablo="+tab_circdiff.join(","));
	if (document.getElementById('sort_field')) {
		document.getElementById('sort_field').options[0].setAttribute('selected','selected');
	}
}

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
	var id_circdiff = 0;
	if(element.childNodes[i].getAttribute("id_circdiff")){
		id_circdiff = element.childNodes[i].getAttribute("id_circdiff");
	}
	return id_circdiff;
}

function circdiffprint_update_order_callback(source,tab_circdiff) {
	var id_serialcirc = source.getAttribute("id_serialcirc");
	var url= "./ajax.php?module=catalog&categ=serialcirc_diff&sub=up_order_circdiffprint&id_serialcirc="+id_serialcirc;	
	var action = new http_request();
	action.request(url,true,"&tablo="+tab_circdiff.join(","));
}

function circdiffgroupdrop_circdiffgroupdrop(dragged,target){
	element_drop(dragged,target,'circdiffgroupdrop');
}

function circdiffgroupdrop_get_tab_order_value(element, i) {
	var id_circdiff = 0;
	if(element.childNodes[i].getAttribute("id_circdiff")){
		id_circdiff = element.childNodes[i].getAttribute("id_circdiff");
	}
	return id_circdiff;
}

function circdiffgroupdrop_update_order_callback(source,tab_circdiff) {
	var url= "./ajax.php?module=catalog&categ=serialcirc_diff&sub=up_order_circdiffgroupdrop";	
	var action = new http_request();
	action.request(url,true,"&tablo="+tab_circdiff.join(","));
}
