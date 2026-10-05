// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_form.js,v 1.2.22.1 2025/04/16 08:15:53 rtigero Exp $

/*
variables a déclarer dans le formulaire appelant:

 msg_demandes_note_confirm_demande_end					//
 msg_demandes_actions_nocheck							//
 msg_demandes_confirm_suppr								//message de confirmation de suppression
 msg_demandes_note_confirm_suppr						//message de confirmation de suppression d'une note
*/

/*
 * Gestion des évènements dans les formulaires
 */

function expand_action(el, id_demande , unexpand) {
	if (!isDOM){
    	return;
	}
	
	var whichEl = document.getElementById(el + 'Child');
	var whichElTd = document.getElementById(el + 'ChildTd');
	var whichIm = document.getElementById(el + 'Img');
	
  	if(whichEl.style.display == 'none') {
		if(whichElTd.innerHTML==''){
			var req = new http_request();
			req.request('./ajax.php?module=demandes&categ=dmde&quoifaire=show_list_action',true,'id_demande='+id_demande,true,function(data){
		  		whichElTd.innerHTML=data;
		  		parse_dynamic_elts();
			});
		}
		whichEl.style.display  = '';
    	if (whichIm){
    		whichIm.src= imgOpened.src;
    	}
    	changeCoverImage(whichEl);
	}else if(unexpand) {
    	whichEl.style.display='none';
    	if (whichIm){
    		whichIm.src=imgClosed.src;
    	}
  	}		
}

function expand_note(el, id_action , unexpand) {
	if (!isDOM){
    	return;
	}
	
	var whichEl = document.getElementById(el + 'Child');
	var whichElTd = document.getElementById(el + 'ChildTd');
	var whichIm = document.getElementById(el + 'Img');
	
  	if(whichEl.style.display == 'none') {
		if(whichElTd.innerHTML==''){
			var req = new http_request();
			req.request('./ajax.php?module=demandes&categ=note&quoifaire=show_dialog',true,'id_action='+id_action,true,function(data){
		  		whichElTd.innerHTML=data;
		  		window.location.href="#fin";
			});
		}
		whichEl.style.display  = '';
    	if (whichIm){
    		whichIm.src= imgOpened.src;
    	}
    	changeCoverImage(whichEl);
		window.location.href="#fin";
	}else if(unexpand) {
    	whichEl.style.display='none';
    	if (whichIm){
    		whichIm.src=imgClosed.src;
    	}
  	}		
}

function change_read_dmde(el, id_demande) {
	if (!isDOM){
    	return;
	}		
	var whichEl = document.getElementById(el);	
	var whichIm1 = document.getElementById(el + 'Img1');
	var whichIm2 = document.getElementById(el + 'Img2');	
	var whichTr = whichIm1.parentNode.parentNode;
	var whichTrAction = document.getElementById('action'+id_demande+'ChildTd');	
	
	var req = new http_request();
	req.request('./ajax.php?module=demandes&categ=dmde&quoifaire=change_read_dmde',true,'id_demande='+id_demande,true,function(data){
 		if(data != ''){
			if(whichIm1.style.display == ''){
				whichIm1.style.display = 'none';
				whichIm2.style.display = '';
			} else {
				whichIm1.style.display = '';
				whichIm2.style.display = 'none';	
			}
		
			if(whichIm1.parentNode.parentNode.style.fontWeight == ''){
				whichIm1.parentNode.parentNode.style.fontWeight = 'bold';
				
			} else {
				whichIm1.parentNode.parentNode.style.fontWeight = '';
				
			}	
			
 		}
	});		
}

function change_read_action(el, id_action, id_demande) {
	if (!isDOM){
    	return;
	}		
	var whichEl = document.getElementById(el);	
	var whichIm1 = document.getElementById(el + 'Img1');
	var whichIm2 = document.getElementById(el + 'Img2');	
	var whichTr = whichIm1.parentNode.parentNode;
	if(document.getElementById('dmde'+id_demande)) {
		var whichElDmde = document.getElementById('dmde'+id_demande);
		var whichIm1dmde = document.getElementById('dmde' + id_demande + 'Img1');
		var whichIm2dmde = document.getElementById('dmde' + id_demande + 'Img2');
	}
	
	var req = new http_request();
	req.request('./ajax.php?module=demandes&categ=action&quoifaire=change_read_action',true,'id_action='+id_action,true,function(data){
 		
		if(data == 1){
			if(whichIm1.style.display == ''){
				whichIm1.style.display = 'none';
				whichIm2.style.display = '';
			} else {
				whichIm1.style.display = '';
				whichIm2.style.display = 'none';	
			}
			
			if(whichIm1.parentNode.parentNode.style.fontWeight == ''){
				whichIm1.parentNode.parentNode.style.fontWeight = 'bold';
				
			} else {
				whichIm1.parentNode.parentNode.style.fontWeight = '';
			}
			if(whichElDmde) {
				if(whichElDmde.style.fontWeight != 'bold'){
					if(whichIm1dmde.style.display == ''){
						whichIm1dmde.style.display = 'none';
						whichIm2dmde.style.display = '';
					} else {
						whichIm1dmde.style.display = '';
						whichIm2dmde.style.display = 'none';	
					}
				
					if(whichElDmde.style.fontWeight == ''){
						whichElDmde.style.fontWeight = 'bold';
					} else {
						whichElDmde.style.fontWeight = '';
					}
				}
			}			
 		}
	});		
}

function change_read_note(el, id_note,id_action,id_demande) {
	if (!isDOM){
    	return;
	}		
	var whichEl = document.getElementById(el);	
	var whichIm1 = document.getElementById(el + 'Img1');
	var whichIm2 = document.getElementById(el + 'Img2');
	if(document.getElementById('action'+id_action)) {
		var whichElAction =	document.getElementById('action'+id_action);
		var whichIm1Action = document.getElementById('read'+id_action + 'Img1');
		var whichIm2Action = document.getElementById('read' +id_action + 'Img2');
	}
	var req = new http_request();
	var tab = {"id_note":id_note,"id_action":id_action,"id_demande":id_demande};
	req.request('./ajax.php?module=demandes&categ=note&quoifaire=change_read_note',true,'tab='+JSON.stringify(tab),true,function(data){
 		if(data == 1){
			if(whichIm1.style.display == ''){
				whichIm1.style.display = 'none';
				whichIm2.style.display = '';
			} else {
				whichIm1.style.display = '';
				whichIm2.style.display = 'none';	
			}
			if(whichElAction) {
				if(whichElAction.style.fontWeight != 'bold'){
					if(whichIm1Action.style.display == ''){
						whichIm1Action.style.display = 'none';
						whichIm2Action.style.display = '';
					} else {
						whichIm1Action.style.display = '';
						whichIm2Action.style.display = 'none';	
					}
				
					if(whichElAction.style.fontWeight == ''){
						whichElAction.style.fontWeight = 'bold';
					} else {
						whichElAction.style.fontWeight = '';
					}
				}
			}
		}
	});		
}

function change_demande_end(el, id_note,id_action,id_demande) {
	if (!isDOM){
    	return;
	}
	if(!confirm_demande_end()) return;
	
	var whichIm3 = document.getElementById(el + 'Img3');		
	
	var req = new http_request();
	var tab = {"id_note":id_note,"id_action":id_action,"id_demande":id_demande};
	req.request('./ajax.php?module=demandes&categ=note&quoifaire=final_response',true,'tab='+JSON.stringify(tab),true,function(data){
 		if(data == 1){
			whichIm3.style.color='red';
		}
	});		
}
	
function confirm_demande_end(){
	result = confirm(msg_demandes_note_confirm_demande_end);
	if(result){
		return true;
	}
	return false;
}
		
function verifChkAction(form_name, id_demande) {
	var elts_name = 'chk_action_'+id_demande+'[]';		
	var elts = document.forms[form_name].elements['chk_action_'+id_demande+'[]'];
	var elts_cnt  = (typeof(elts.length) != 'undefined')
              ? elts.length
              : 0;
	nb_chk = 0;
	if (elts_cnt) {
		for(var i=0; i < elts.length; i++) {
			if (elts[i].checked) nb_chk++;
		}
	} else {
		if (elts.checked) nb_chk++;
	}
	if (nb_chk == 0) {
		alert(msg_demandes_actions_nocheck);
		return false;	
	}
	
	var sup = confirm(msg_demandes_confirm_suppr);
	if(!sup) 
		return false;
	return true;
}

function confirm_delete_note(id_action) {
	result = confirm(msg_demandes_note_confirm_suppr);
	if(result){
		//Dans le cas d'une suppression de note depuis la liste des actions on passe l'action en cours au formulaire
		if(document.forms['liste_action']) {
			document.forms['liste_action'].idaction.value = id_action;
		}
		return true;
	}
	return false;
}

/**
 * Fonction permettant de remplir le formulaire de la liste des actions avec l'action en cours de modif
 */
function setup_note_form_from_dialog(form, id_action) {
	form.idaction.value = id_action;
	form.contenu_note.value = form['contenu_note_' + id_action].value;
	form.redirectto.value = form['redirectto_' + id_action].value;
	form.ck_prive.value = form['ck_prive_' + id_action].checked ? 1 : 0;
	form.ck_vue.value = form['ck_vue_' + id_action].checked ? 1 : 0;
	form.ck_rapport.value = form['ck_rapport_' + id_action].checked ? 1 : 0;
}