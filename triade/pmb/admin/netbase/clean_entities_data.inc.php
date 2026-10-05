<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_entities_data.inc.php,v 1.4 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $v_state, $spec;

require_once ($class_path."/netbase/netbase_records.class.php");

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["cleaning_entities_data"]);

$affected=0;
$cleaned = netbase_records::clean_data();
if($cleaned) {
	$affected = count(netbase_records::get_cleaned_records());
	$v_state .= netbase::get_display_progress_v_state($msg["cleaning_entities_data"], $affected." ".$msg["cleaning_res_entities_records_data"]);
} else {
    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_entities_data"], 'KO');
}
$spec = $spec - CLEAN_ENTITIES_DATA;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);