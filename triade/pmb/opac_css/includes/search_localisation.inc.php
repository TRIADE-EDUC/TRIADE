<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_localisation.inc.php,v 1.5 2015-04-03 11:16:16 jpermanne Exp $

// Permet de positioner par défaut le sélecteur de localisation en recherche sur la localisation du lecteur
// s'il est connecté. Paramétrer opac_search_other_function

function search_other_function_filters() {
	global $recherche_loc;
	global $charset;
	global $msg,$dbh;
	// mettre par défaut la localisation du lecteur
	if(!isset($recherche_loc) && $_SESSION["empr_location"]) {
		$recherche_loc=$_SESSION["empr_location"];
	}
	$r.="<select name='recherche_loc'>";
	$r.="<option value='0'>".htmlentities($msg["search_loc_all_site"],ENT_QUOTES,$charset)."</option>";
	$requete="select location_libelle,idlocation from docs_location where location_visible_opac=1";
	$result = pmb_mysql_query($requete, $dbh);
	if (pmb_mysql_num_rows($result)){
		while (($loc = pmb_mysql_fetch_object($result))) {
			$selected="";
			if ($recherche_loc==$loc->idlocation) {$selected="selected";}
			$r.= "<option value='$loc->idlocation' $selected>$loc->location_libelle</option>";
		}
	}
	$r.="</select>";
	return $r;
}

function search_other_function_clause() {
	global $recherche_loc;

	$r="";
	if ($recherche_loc) {
		$r="select distinct notice_id from notices,exemplaires where notices.notice_id=exemplaires.expl_notice and expl_location=$recherche_loc";
	}
	return $r;
}

function search_other_function_has_values() {
	global $recherche_loc;
	if ($recherche_loc) return true; else return false;
}

function search_other_function_get_values(){
	global $recherche_loc;
	return $recherche_loc;
}

function search_other_function_rec_history($n) {
	global $recherche_loc;
	$_SESSION["recherche_loc".$n]=$recherche_loc;
}

function search_other_function_get_history($n) {
	global $recherche_loc;
	$recherche_loc=$_SESSION["recherche_loc".$n];
}

function search_other_function_human_query($n) {
	global $recherche_loc,$msg;
	$r="";
	$recherche_loc=$_SESSION["recherche_loc".$n];
	if ($recherche_loc) {
		$requete="select location_libelle from docs_location where idlocation='".$recherche_loc."' limit 1";
		$res=pmb_mysql_query($requete);
		$r=$msg["search_history_localisation_title"].@pmb_mysql_result($res,0,0);
	}
	return $r;
}

function search_other_function_post_values() {
	global $recherche_loc;
	return "<input type=\"hidden\" name=\"recherche_loc\" value=\"$recherche_loc\">\n";
}

?>