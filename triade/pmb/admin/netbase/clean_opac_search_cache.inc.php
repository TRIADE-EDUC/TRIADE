<?php

$v_state=urldecode($v_state);

print "<br /><br /><h2 class='center'>".htmlentities($msg["cleaning_opac_search_cache"], ENT_QUOTES, $charset)."</h2>";

$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["cleaning_opac_search_cache"], ENT_QUOTES, $charset)." : ";
$query = "truncate table search_cache";
if(pmb_mysql_query($query)){
	$query = "optimize table search_cache";
	if(pmb_mysql_query($query)){
		$v_state.= "OK";
	}else{
		$v_state.= "OK";
	}
}else{
	$v_state.= "KO";
}
$spec = $spec - CLEAN_OPAC_SEARCH_CACHE;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '2');