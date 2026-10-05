<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: inra.inc.php,v 1.5.10.1 2025/03/25 07:32:25 dgoron Exp $

function get_field_dateparution() {
	global $field_dateparution;
	if(!$field_dateparution) {
		$q = "select idchamp from notices_custom where name='dateparution' limit 1 "; 
		$result = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($result)) {
		    $field_dateparution = pmb_mysql_result($result,0,0);
		}
	}
	if(!$field_dateparution) {
	    $field_dateparution=0;
	}
	return $field_dateparution;
}

function search_other_function_filters() {
	global $opac_view_class;
	global $pmb_opac_view_class;
	global $charset,$msg,$base_path;
	
	if(is_object($opac_view_class)) { 
		return $opac_view_class->get_list("chg_opac_view",$_SESSION['opac_view']);
	}else {
		$opac_view_class= new $pmb_opac_view_class(0,0);
		return $opac_view_class->get_list("chg_opac_view");
	}
}

function search_other_function_clause() {
	return '';
}

function search_other_function_has_values() {
	return true;
}

function search_other_function_get_values() {
	return $_SESSION['opac_view'];
}

function search_other_function_rec_history($n) {
}

function search_other_function_get_history($n) {
}

function search_other_function_human_query($n) {
	return "";
}

function search_other_function_post_values() {
    global $charset;
    
    $retour = "<input type=\"hidden\" name=\"chg_opac_view\" value=\"".htmlentities($_SESSION['opac_view'], ENT_QUOTES, $charset)."\">\n";
	
	return $retour;
}
?>