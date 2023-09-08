/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: element_drop.js,v 1.1 2016-09-29 13:44:41 dgoron Exp $ */


/**********************************
 *								  *				
 *      Drop d'un élément         *
 *                                * 
 **********************************/

function element_drop(dragged,target,type){

	var element=target.parentNode;
	element.insertBefore(dragged,target);
	
	element_downlight(target);
	
	recalc_recept();
	element_update_order(dragged,target,type);
}

/*
 * Mis à jour de l'ordre
 */
function element_update_order(source,cible,type){
	var src_order =  source.getAttribute("order");
	var target_order = cible.getAttribute("order");
	var element = source.parentNode;
	
	var index = 0;
	var tab_elements_order = new Array();
	for(var i=0;i<element.childNodes.length;i++){
		if(element.childNodes[i].nodeType == 1){
			if(element.childNodes[i].getAttribute("recepttype")==type){
				element.childNodes[i].setAttribute("order",index);
				if(typeof window[type+'_get_tab_order_value'] !== 'undefined') {
					tab_elements_order[index] = window[type+'_get_tab_order_value'](element,i);
			    }
				index++;
			}
		}
	}
    if(typeof window[type+'_update_order_callback'] !== 'undefined') {
    	window[type+'_update_order_callback'](source,tab_elements_order);
    }
}

function element_highlight(obj) {
	obj.style.background="#DDD";	
}

function element_downlight(obj) {
	obj.style.background="";
}