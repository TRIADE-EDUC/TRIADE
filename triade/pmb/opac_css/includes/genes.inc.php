<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: genes.inc.php,v 1.17.8.1 2025/03/25 07:32:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function search_other_function_filters() {
	global $typ_notice, $cnl_bibli;
	
	$cnl_bibli = intval($cnl_bibli);
	$r.="<select name='cnl_bibli'>";
	$r.="<option value=''>tous les sites</option>";
	$requete="select location_libelle,idlocation from docs_location where location_visible_opac=1";
	$result = pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($result)){
		while ($loc = pmb_mysql_fetch_object($result)) {
			$selected="";
			if ($cnl_bibli==$loc->idlocation) {$selected="selected";}
			$r.= "<option value='$loc->idlocation' $selected>$loc->location_libelle</option>";
		}
	}
	$r.="</select>";

	// Année de parution : fonctionnel mais désactivé pour l'instant
	//$r.="Année de parution <input type='text' size='5' name='annee_parution' value='".htmlentities($annee_parution,ENT_QUOTES,$charset)."'/>";
	$r.="<br/>Restreindre à <input type='checkbox' name=\"typ_notice[a]\" value='1' ".($typ_notice['a']?"checked":"")."/>&nbsp;Articles de revues<input type='checkbox' name=\"typ_notice[s]\" value='1' ".($typ_notice['s']?"checked":"")."/>&nbsp;Revues&nbsp;<input type='checkbox' name=\"typ_notice[m]\" value='1' ".($typ_notice['m']?"checked":"")."/>&nbsp;Tout sauf revues";
	return $r;
}

function search_other_function_clause() {
	global $typ_notice,$annee_parution;
	global $cnl_bibli;
	
	$from="";
	$where="";
	$cnl_bibli = intval($cnl_bibli);
	if ($cnl_bibli) {
		$from .= ",exemplaires";
		$where .= " and notices.notice_id=exemplaires.expl_notice and expl_location=$cnl_bibli";
	}
	$t_n_tab=array();
	if (is_array($typ_notice)) {
	    reset($typ_notice);
    	foreach ($typ_notice as $key => $val) {
    	    $t_n_tab[]=$key;
    	}
	}
	$t_n=implode("','",$t_n_tab);
	if ($t_n) {
		$t_n="'".$t_n."'";
		$where .= " and niveau_biblio in (".$t_n.")";
	}
	if ($annee_parution) {
		$where .= " and year like '%".addslashes($annee_parution)."%'";
	}
	if ($cnl_bibli || $t_n || $annee_parution) {
		$r = "select distinct notice_id from notices $from where 1 $where";
	}
	return $r;
}

function search_other_function_has_values() {
	global $typ_notice, $annee_parution;
	global $cnl_bibli;
	if (((count($typ_notice))||($annee_parution))||($cnl_bibli)) return true; else return false;
}

function search_other_function_get_values(){
	global $typ_notice, $annee_parution, $cnl_bibli;
	return $typ_notice."---".$annee_parution."---".$cnl_bibli;
}

function search_other_function_rec_history($n) {
	global $typ_notice,$annee_parution;
	global $cnl_bibli;
	$_SESSION["cnl_bibli".$n]=$cnl_bibli;
	$_SESSION["typ_notice".$n]=$typ_notice;
	$_SESSION["annee_parution".$n]=$annee_parution;
}

function search_other_function_get_history($n) {
	global $typ_notice,$annee_parution;
	global $cnl_bibli;
	$cnl_bibli=$_SESSION["cnl_bibli".$n];
	$typ_notice=$_SESSION["typ_notice".$n];
	$annee_parution=$_SESSION["annee_parution".$n];
}

function search_other_function_human_query($n) {
	global $typ_notice,$annee_parution;
	global $cnl_bibli;
	$r="";
	$cnl_bibli = intval($_SESSION["cnl_bibli".$n]);
	if ($cnl_bibli) {
		$r="bibliotheque : ";
		$requete="select location_libelle from docs_location where id_location='".$cnl_bibli."' limit 1";
		$res=pmb_mysql_query($requete);
		$r.=pmb_mysql_result($res,0,0);
		$r=" ";
	}
	$notices_t=array("m"=>"Monographies","s"=>"Périodiques","a"=>"Articles");
	$typ_notice=$_SESSION["typ_notice".$n];
	$annee_parution=$_SESSION["annee_parution".$n];
	if (is_array($typ_notice) && count($typ_notice)) {
		$r.="pour les types de notices ";
		reset($typ_notice);
		$t_l=array();
		foreach ($typ_notice as $key => $val) {
			$t_l[]=$notices_t[$key];
		}
		$r.=implode(", ",$t_l);
	}
	if ($annee_parution) {
		if ($r) $r.=" ";
		$r.="parus en ".$annee_parution;
	}
	return $r;
}

function search_other_function_post_values() {
	global $cnl_bibli, $charset;
	return "<input type=\"hidden\" name=\"cnl_bibli\" value=\"".htmlentities($cnl_bibli, ENT_QUOTES, $charset)."\">\n";
}

?>