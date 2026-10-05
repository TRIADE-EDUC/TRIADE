/* +-------------------------------------------------+
// | 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_composee_drag_n_drop.js,v 1.3 2021/01/21 09:42:08 jlaurent Exp $ */


/******************************************************************
 *																  *
 *      Drag'n'drop des elements d'une vedette composee        	  *
 *							 									  *
 ******************************************************************/
/*
 * Fonction pour ajouter un nouvel element
 */
function vedette_composee_available_fields_vedette_composee_subdivision(dragged,target){
	var subdivision_id = target.getAttribute("id");
	var n = subdivision_id.indexOf("_composed_");
	/**
	 * Ci dessous: les trois globales a degager
	 */

	var caller_property_name = subdivision_id.substring(0,n)+"_composed";
	var caller_property_name_contribution = subdivision_id.substring(0,n);
	var caller_property_authid = dragged.getAttribute("authid");
	var caller_property_authlabel = dragged.getAttribute("dragtext");
	
	var elements_order = document.getElementById(subdivision_id + "_elements_order");
	var new_order;
	var nb_elements;
	if (elements_order.value) {
		var tab_elements_order = elements_order.value.split(",");
		nb_elements = tab_elements_order.length;
		new_order = get_max(tab_elements_order) + 1;
	} else {
		nb_elements = 0;
		new_order = 0;
	}
	vedette_element_downlight(target);

	if ((dragged.getAttribute("parentorder") == target.getAttribute("parentorder")) && ((target.getAttribute("cardmax") == "") || (target.getAttribute("cardmax") > nb_elements))) {
		var div = document.createElement("div");
		div.setAttribute("id", subdivision_id + "_element_" + new_order);
		div.setAttribute("class", "vedette_composee_element contribution_vedette_flex_element");
		div.setAttribute("dragtype", "vedette_composee_element");
		div.setAttribute("recepttype", "vedette_composee_element");
		div.setAttribute("draggable", "yes");
		div.setAttribute("recept", "yes");
		div.setAttribute("order", new_order);
		div.setAttribute("highlight", "vedette_element_highlight");
		div.setAttribute("downlight", "vedette_element_downlight");
		div.setAttribute("handler", subdivision_id + "_element_" + new_order + "_handler");
		div.setAttribute("vedettetype", dragged.getAttribute("vedettetype"));
		div.setAttribute("available_field_num", dragged.getAttribute("available_field_num"));
		div.setAttribute("data-pmb-params", dragged.getAttribute("data-pmb-params"));
		
		var handler = document.createElement("span");
		handler.setAttribute("id", subdivision_id + "_element_" + new_order + "_handler");
		handler.setAttribute("style", "float:left;padding-right:7px;");
		
		var img = document.createElement("img");
		img.setAttribute("src", "./images/drag_symbol.png");
		img.setAttribute("style", "vertical-align:middle;");
		
		handler.appendChild(img);
		div.appendChild(handler);
				
		vedette_element.create_box(caller_property_name,dragged.getAttribute("vedettetype"), div, target.getAttribute("subdivisiontype"), new_order, 0, "", "", target.getAttribute("parentorder"), dragged.getAttribute("data-pmb-params"), caller_property_name_contribution);

		target.insertBefore(div, target.lastElementChild);
		vedette_composee_update_order(target);
		
		init_drag();
		ajax_pack_element(document.getElementById(subdivision_id + "_element_" + new_order + "_label"));
	} else {
		alert("Le nombre maximal d'elements pour cette subdivision est atteint !");
	}
}

/*
 * Fonction pour trier les elements
 */
function vedette_composee_element_vedette_composee_element(dragged,target){
	var dragged_parent = dragged.parentNode;
	var target_parent = target.parentNode;
	vedette_element_downlight(target);
	
	// On commence par verifier qu'on reste bien dans la meme subdivision
	if (dragged_parent == target_parent) {
		dragged_parent.insertBefore(dragged, target);
		recalc_recept();
		vedette_composee_update_order(dragged_parent);
	} else {
		vedette_composee_element_vedette_composee_subdivision(dragged,target_parent);
	}
}

/*
 * Fonction pour changer de subdivision
 */
function vedette_composee_element_vedette_composee_subdivision(dragged,target){
	var dragged_parent = dragged.parentNode;
	var subdivision_id = target.getAttribute("id");
	var elements_order = document.getElementById(subdivision_id + "_elements_order");
	var new_order;
	var nb_elements;
	var element_label_id = dragged.getAttribute("id")+'_label';
	var element_label_node = document.getElementById(element_label_id);
	var authid = element_label_node .getAttribute("authid");
	vedette_element_downlight(target);
	
	if (elements_order.value) {
		var tab_elements_order = elements_order.value.split(",");
		nb_elements = tab_elements_order.length;
		new_order = get_max(tab_elements_order) + 1;
	} else {
		nb_elements = 0;
		new_order = 0;
	}
	
	if ((dragged_parent.getAttribute("parentorder") == target.getAttribute("parentorder")) && ((target.getAttribute("cardmax") == "") || (target.getAttribute("cardmax") > nb_elements))) {
	// On verifie qu'on change de subdivision, sinon on ne fait rien
		if (dragged_parent != target) {
			vedette_element.update_box(dragged.getAttribute("vedettetype"), dragged, target.getAttribute("subdivisiontype"), new_order, target.getAttribute("parentorder"), authid);
			
			dragged.setAttribute("id", subdivision_id + "_element_" + new_order);
			dragged.setAttribute("order", new_order);
			
			var handler = document.getElementById(dragged.getAttribute("handler"));
			dragged.setAttribute("handler", subdivision_id + "_element_" + new_order + "_handler");
			handler.setAttribute("id", subdivision_id + "_element_" + new_order + "_handler");
			
			target.insertBefore(dragged, target.lastElementChild);
			recalc_recept();
			vedette_composee_update_order(dragged_parent);
			vedette_composee_update_order(target);
			ajax_pack_element(document.getElementById(subdivision_id + "_element_" + new_order + "_label"));
		}
	} else {
		alert("Le nombre maximal d'elements pour cette subdivision est atteint !");
	}
}

/*
 * Fonction supprimer un element
 */
function vedette_composee_element_vedette_composee_delete_element(dragged,target){
	var parent = dragged.parentNode;

	parent.removeChild(dragged);
	recalc_recept();
	vedette_composee_update_order(parent);
	
	vedette_element_downlight(target);
}

/**
 * Mise a jour de l'ordre
 * 
 * @param parent Subdivision parente
 */
function vedette_composee_update_order(parent){
	var parent_id = parent.getAttribute("id");
	var subdivisionorder = parent.getAttribute("order");
	// On met a jour le tableau des libelles pour l'apereu
	var id_tab_vedette_elements = parent_id.replace("_" + parent.getAttribute("subdivisiontype"), "") + "_tab_vedette_elements";
	window[id_tab_vedette_elements][subdivisionorder] = new Object();
	var index = 0;
	var elements_order = new Array();
	for(var i=0;i<parent.childNodes.length;i++){
		if(parent.childNodes[i].nodeType == 1){
			if(parent.childNodes[i].getAttribute("recepttype")=="vedette_composee_element"){
				elements_order[index] = parent.childNodes[i].getAttribute("order");

				var label = document.getElementById(parent.childNodes[i].getAttribute("id") + "_label").getAttribute("rawlabel");
				window[id_tab_vedette_elements][subdivisionorder][index] = label;
				
				index++;
			}
		}
	}
	var id_vedette_apercu = parent_id.replace("_" + parent.getAttribute("subdivisiontype"), "") + "_vedette_composee_apercu";
	var id_vedette_separator = parent_id.replace("_" + parent.getAttribute("subdivisiontype"), "") + "_separator";
	vedette_composee_update_apercu(id_vedette_apercu, window[id_tab_vedette_elements], window[id_vedette_separator]);
	
	if(document.getElementById(parent_id + "_elements_order")){
		document.getElementById(parent_id + "_elements_order").value=elements_order.join(",");
	}
}

/*
 * Mise a jour de l'apercu
 */
function vedette_composee_update_apercu(id_apercu, tab_vedette_elements, vedette_separator) {
	apercu = "";
	for (var i in tab_vedette_elements) {
		for (var j in tab_vedette_elements[i]) {
			if (tab_vedette_elements[i][j]) {
				if (apercu) apercu = apercu + vedette_separator;
				apercu = apercu + tab_vedette_elements[i][j];
			}
		}
	}
	document.getElementById(id_apercu).value = apercu;
	if(document.getElementById(id_apercu+'_autre')) {
		document.getElementById(id_apercu+'_autre').value = apercu;
	}	
}

/**
 * Mise a jour de l'ensemble des elements de la vedette composee
 */
function vedette_composee_update_all(id_vedette_composee_subdivisions) {
	var vedette_composee_subdivisions = document.getElementById(id_vedette_composee_subdivisions);
	for(var i=0;i<vedette_composee_subdivisions.childNodes.length;i++){
		if(vedette_composee_subdivisions.childNodes[i].nodeType == 1){
			if(vedette_composee_subdivisions.childNodes[i].getAttribute("recepttype")=="vedette_composee_subdivision"){
				vedette_composee_update_order(vedette_composee_subdivisions.childNodes[i]);
			}
		}
	}
}

function vedette_composee_delete_subdivisiontype(parent){
	var parent_id = parent.getAttribute("id");
	var subdivisionorder = parent.getAttribute("order");
	// On met a jour le tableau des libelles pour l'apercu
	var id_tab_vedette_elements = parent_id.replace("_" + parent.getAttribute("subdivisiontype"), "") + "_tab_vedette_elements";
	window[id_tab_vedette_elements][subdivisionorder] = new Object();
	
	var index = 0;
	var elements_order = new Array();
	for(var i=0;i<parent.childNodes.length;i++){
		if(parent.childNodes[i].nodeType == 1){
			if(parent.childNodes[i].getAttribute("recepttype")=="vedette_composee_element"){
				var child=(parent.childNodes[i]);
				parent.removeChild(child);
			}
		}
	}
	var id_vedette_apercu = parent_id.replace("_" + parent.getAttribute("subdivisiontype"), "") + "_vedette_composee_apercu";
	var id_vedette_separator = parent_id.replace("_" + parent.getAttribute("subdivisiontype"), "") + "_separator";
	vedette_composee_update_apercu(id_vedette_apercu, window[id_tab_vedette_elements], window[id_vedette_separator]);
	
	if(document.getElementById(parent_id + "_elements_order")){
		document.getElementById(parent_id + "_elements_order").value=elements_order.join(",");
	}
}

function vedette_composee_delete_all(id_vedette_composee_subdivisions) {
	var vedette_composee_subdivisions = document.getElementById(id_vedette_composee_subdivisions);
	for(var i=0;i<vedette_composee_subdivisions.childNodes.length;i++){
		if(vedette_composee_subdivisions.childNodes[i].nodeType == 1){
			if(vedette_composee_subdivisions.childNodes[i].getAttribute("recepttype")=="vedette_composee_subdivision"){
				vedette_composee_delete_subdivisiontype(vedette_composee_subdivisions.childNodes[i]);
			}
		}
	}
	 vedette_composee_update_all(id_vedette_composee_subdivisions);
}

function vedette_element_highlight(obj) {
	obj.style.background="#DDD";	
}

function vedette_element_downlight(obj) {
	obj.style.background="";
}

function get_max(array) {
	var max = 0;
	var value = 0;
	for (var i in array) {
		value = parseInt(array[i]);
		if (value > max) {
			max  = value;
		}
	}
	return max;
}
