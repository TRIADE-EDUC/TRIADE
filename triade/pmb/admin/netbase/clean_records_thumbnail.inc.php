<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_records_thumbnail.inc.php,v 1.5 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $v_state, $spec;

require_once ($class_path."/netbase/netbase_records.class.php");

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["cleaning_records_thumbnail"]);

$cleaned = netbase_records::clean_thumbnail();
if($cleaned) {
    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_records_thumbnail"], 'OK');
} else {
    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_records_thumbnail"], $msg['notice_img_folder_no_access']);
}

$spec = $spec - CLEAN_RECORDS_THUMBNAIL;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);