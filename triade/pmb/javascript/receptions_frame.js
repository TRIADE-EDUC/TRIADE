// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: receptions_frame.js,v 1.4 2011/08/10 10:08:27 dbellamy Exp $

/*
 * nécessite :	ajax.js
 * 				tablist.js
 * 				http_request.js
 * 
 * variables a déclarer dans le formulaire appelant:
 * msg_error_cb_cote				//cote et cb obligatoires
 * msg_error_cb						//cb déjà utilisé
 * msg_acquisition_recept_qte_err		//qté incorrecte
 *   
 */			


//initialisations
var base_path = './../../..';
var imgOpened = new Image();
imgOpened.src = base_path+'/images/minus.gif';
var imgClosed = new Image();
imgClosed.src = base_path+'/images/plus.gif';
var imgPatience = new Image();
imgPatience.src = base_path+'/images/patience.gif';


//Modification d'un commentaire
 function recept_mod_comment(id){
	var comment = document.getElementById(id);
	var comment_mod = document.getElementById(id+'_mod');
	comment_mod.value = comment.innerHTML.replace(/<br>/g, '\r');
	comment.style.display='none';
	comment_mod.parentNode.style.display='block';
	comment_mod.focus();
}

 
//Annulation modification d'un commentaire
 function recept_undo_comment(id){
	 var comment = document.getElementById(id);
	 var comment_mod = document.getElementById(id+'_mod');
	 comment_mod.parentNode.style.display='none';
	 comment.style.display='block';
}
 
 
//Mise a jour et enregistrement d'un commentaire
function recept_upd_comment(id){
	var comment = document.getElementById(id);
	var comment_mod = document.getElementById(id+'_mod');
	var url= base_path+"/ajax.php?module=acquisition&categ=ach&sub=recept&quoifaire=upd_comment&id="+id+"&comment="+encodeURIComponent(comment_mod.value);
	var req = new http_request();
	if(req.request(url,1)){
		//Il y a une erreur
	} else {
		comment_mod.value = '';
		comment.innerHTML = req.get_text();
		comment_mod.parentNode.style.display='none';
		comment.style.display='block';
		//maj parent
		window.parent.document.getElementById(id).innerHTML = req.get_text();
	}
}


//Suppression d'un commentaire
function recept_del_comment(id){
	var comment = document.getElementById(id);
	var comment_mod = document.getElementById(id+'_mod');
	comment.innerHTML='';
	comment_mod.Value='';
	recept_upd_comment(id);
}


//Mise a jour d'un statut de ligne
function recept_upd_lgstat (id) {
	var lgstat=document.getElementById(id); 
	var url= base_path+"/ajax.php?module=acquisition&categ=ach&sub=recept&quoifaire=upd_lgstat&id="+id+"&lgstat="+lgstat.value;
	var req = new http_request();
	if(req.request(url,1)){
		//Il y a une erreur
	} else {
		//maj parent
		window.parent.document.getElementById(id).value=lgstat.value;
	}
}


//enregistrement piece jointe ou url
function recept_upload_file(form) {
	var f = document.getElementById('f_fichier');
	var u = document.getElementById('f_url');
	if (f.value!='' || u.value!='') {
		form.setAttribute('action',base_path+'/acquisition/achats/receptions/receptions_frame.php?action=upload_file');
		form.submit();
	}
}


//ajout exemplaire
function recept_add_expl(form) {
	var cb = form.elements['f_ex_cb'];
	cb.value = cb.value.replace(/^ */,'');
	cb.value = cb.value.replace(/ *$/,'');
	
	var cote = form.elements['f_ex_cote'];
	cote.value = cote.value.replace(/^ */,'');
	cote.value = cote.value.replace(/ *$/,'');
	if (cb.value=='' || cote.value=='') {
		alert(msg_error_cb_cote);
		return false;
	}
	form.setAttribute('action',base_path+'/acquisition/achats/receptions/receptions_frame.php?action=add_expl');
	form.submit();
}

//enregistrement réception
function recept_update(form) {
	var qte_liv=form.elements['qte_liv'];
	if (isNaN(qte_liv.value) || qte_liv.value<=0) {
		alert(msg_acquisition_recept_qte_err);
		return false;
	}
	form.setAttribute('action',base_path+'/acquisition/achats/receptions/receptions_frame.php?action=update');
	form.submit();
}


//mise à jour statut suggestion
function recept_update_sug(form) {
	form.setAttribute('action',base_path+'/acquisition/achats/receptions/receptions_frame.php?action=update_sug');
	form.submit();
}


//mise à jour de la liste dans la fenetre parent
function recept_update_liste () {
	try {
		var no = document.getElementById('no').value;
		var qte_cde = document.getElementById('qte_cde').innerHTML;
		var qte_sol = document.getElementById('qte_sol').innerHTML;
		var lst_qte_sol = window.parent.document.getElementById('qte_sol['+no+']');
		var lst_qte_rec = window.parent.document.getElementById('qte_rec['+no+']');
		lst_qte_sol.innerHTML = qte_sol;
		lst_qte_rec.innerHTML = (qte_cde-qte_sol);
	} catch(err) {}
}

//annulation précédente réception
function recept_undo(form) {
	form.setAttribute('action',base_path+'/acquisition/achats/receptions/receptions_frame.php?action=undo');
	form.submit();
}

//passage à la ligne suivante
function recept_next(direction) {
	var next = document.getElementById('no').value*1+(direction);
	var max_no = window.parent.document.getElementById('max_no').value;
	if (direction==1 && next>max_no) {
		next=1;
	}
	if (direction==-1 && next<1) {
		next=max_no;
	}
	window.parent.document.getElementById('bt_rec['+next+']').click();
}


//calcul de la section en fonction de la localisation
function calcule_section(selectBox) {
	for (i=0; i<selectBox.options.length; i++) {
		id=selectBox.options[i].value;
	    list=document.getElementById('docloc_section'+id);
	    list.style.display='none';
	}

	id=selectBox.options[selectBox.selectedIndex].value;
	list=document.getElementById('docloc_section'+id);
	list.style.display='block';
}


//effacement message client
function recept_clear_msg() {
	document.getElementById('msg_client').style.display='none';
}


recept_update_liste();

var msg_client =document.getElementById('msg_client');
if (msg_client.innerHTML!='') msg_client.style.display='block';
setTimeout('recept_clear_msg()', 2000);
try {
	document.getElementById('qte_liv').focus();	
} catch(err){};
