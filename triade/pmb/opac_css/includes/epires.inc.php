<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: epires.inc.php,v 1.10.10.1 2025/03/21 13:05:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function search_other_function_filters() {
	global $typ_notice,$charset,$annee_parution;
	
	if (!is_array($typ_notice)) {
	    $typ_notice = array();
	}
	return "Année de parution <input type='text' size='5' name='annee_parution' value='".htmlentities($annee_parution,ENT_QUOTES,$charset)."'/>&nbsp;Restreindre à <input type='checkbox' name=\"typ_notice[a]\" value='1' ".(isset($typ_notice['a']) && $typ_notice['a'] ? "checked":"")."/>&nbsp;Articles de revues&nbsp;<input type='checkbox' name=\"typ_notice[m]\" value='1' ".(isset($typ_notice['m']) && $typ_notice['m'] ? "checked" : "")."/>&nbsp;Tout sauf revues";
}

function search_other_function_clause() {
	global $typ_notice,$annee_parution;
	
	$t_n_tab=array();
	$r = "";
	if (is_array($typ_notice)) {
        reset($typ_notice);
    	foreach ($typ_notice as $key => $val) {
    	    $t_n_tab[]=$key;
    	}
	}
	$t_n=implode("','",$t_n_tab);
	if ($t_n) {
		$t_n="'".$t_n."'";
		$r .= "select distinct notice_id from notices where niveau_biblio in (".$t_n.")";
		if ($annee_parution) {
			$r .= " and year like '%".$annee_parution."%'";
		}
	} else {
		if ($annee_parution) {
			$r .= "select distinct notice_id from notices where year like '%".$annee_parution."%'";
		}
	}
	return $r;
}

function search_other_function_has_values() {
	global $typ_notice, $annee_parution;
	if ((is_countable($typ_notice) && count($typ_notice))||($annee_parution)) {
	    return true;
	} else {
	    return false;
	}
}

function search_other_function_get_values(){
	global $typ_notice, $annee_parution;
	return serialize($typ_notice)."---".$annee_parution;
}


function search_other_function_rec_history($n) {
	global $typ_notice,$annee_parution;
	$_SESSION["typ_notice".$n]=$typ_notice;
	$_SESSION["annee_parution".$n]=$annee_parution;
}

function search_other_function_get_history($n) {
	global $typ_notice,$annee_parution;
	$typ_notice=$_SESSION["typ_notice".$n];
	$annee_parution=$_SESSION["annee_parution".$n];
}

function search_other_function_human_query($n) {
	global $typ_notice,$annee_parution;
	$r="";
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
	global $typ_notice, $annee_parution, $charset;
	$ret = "";
	if (is_array($typ_notice)) {
	    if (isset($typ_notice["m"]) && $typ_notice["m"] != "") {
    	    $ret .= "<input type=\"hidden\" name=\"typ_notice[m]\" value=\"".htmlentities($typ_notice["m"], ENT_QUOTES, $charset)."\">";
    	}
    	if (isset($typ_notice["s"]) && $typ_notice["s"] != "") {
    	    $ret .= "<input type=\"hidden\" name=\"typ_notice[s]\" value=\"".htmlentities($typ_notice["s"], ENT_QUOTES, $charset)."\">";
    	}
    	if (isset($typ_notice["b"]) && $typ_notice["b"] != "") {
    	    $ret .= "<input type=\"hidden\" name=\"typ_notice[b]\" value=\"".htmlentities($typ_notice["b"], ENT_QUOTES, $charset)."\">";
    	}
    	if (isset($typ_notice["a"]) && $typ_notice["a"] != "") {
    	    $ret .= "<input type=\"hidden\" name=\"typ_notice[a]\" value=\"".htmlentities($typ_notice["a"], ENT_QUOTES, $charset)."\">";
    	}
	}
	return "<input type=\"hidden\" name=\"annee_parution\" value=\"".htmlentities($annee_parution, ENT_QUOTES, $charset)."\">".$ret."\n";
}
