function instru_highlight(obj){
	obj.style.background = "#FFF";
}

function instru_downlight(obj){
	obj.style.background = "";
}

function instru_instru(dragged,target){
	//Do Switch
	instru_downlight(target);
	if(dragged.getAttribute('musicstand') == target.getAttribute('musicstand')){
		var parent = target.parentNode;
		parent.insertBefore(dragged, target);
		instru_update_order(parent);
	}
}
function instru_update_order(node){
	var instru = node.querySelectorAll('[dragtype="instru"]');
	for(var i=0 ; i<instru.length ; i++){
		var instance_widget = dijit.registry.byId(instru[i].getAttribute('id'));
		instance_widget.set_order(i+1);
	}
	recalc_recept();
}