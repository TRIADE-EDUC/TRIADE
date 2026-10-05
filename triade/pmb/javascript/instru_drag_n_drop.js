// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: instru_drag_n_drop.js,v 1.3 2022/06/02 10:14:27 qvarin Exp $

function instru_highlight(obj){
	obj.style.background = "#FFF";
}

function instru_downlight(obj){
	obj.style.background = "";
}

function instru_instru(dragged,target){
	//Do Switch
	instru_downlight(target);
	
	if (dragged.id == target.id) {
		// On n'a rien fait
		return false;
	}
	
	if(dragged.getAttribute('musicstand') == target.getAttribute('musicstand')){
		var parent = target.parentNode;
		if (dragged.getAttribute('order') > target.getAttribute('order')) {
			// Insert Before
			parent.insertBefore(dragged, target);
		} else {
			// Insert After
			parent.insertBefore(dragged, target.nextSibling);
		}
		instru_update_order(parent);
	}
}
function instru_update_order(parentNode) {
	var instru = parentNode.querySelectorAll('[dragtype="instru"]');
	for (var i=0; i < instru.length; i++) {
		var instance_widget = dijit.registry.byId(instru[i].getAttribute('id'));
		instance_widget.set_order(i+1);
		if (i == (instru.length-1)) {
			instance_widget.publish_event("drag_and_drop_end");	
		}
	}
	recalc_recept();
}