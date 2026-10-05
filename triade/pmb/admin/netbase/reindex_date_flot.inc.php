<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_date_flot.inc.php,v 1.4 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $start, $v_state, $spec, $count;

require_once ($class_path."/netbase/netbase_entities.class.php");

// initialisation de la borne de départ
if (empty($start)) {
	$start=0;
	//remise a zero de la table au début
}

$v_state=urldecode($v_state);
$fields_date_flot = array();
if (empty($count)) {
    $count = 0;
    $fields_date_flot = netbase_entities::get_custom_fields_date_flot();
    if(!empty($fields_date_flot)) {
    	foreach ($fields_date_flot as $prefix=>$fields) {
    		$count += count($fields);
    	}
    }
}

print netbase::get_display_progress_title($msg["nettoyage_reindex_date_flot"]);

$counter = 0;
foreach ($fields_date_flot as $prefix=>$fields_id) {
    foreach ($fields_id as $field_id) {
		netbase_entities::index_custom_field_date_flot($prefix, $field_id);
        $counter++;
        print netbase::get_display_progress($counter, $count);
    }
}

$spec = $spec - INDEX_DATE_FLOT;
$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_reindex_date_flot"], $count." ".$msg["nettoyage_res_reindex_date_flot"]);

print netbase::get_process_state_form($v_state, $spec);
