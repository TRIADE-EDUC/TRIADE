// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: domain.js,v 1.4 2023/09/08 06:45:25 dgoron Exp $

var pbar=document.getElementById('pbar');
var pbar_progress_node=document.getElementById('pbar_progress');
var pbar_percent=document.getElementById('pbar_percent');

var dom_var_nb_cols=5;

//initialisation ressources
function pbar_init() {
	document.getElementById('pbar_ini_msg').style.display='block';
	document.getElementById('pbar_end_msg').style.display='none';
	pbar.style.display='block';
	var nb_todo=dom_updateResources(1);
	dom_updateProgress(0, nb_todo);
	dom_updateResources(3);	
	pbar_end();
	return false;
}
 
function dom_updateProgress(nb_done, nb_todo) {
	if(nb_done < nb_todo) {
		nb_done=dom_updateResources(2, nb_done);
		if(nb_done) {
			pbar_progress(nb_done, nb_todo);
			//Application d'un léger timeout pour la mise à jour de la progressbar dans le navigateur
			setTimeout(function() {dom_updateProgress(nb_done, nb_todo);}, 10);
		}
	}
}

//Mise a jour barre de progression
function pbar_progress(nb_done, nb_todo) {
	
	var p=0;
	if(nb_todo>0) {
		if(nb_done>nb_todo) nb_done=nb_todo;
		var p=Math.floor((nb_done/nb_todo)*100);
	}
	pbar_progress_node.value=p;
	pbar_percent.innerHTML=nb_done+" / "+nb_todo+" -- "+p+'%';
	return false;
}

//affichage etat final
function pbar_end() {
	
	document.getElementById('pbar_ini_msg').style.display='none';
	document.getElementById('pbar_end_msg').style.display='block';
	return false;
}

//Demande nb elements a modifier
function dom_updateResources(step, nb_done=0){
	
	var url='';
	var chk_sav_spe_rights=0;

	switch(step) {
		case 1 : 
			url= "./ajax.php?module=admin&categ=acces&dom_id="+document.getElementById('dom_id').value+"&fname=getNbResourcesToUpdate";
			break;
		case 2 :
			if(document.getElementById('chk_sav_spe_rights').checked) {
				chk_sav_spe_rights=1;
			}
			url= "./ajax.php?module=admin&categ=acces&dom_id="+document.getElementById('dom_id').value+"&fname=updateRessources&nb_done="+nb_done+"&chk_sav_spe_rights="+chk_sav_spe_rights;
			break;
		case 3 :
		default :
			url= "./ajax.php?module=admin&categ=acces&dom_id="+document.getElementById('dom_id').value+"&fname=cleanResources";
			break;
	}
	// On initialise la classe:
	var getAttr = new http_request();
	// Ex�cution de la requete
	if(getAttr.request(url)){
		// Il y a une erreur. Afficher le message retourn�
		alert (getAttr.get_text());			
	}else { 
		//alert(getAttr.get_text());
		return (parseInt(getAttr.get_text()));
	}
	return false;
}

//Decalage tableau des droits � gauche
function dom_move_left() {
	
	var par =  document.getElementById('dom_tab').children[0];
	var first_col= par.children[1];
	var last_col = par.lastElementChild;
	par.insertBefore(first_col,last_col);
	par.insertBefore(last_col,first_col);
	dom_resize_to();
	dom_update_usr_sel();
}

//Decalage tableau des droits � droite
function dom_move_right() {
	
	var par =  document.getElementById('dom_tab').children[0];
	var first_col= par.children[1];
	var last_col = par.lastElementChild;
	par.insertBefore(last_col,first_col);
	dom_resize_to();
	dom_update_usr_sel();
}

//Decalage tableau des droits en d�but de tableau
function dom_move_first() {
	
	var par =  document.getElementById('dom_tab').children[0];
	while(par.children[1].getAttribute('id')!='col_0') {
		dom_move_left();
	}
	dom_update_usr_sel();
}

//Decalage tableau des droits vers un role specifique
function dom_move_to(col_id, selected_id) {
	var par =  document.getElementById('dom_tab').children[0];
	while(par.children[1].getAttribute('id')!=col_id) {
		dom_move_right();
	}
	dom_update_usr_sel();
}

//Mise a jour selecteur role utilisateur
function dom_update_usr_sel() {
	var par =  document.getElementById('dom_tab').children[0];
	var first_col_id= par.children[1].getAttribute('id');
	var t_sel=document.getElementsByName('dom_usr_sel');
	for (var i=0;i<t_sel.length;i++){
		t_sel[i].value=first_col_id;
	}
}

//Redimensionnement de largeur visible du tableau des droits 
function dom_resize_to(nb_cols, selected_id) {
	
	var par =  document.getElementById('dom_tab').children[0];
	if(nb_cols!=undefined) {
		dom_var_nb_cols=nb_cols;
	}
	var j = dom_var_nb_cols*1+1;
	for(var i=1;i<j;i++) {
		par.children[i].style.display='table-cell';
	}
	for(var i=j;i<par.children.length;i++) {
		par.children[i].style.display='none';
	}
	if (selected_id!=undefined) {
		var t_sel=document.getElementsByName('dom_nb_col_sel');
		for (var i=0;i<t_sel.length;i++){
			t_sel[i].selectedIndex=selected_id;
		}
	}
}
