/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_dnd.js,v 1.3 2013/03/11 10:40:20 mbertin Exp $ */

function editionsstatefields_editionsstatefields(dragged,target){
	//Ajout d'un champs
	//à la dépose d'un élément, on ajoute un champs caché et on recharge...
	var input = document.createElement("input");
	input.setAttribute("type","hidden");
	input.setAttribute("name","editions_state_fields_content_fields[]");
	input.setAttribute("value",dragged.id);
	target.appendChild(input);
	//y compris dans le tri
	var input2 = document.createElement("input");
	input2.setAttribute("type","hidden");
	input2.setAttribute("name","editions_state_orders_fields[]");
	input2.setAttribute("value",dragged.id);
	document.getElementById("order_fields").appendChild(input2);
	dragged.parentNode.removeChild(dragged);

	editionsstate_downlight(target);
	editionsstate_form_refresh();

}

function editionsstatefieldslist_editionsstatefieldslist(dragged,target){
	//Suppresion d'un champs
	//à la dépose d'un élément, on ajoute un champs caché et on recharge...
	var input = document.createElement("input");
	input.setAttribute("type","hidden");
	input.setAttribute("name","editions_state_fields_fields[]");
	input.setAttribute("value",dragged.id.replace("fields_",""));
	target.appendChild(input);
	//y compris dans le tri
	var inputs = document.getElementsByName('editions_state_orders_fields[]');
	//console.log(inputs);
	for(var i=0 ; i<inputs.length ; i++){
		if(inputs[i].value == dragged.id.replace("fields_","")){
			inputs[i].parentNode.removeChild(inputs[i]);
		}
	}
	var inputs = document.getElementsByName('editions_state_orders_content_fields[]');
	//console.log(inputs);
	for(var i=0 ; i<inputs.length ; i++){
		if(inputs[i].value == dragged.id.replace("fields_","")){
			inputs[i].parentNode.removeChild(inputs[i]);
		}
	}
	dragged.parentNode.removeChild(dragged);
	editionsstate_downlight(target);
	editionsstate_form_refresh();
}

function editionsstatefilterslist_editionsstatefilterslist(dragged,target){
	//Suppresion d'un filtre
	//à la dépose d'un élément, on ajoute un champs caché et on recharge...
	var input = document.createElement("input");
	input.setAttribute("type","hidden");
	input.setAttribute("name","editions_state_filters_fields[]");
	input.setAttribute("value",dragged.id.replace("filters_","").replace("_drag",""));
	target.appendChild(input);
	var drag = document.getElementById(dragged.id.replace("_drag",""));
	drag.parentNode.removeChild(drag);
	editionsstate_downlight(target);
	editionsstate_form_refresh();
}

function editionsstateorderslist_editionsstateorderslist(dragged,target){
	//Suppresion d'un tri
	//à la dépose d'un élément, on ajoute un champs caché et on recharge...
	var input = document.createElement("input");
	input.setAttribute("type","hidden");
	input.setAttribute("name","editions_state_orders_fields[]");
	input.setAttribute("value",dragged.id.replace("orders_","").replace("_drag",""));
	target.appendChild(input);
	var drag = document.getElementById(dragged.id.replace("_drag",""));
	drag.parentNode.removeChild(drag);
	editionsstate_downlight(target);
	editionsstate_form_refresh();
}

function editionsstateorders_editionsstateorders(dragged,target){
	//Ajout d'un tri
	var input = document.createElement("input");
	input.setAttribute("type","hidden");
	input.setAttribute("name","editions_state_orders_content_fields[]");
	input.setAttribute("value",dragged.id.replace("crit_",""));
	target.appendChild(input);	
	dragged.parentNode.removeChild(dragged);
	editionsstate_form_refresh();
}

function editionsstatefilters_editionsstatefilters(dragged,target){
	//Ajout d'un filtre
	var input = document.createElement("input");
	input.setAttribute("type","hidden");
	input.setAttribute("name","editions_state_filters_content_fields[]");
	input.setAttribute("value",dragged.id.replace("filter_",""));
	target.appendChild(input);	
	dragged.parentNode.removeChild(dragged);
	editionsstate_form_refresh();
}

function editionsstate_form_refresh(){
	recalc_recept();
	document.forms['editions_state_form'].action = document.forms['editions_state_form'].action.replace("=save","=edit");
	document.forms['editions_state_form'].partial_submit.value = 1;
	document.forms['editions_state_form'].submit();
}

function editionsstate_highlight(obj) {
	obj.style.border="2px solid black";	
}

function editionsstate_downlight(obj) {
	obj.style.border="";
}