// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dyn_form.js,v 1.6 2016-04-22 09:43:53 jpermanne Exp $

function make_form(id){
	
	var action = new http_request();
	var url = "./ajax.php?module=catalog&categ=avis&id="+id+"&quoifaire=show_form";
	
	action.request(url);
	
	if(action.get_status() == 0){
		var div_affichage = document.getElementById("avis_"+id);
		div_affichage.style.display = "none";
		
		document.getElementById("update_"+id).style.display = "";
		document.getElementById("update_"+id).innerHTML = action.get_text();
		eval("document.getElementById(\"save_avis_\"+id).onclick=function() { save_avis("+id+"); }");
	}
	
}

function avis_exit(id){
	var div_affichage = document.getElementById('avis_'+id);
	div_affichage.style.display = 'block';
	document.getElementById('update_'+id).innerHTML = '';
}	


function save_avis(id){
	
	var action = new http_request();
	var sujet = document.getElementById("field_sujet_"+id).value;
	
	if (typeof(tinyMCE) != "undefined") {tinyMCE_execCommand('mceToggleEditor',true,"avis_desc_"+id);}
	if (typeof(tinyMCE) != "undefined") {tinyMCE_execCommand('mceRemoveControl',true,"avis_desc_"+id);}
	var desc = document.getElementById("avis_desc_"+id).value;
	var url = "./ajax.php?module=catalog&categ=avis&id="+id+"&quoifaire=update_avis";
	
	action.request(url, true, "sujet="+encodeURIComponent(sujet)+"&desc="+encodeURIComponent(desc));
	
	if(action.get_status() == 0){
		document.getElementById("update_"+id).style.display = "none";		
		document.getElementById("avis_"+id).innerHTML = action.get_text();
		document.getElementById("avis_"+id).style.display = "";
	}
	
}

function stop_evenement(event){
	
	if (event.stopPropagation) {
		  event.stopPropagation();
		}
	  event.cancelBubble = true;
	 	 
}