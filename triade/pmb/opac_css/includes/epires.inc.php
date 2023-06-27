<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: epires.inc.php,v 1.9 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function search_other_function_filters() {
	global $typ_notice,$charset,$annee_parution;
	$r="";
	$r.="Année de parution <input type='text' size='5' name='annee_parution' value='".htmlentities($annee_parution,ENT_QUOTES,$charset)."'/>&nbsp;Restreindre à <input type='checkbox' name=\"typ_notice[a]\" value='1' ".($typ_notice['a']?"checked":"")."/>&nbsp;Articles de revues&nbsp;<input type='checkbox' name=\"typ_notice[m]\" value='1' ".($typ_notice['m']?"checked":"")."/>&nbsp;Tout sauf revues";
	return $r;
}

function search_other_function_clause() {
	global $typ_notice,$annee_parution;
	reset($typ_notice);
	$t_n_tab=array();
	$r = "";
	foreach ($typ_notice as $key => $val) {
	    $t_n_tab[]=$key;
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
	if ((count($typ_notice))||($annee_parution)) return true; else return false;
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
	if (count($typ_notice)) {
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
	global $typ_notice,$annee_parution;
	$ret = "";
	if ($typ_notice["m"] != "") $ret .= "<input type=\"hidden\" name=\"typ_notice[m]\" value=\"".$typ_notice["m"]."\">";
	if ($typ_notice["s"] != "") $ret .= "<input type=\"hidden\" name=\"typ_notice[s]\" value=\"".$typ_notice["s"]."\">";
	if ($typ_notice["b"] != "") $ret .= "<input type=\"hidden\" name=\"typ_notice[b]\" value=\"".$typ_notice["b"]."\">";
	if ($typ_notice["a"] != "") $ret .= "<input type=\"hidden\" name=\"typ_notice[a]\" value=\"".$typ_notice["a"]."\">";
	return "<input type=\"hidden\" name=\"annee_parution\" value=\"".$annee_parution."\">".$ret."\n";
}

?>