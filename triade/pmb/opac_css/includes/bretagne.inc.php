<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bretagne.inc.php,v 1.9.16.1 2025/03/25 07:32:24 dgoron Exp $

function search_other_function_filters() {
	global $bretagne_section,$charset;
	if (empty($bretagne_section)) {
	    $bretagne_section=array();
	}
	$requete="select notices_custom_list_value,notices_custom_list_lib from notices_custom_lists where notices_custom_champ=6";
	$resultat=pmb_mysql_query($requete);
	while ($res=pmb_mysql_fetch_object($resultat)) {
		$r.="<input type='checkbox' name='bretagne_section[]' value='".$res->notices_custom_list_value."' ";
		$as=array_search($res->notices_custom_list_value,$bretagne_section);
		if (($as!==null)&&($as!==false)) $r.="checked";
		$r.=">&nbsp;".htmlentities($res->notices_custom_list_lib,ENT_QUOTES,$charset)."&nbsp;";
	}
	return $r;
}

function search_other_function_clause() {
	global $bretagne_section;
	if (empty($bretagne_section)) {
	    $bretagne_section=array();
	}
	$section=implode(",",$bretagne_section);
	$r='';
	if ($section) {
		$r.="select distinct notices_custom_origine as notice_id from notices_custom_values where notices_custom_champ='6' and notices_custom_integer in (".$section.")";
	}
	return $r;
}

function search_other_function_has_values() {
	global $bretagne_section;
	if (empty($bretagne_section)) {
	    $bretagne_section=array();
	}
	if (count($bretagne_section)) return true; else return false;
}

function search_other_function_get_values(){
	global $bretagne_section;
	return serialize($bretagne_section);
}

function search_other_function_rec_history($n) {
	global $bretagne_section;
	$_SESSION["bretagne_section".$n]=$bretagne_section;
}

function search_other_function_get_history($n) {
	global $bretagne_section;
	$bretagne_section=$_SESSION["bretagne_section".$n];
}

function search_other_function_human_query($n) {
	global $bretagne_section;
	$r="";
	$bretagne_section=$_SESSION["bretagne_section".$n];
	if(is_array($bretagne_section)) {
		$section=implode(",",$bretagne_section);
	} else {
		$section='';
	}
	if ($section) {
		$requete="select notices_custom_list_value,notices_custom_list_lib from notices_custom_lists where notices_custom_champ=6 and notices_custom_list_value in ($section)";
		$resultat=pmb_mysql_query($requete);
		$sect = array();
		while ($res=pmb_mysql_fetch_object($resultat)) $sect[]=$res->notices_custom_list_lib;
		$r=implode(" ou ",$sect);
		if ($r) {
		    $r="section(s) : ".$r;
		}
	}
	return $r;
}

function search_other_function_post_values() {
	global $bretagne_section;
	$r = "";
	if ($bretagne_section && is_array($bretagne_section)) {
		$section=implode(",",$bretagne_section);
		if ($section) {
			$requete="select notices_custom_list_value,notices_custom_list_lib from notices_custom_lists where notices_custom_champ=6 and notices_custom_list_value in ($section)";
			$resultat=pmb_mysql_query($requete);
			while ($res=pmb_mysql_fetch_object($resultat)) {
				$r .= "<input type='hidden' name='bretagne_section[]' value='".$res->notices_custom_list_value."' />\n";
			}
		}
	}
	return $r;
}