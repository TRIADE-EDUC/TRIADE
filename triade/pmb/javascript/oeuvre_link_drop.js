/* +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: oeuvre_link_drop.js,v 1.2 2020/04/01 15:54:29 tsamson Exp $ */


/**********************************
 *								  *				
 *      Tri des oeuvres liées     *
 *                                * 
 **********************************/
//expressions
function oeuvre_expression_oeuvre_expression(dragged,target){
	element_drop(dragged,target,'oeuvre_expression');
}

function oeuvre_expression_get_tab_order_value(element, i) {
	return element.childNodes[i].getAttribute("id").substr(19);
}

function oeuvre_expression_update_order_callback(source,tab_link) {
	if(document.getElementById("tab_oeuvre_expression_order")){
		document.getElementById("tab_oeuvre_expression_order").value=tab_link.join(",");
	}
}

function oeuvre_expression_highlight(obj) {
	obj.style.background="#DDD";	
}

function oeuvre_expression_downlight(obj) {
	obj.style.background="";
}

//expressions de
function oeuvre_expression_from_oeuvre_expression_from(dragged,target){
	element_drop(dragged,target,'oeuvre_expression_from');
}

function oeuvre_expression_from_get_tab_order_value(element, i) {
	return element.childNodes[i].getAttribute("id").substr(24);
}

function oeuvre_expression_from_update_order_callback(source,tab_link) {
	if(document.getElementById("tab_oeuvre_expression_from_order")){
		document.getElementById("tab_oeuvre_expression_from_order").value=tab_link.join(",");
	}
}

function oeuvre_expression_from_highlight(obj) {
	obj.style.background="#DDD";	
}

function oeuvre_expression_from_downlight(obj) {
	obj.style.background="";
}


//autres liens
function other_link_other_link(dragged,target){
	element_drop(dragged,target,'other_link');
}

function other_link_get_tab_order_value(element, i) {
	return element.childNodes[i].getAttribute("id").substr(12);
}

function other_link_update_order_callback(source,tab_link) {
	if(document.getElementById("tab_other_link_order")){
		document.getElementById("tab_other_link_order").value=tab_link.join(",");
	}
}

function other_link_highlight(obj) {
	obj.style.background="#DDD";	
}

function other_link_downlight(obj) {
	obj.style.background="";
}
