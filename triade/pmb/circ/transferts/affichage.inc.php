<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: affichage.inc.php,v 1.17 2021/03/18 08:56:37 dgoron Exp $

global $class_path;
require_once ("$class_path/mono_display.class.php");
require_once ("$class_path/serial_display.class.php");
require_once ("$class_path/notice.class.php");
require_once ("$class_path/serials.class.php");

//traite l'affichage d'une colonne
function aff_colonne($str_ligne, $nom_col, $val_col) {
	
	if 	(substr($nom_col, 0 , 9) == "val_date_") {
		$str_ligne = str_replace("!!".$nom_col."!!", formatdate($val_col), $str_ligne);
		$str_ligne = str_replace("!!".$nom_col."_mysql!!", $val_col, $str_ligne);
	} elseif ($nom_col=="val_ex") {
		//c'est le no d'exemplaire
		$str_ligne = str_replace("!!val_ex!!",aff_exemplaire($val_col),$str_ligne);
	} elseif ($nom_col=="val_empr") {
		//c'est le cb lecteur
		$str_ligne = str_replace("!!val_empr!!",aff_emprunteur($val_col),$str_ligne);
	} elseif ($nom_col=="val_section") {
		$str_ligne = str_replace("!!".$nom_col."!!",do_liste_section($val_col), $str_ligne);
	} elseif ($nom_col=="val_statut") {//Il faut mettre l'info de retour si il est emprunté
		$str_ligne = str_replace("!!".$nom_col."!!",aff_statut_exemplaire($val_col), $str_ligne);
	} else {
		$str_ligne = str_replace("!!".$nom_col."!!",$val_col, $str_ligne);
	}

	return $str_ligne;
}

//renvoi le no d'exemplaire pour le tableau avec ou sans lien
function aff_exemplaire($cb_expl) {
		
	$des_expl = "<a href='./circ.php?categ=visu_ex&form_cb_expl=" . $cb_expl . "'>";
	$des_expl .= $cb_expl;
	$des_expl .= "</a>";

	return $des_expl;
}

//renvoi le nom du lecteur pour le tableau avec ou sans lien
function aff_emprunteur($cb_empr='') {

	if ($cb_empr == '') return;
	
	$rqt = "select concat(empr_nom,' ',empr_prenom) as empr_nom_prenom from empr where empr_cb='".$cb_empr."'";
	$result = pmb_mysql_query($rqt);
	
	if (SESSrights & CIRCULATION_AUTH) {
		$des_empr = "<a href='./circ.php?categ=pret&form_cb=" . $cb_empr . "'>";
		$des_empr .= pmb_mysql_result($result, 0, "empr_nom_prenom");
		$des_empr .= "</a>";
	} else
		$des_empr = pmb_mysql_result($result, 0, "empr_nom_prenom");

	return $des_empr;
}

//renvoi le titre de l'exemplaire pour le tableau avec ou sans lien
function aff_titre($id_notice,$id_bulletin) {
	$link="";
	if ($id_notice!=0) {
		
		//c'est une notice
		if (SESSrights & CATALOGAGE_AUTH)
			$link = notice::get_pattern_link();
		$disp = new mono_display($id_notice,0,$link);
		
		
	} else {
		//c'est un bulletin
		if (SESSrights & CATALOGAGE_AUTH) 
			$link = serial::get_pattern_link();
		$disp = new bulletinage_display($id_bulletin,0,$link);
	}
	
	return  $disp->header;
}

function aff_user($id) {
	if(!$id) return '';
	$result = pmb_mysql_query('SELECT username FROM users where userid="'.$id.'" ');
	if ($r = pmb_mysql_fetch_object($result)) {
		return $r->username;
	}
	return '';
}

//renvoi le statut de l'exemplaire
function aff_statut_exemplaire($val_col){
	global $msg;
	$message="";
	$tmp=explode("###",$val_col);
	if(preg_match("/^(.+?)###([0-9]+)$/",$val_col,$matches)){
		$requete="SELECT date_format(pret_retour, '".$msg["format_date"]."') AS aff_pret_retour FROM pret WHERE pret_idexpl='".$matches[2]."'";
		$res=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)){//On affiche la date de retour
			$message=$matches[1]."<br/><strong>".$msg["358"]." ".pmb_mysql_result($res,0,0)."</strong>";
		}else{//On affiche le statut de l'exemplaire
			$message=$matches[1];
		}
	}else{
		$message=$val_col;
	}
	return $message;
}

//fonction de generation de select
function do_liste($rqt, $idsel) {
	//on execute la requete
	$res = pmb_mysql_query($rqt);
	$tmpOpt = "";
	
	//on parcours la liste des options
	while ($value = pmb_mysql_fetch_array($res)) {
		//debut de l'option
		$tmpOpt .= "<option value='" . $value[0] . "'";
		
		if ($value[0]==$idsel)
			//c'est l'option par défaut
			$tmpOpt .= " selected";
		
		//fin de l'option
		$tmpOpt .= ">" . $value[1] . "</option>";
	}
	
	//on retourne la liste
	return $tmpOpt;
}

//fonction de generation de select avec les statuts
function do_liste_section($idselect) {
	global $deflt_docs_location;
	return do_liste("SELECT idsection, section_libelle FROM docs_section INNER JOIN docsloc_section ON idsection=num_section WHERE num_location=".$deflt_docs_location ,$idselect);
}

//fonction de generation de select avec les statuts
function do_liste_statut($idselect) {
	return do_liste("SELECT idstatut, statut_libelle FROM docs_statut order by statut_libelle",$idselect);
}

//fonction de generation de select avec les localisations
function do_liste_localisation($idselect) {
	return do_liste("SELECT idlocation, location_libelle FROM docs_location ORDER BY 2",$idselect);
}

?>
