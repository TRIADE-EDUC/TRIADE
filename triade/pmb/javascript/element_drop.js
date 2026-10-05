// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: element_drop.js,v 1.2 2020/03/05 14:53:32 btafforeau Exp $


/********************************** 
 *								  *	
 *      Drop d'un élément         * 
 *                                * 
 **********************************/

function element_drop(dragged, target, type) {
	let element = target.parentNode;
	let nodeTarget = target;
	
	if (dragged.getAttribute('order') < target.getAttribute('order')) {
		nodeTarget = target.nextSibling;
	}
	element.insertBefore(dragged, nodeTarget);
	
	element_downlight(target);
	
	recalc_recept();
	element_update_order(dragged, target, type);
}

/*
 * Mise à jour de l'ordre
 */
function element_update_order(source, cible, type) {
	let src_order =  source.getAttribute('order');
	let target_order = cible.getAttribute('order');
	let element = source.parentNode;
	let index = 0;
	let tab_elements_order = new Array();
	
	for (let i = 0; i < element.childNodes.length; i++) {
		if (element.childNodes[i].nodeType == 1) {
			if (element.childNodes[i].getAttribute('recepttype') == type) {
				element.childNodes[i].setAttribute('order', index);
				if (typeof window[type+'_get_tab_order_value'] !== 'undefined') {
					tab_elements_order[index] = window[type+'_get_tab_order_value'](element, i);
			    }
				index++;
			}
		}
	}
    if (typeof window[type+'_update_order_callback'] !== 'undefined') {
    	window[type+'_update_order_callback'](source, tab_elements_order);
    }
}

function element_highlight(obj) {
	obj.style.background = '#DDD';	
}

function element_downlight(obj) {
	obj.style.background = '';
}