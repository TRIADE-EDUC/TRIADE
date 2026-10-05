/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_perso_drop.js,v 1.1 2016/10/06 12:41:18 dgoron Exp $ */


/********************************************
 *								  			*				
 *      Tri des recherches prédéfinies      *
 *                                			* 
 ********************************************/

function search_perso_search_perso(dragged,target){
	element_drop(dragged,target,'search_perso');
}

function search_perso_get_tab_order_value(element, i) {
	return element.childNodes[i].getAttribute("id").substr(13);
}

function search_perso_update_order_callback(source,tab_search_perso) {
	var url= "./ajax.php?module=ajax&categ=tri&quoifaire=up_order_search_perso";
	var action = new http_request();
	action.request(url,true,"&tab_search_perso="+tab_search_perso.join(","));
}

function search_perso_highlight(obj) {
	obj.style.background="#DDD";	
}

function search_perso_downlight(obj) {
	obj.style.background="";
}
