/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis_drop.js,v 1.3 2016/09/29 13:44:41 dgoron Exp $ */


/**********************************
 *								  *				
 *      Tri des avis              *
 *                                * 
 **********************************/

function avisdrop_avisdrop(dragged,target){
	element_drop(dragged,target,'avisdrop');
}

function avisdrop_get_tab_order_value(element, i) {
	var id_avis = 0;
	if(element.childNodes[i].getAttribute("id_avis")){
		id_avis = element.childNodes[i].getAttribute("id_avis");
	}
	return id_avis;
}

function avisdrop_update_order_callback(source,tab_avis) {
	var url= "./ajax.php?module=ajax&categ=tri&quoifaire=up_order_avis";
	var action = new http_request();
	action.request(url,true,"&tablo_avis="+tab_avis.join(","));
}

function avis_highlight(obj) {
	obj.style.background="#DDD";
	
}
function avis_downlight(obj) {
	obj.style.background="";
}
