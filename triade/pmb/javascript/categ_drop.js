/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_drop.js,v 1.2 2016/09/29 13:44:41 dgoron Exp $ */


/**********************************
 *								  *				
 *      Tri des categ              *
 *                                * 
 **********************************/

function categ_categ(dragged,target){
	element_drop(dragged,target,'categ');
}

function categ_get_tab_order_value(element, i) {
	return element.childNodes[i].getAttribute("id").substr(5);
}

function categ_update_order_callback(source,tab_categ) {
	if(document.getElementById("tab_categ_order")){
		document.getElementById("tab_categ_order").value=tab_categ.join(",");
	}
}

function categ_highlight(obj) {
	obj.style.background="#DDD";	
}

function categ_downlight(obj) {
	obj.style.background="";
}
