<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_cache_amende.inc.php,v 1.9 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $v_state, $spec;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["cleaning_cache_amende"]);

$query = "truncate table cache_amendes";
if(pmb_mysql_query($query)){
	$query = "optimize table cache_amendes";
	if(pmb_mysql_query($query)){
	    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_cache_amende"], 'OK');
	}else{
	    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_cache_amende"], 'KO');
	}
}else{
    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_cache_amende"], 'KO');
}
$spec = $spec - CLEAN_CACHE_AMENDE;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);