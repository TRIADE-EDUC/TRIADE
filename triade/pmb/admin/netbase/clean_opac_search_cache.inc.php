<?php

global $msg;
global $v_state, $spec;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["cleaning_opac_search_cache"]);

$query = "truncate table search_cache";
if(pmb_mysql_query($query)){
	$query = "optimize table search_cache";
	if(pmb_mysql_query($query)){
	    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_opac_search_cache"], 'OK');
	}else{
	    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_opac_search_cache"], 'KO');
	}
}else{
    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_opac_search_cache"], 'KO');
}
$spec = $spec - CLEAN_OPAC_SEARCH_CACHE;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '2');