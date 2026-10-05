/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept_drop.js,v 1.2 2016/09/29 13:44:41 dgoron Exp $ */


/**********************************
 *								  *				
 *      Tri des concepts          *
 *                                * 
 **********************************/

function concept_concept(dragged,target){
	element_drop(dragged,target,'concept');
}

function concept_get_tab_order_value(element, i) {
	return element.childNodes[i].getAttribute("id").substr(8);
}

function concept_update_order_callback(source,tab_concept) {
	if(document.getElementById("tab_concept_order")){
		document.getElementById("tab_concept_order").value=tab_concept.join(",");
	}
}

function concept_highlight(obj) {
	obj.style.background="#DDD";	
}

function concept_downlight(obj) {
	obj.style.background="";
}
