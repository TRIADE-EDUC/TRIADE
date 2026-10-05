// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis.js,v 1.12 2021/03/30 13:32:22 dgoron Exp $


function show_avis(id, object_id, object_type) {
	var div_avis=document.getElementById('avis_'+id+'_'+object_type+'_'+object_id);
	if(div_avis.style.display  == 'block'){
		div_avis.style.display  = 'none';
	}else{
		div_avis.style.display  = 'block';
	}
}

//avis sauvegard� en Ajax
function save_avis(id, object_id, object_type) {
	var note=3;
	var boutons_note = document.getElementsByName('avis_'+id+'_note_'+object_type+'_'+object_id);
	if(boutons_note.length == 1) {
		boutons_note = document.getElementById('avis_'+id+'_note_'+object_type+'_'+object_id);
		if(boutons_note){
			var selIndex = boutons_note.selectedIndex;
			note = boutons_note.options[selIndex].value;
		}
	} else {
		for (var i=0; i < boutons_note.length; i++) {
			if (boutons_note[i].checked) {
				note=i + 1;
			}
		}
	}
	var sujet=document.getElementById('avis_'+id+'_sujet_'+object_type+'_'+object_id).value;
	var commentaire=document.getElementById('avis_'+id+'_commentaire_'+object_type+'_'+object_id).value;
	if(document.getElementById('avis_'+id+'_private_'+object_type+'_'+object_id).checked) {
		var private = 1;
	} else {
		var private = 0;
	}
	var num_liste_lecture=0;
	if(document.getElementById('avis_'+id+'_listes_lecture_'+object_type+'_'+object_id)) {
		num_liste_lecture = document.getElementById('avis_'+id+'_listes_lecture_'+object_type+'_'+object_id).value;
	}
	if(	sujet  || commentaire){
		var url= './ajax.php?module=ajax&categ=avis&sub=save';
		url+='&id='+id;
		url+='&note='+note;
		url+='&'+object_type+'_id='+object_id;

		var req = new http_request();
		req.request(url, true, 'sujet='+encodeURIComponent(sujet)+'&commentaire='+encodeURIComponent(commentaire)+'&private='+private+'&num_liste_lecture='+num_liste_lecture);

		var response=req.get_text();
		avis_callback_response(id, object_id, object_type, response);
	}
	return 1;
}		

function display_listes_lecture(id, notice_id) {
	if(document.getElementById('avis_'+id+'_private_notice_'+notice_id)) {
		if(document.getElementById('avis_'+id+'_private_notice_'+notice_id).checked) {
			document.getElementById('avis_'+id+'_display_listes_lecture_notice_'+notice_id).setAttribute('style', '');
		} else {
			document.getElementById('avis_'+id+'_display_listes_lecture_notice_'+notice_id).setAttribute('style', 'display:none;');
		}
	}
}

function avis_expand_list(node_id) {
	var element = document.getElementById(node_id + "_child");
	var img = document.getElementById(node_id + "_img");
	if (element.style.display) {
		element.style.display = "";
		if(img) img.src = img.src.replace("plus","moins");
	} else {
		element.style.display = "none";
		if(img) img.src = img.src.replace("moins","plus");
	}
}

function avis_checked(id, object_id, object_type) {
	var avis_checked = document.getElementsByName('avis_'+id+'_note_'+object_type+'_'+object_id);
	for(var i=0; i < avis_checked.length; i++) {
		if(avis_checked[i].checked) {
			document.getElementById('avis_'+id+'_detail_note_'+object_type+'_'+object_id).innerHTML = (avis_checked[i].title);
		}
	}
}

function delete_avis(id, object_id, object_type) {
	if(id) {
		var url= './ajax.php?module=ajax&categ=avis&sub=delete&id='+id+'&'+object_type+'_id='+object_id;
		var req = new http_request();
		req.request(url);
		var response=req.get_text();
		avis_callback_response(id, object_id, object_type, response);
	}
}

function avis_callback_response(id, object_id, object_type, response) {
	if(response=='1'){
		document.getElementById('avis_'+id+'_'+object_type+'_'+object_id).innerHTML = '<label>'+msg_avis_validation_en_cours+'</label>';		
	}else if(response=='0'){ 
		// erreur		
	}else{
		if(document.getElementById('avis_'+object_id)) {
			var newNode = element = document.createElement('div');
			newNode.innerHTML = response;
			var oldNode = document.getElementById('avis_'+object_id);
			oldNode.parentNode.replaceChild(newNode, oldNode);
			
			if(document.getElementById('avis_'+object_id)) {
				var scripts = document.getElementById('avis_'+object_id).getElementsByTagName("script");
				for(var i=0; i<scripts.length; i++) {
					if (window.execScript)
						window.execScript(scripts[i].text.replace('<!--',''));
					else
						window.eval(scripts[i].text);
				}
			}
		}
		//on est dans le contexte de la popup, rafraichir la note et le nombre d'avis
		if(window.opener != null) {
			if(window.opener.document.getElementById('avis'+object_id)) {
				var url= './ajax.php?module=ajax&categ=avis&sub=refresh&'+object_type+'_id='+object_id;	
				var req = new http_request();
				req.request(url);
				window.opener.document.getElementById('avis'+object_id).innerHTML = req.get_text();
			}
		}
	}
}