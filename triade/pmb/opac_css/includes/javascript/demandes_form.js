// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_form.js,v 1.1 2023/12/27 15:36:42 dgoron Exp $

/*
variables a d�clarer dans le formulaire appelant:

 msg_demandes_note_confirm_demande_end					//
 msg_demandes_actions_nocheck							//
 msg_demandes_confirm_suppr								//message de confirmation de suppression
 msg_demandes_note_confirm_suppr						//message de confirmation de suppression d'une note
*/

/*
 * Gestion des �v�nements dans les formulaires
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
			req.request('./ajax.php?module=ajax&categ=demandes&quoifaire=show_list_action',true,'id_demande='+id_demande,true,function(data){
		  		whichElTd.innerHTML=data;
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

function change_read(el, id_demande) {
	if (!isDOM){
    	return;
	}
	var whichEl = document.getElementById(el);
	var whichIm1 = document.getElementById(el + 'Img1');
	var whichIm2 = document.getElementById(el + 'Img2');
	var whichTr = whichIm1.parentNode.parentNode;

	var req = new http_request();
	req.request('./ajax.php?module=demandes&categ=dmde&quoifaire=change_read',true,'id_demande='+id_demande,true,function(data){
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
 		}
	});
}

function verifChk(txt) {

	var elts = document.forms['liste'].elements['chk[]'];
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

	if(txt == 'suppr'){
		var sup = confirm(msg_demandes_confirm_suppr);
		if(!sup)
			return false;
		return true;
	}

	return true;
}
