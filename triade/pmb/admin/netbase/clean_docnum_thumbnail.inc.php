<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_docnum_thumbnail.inc.php,v 1.6 2024/04/17 13:55:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $v_state, $spec, $start, $count;

require_once ($class_path."/netbase/netbase_records.class.php");

$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

if (empty($start)) {
    $start=0;
}
$v_state=urldecode($v_state);

if (empty($count)) {
    $nb_explnum = pmb_mysql_query("SELECT count(1) FROM explnum WHERE length(explnum_vignette) > 1000");
    $count = pmb_mysql_result($nb_explnum, 0, 0);
}

print netbase::get_display_progress_title($msg["cleaning_docnum_thumbnail"]);

$query = pmb_mysql_query("SELECT explnum_id FROM explnum WHERE length(explnum_vignette) > 1000 LIMIT $lot");
if(pmb_mysql_num_rows($query)) {
    print netbase::get_display_progress($start, $count);
    
    $next = $start + $lot;
    netbase_records::clean_docnum_thumbnail($lot);
    print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
    $spec = $spec - CLEAN_DOCNUM_THUMBNAIL;
	$v_state .= netbase::get_display_progress_v_state($msg["cleaning_docnum_thumbnail"], 'OK');
    // mise à jour de l'affichage de la jauge
    print netbase::get_display_final_progress();
    print netbase::get_process_state_form($v_state, $spec);
}

