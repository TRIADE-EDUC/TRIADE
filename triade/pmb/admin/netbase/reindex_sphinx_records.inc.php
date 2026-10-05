<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_sphinx_records.inc.php,v 1.1.2.3 2024/12/18 15:30:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $start, $v_state, $spec, $count;

// initialisation de la borne de départ
if (!isset($start)) {
    $start=0;
    //remise a zero de la table au début
	
}

$v_state=urldecode($v_state);

if (!isset($count) || !$count) {
    $notices = pmb_mysql_query("SELECT count(1) FROM notices");
    $count = pmb_mysql_result($notices, 0, 0);
}

print netbase::get_display_progress_title("[Sphinx] ".$msg["nettoyage_reindex_global"]);
$next = netbase_records::index_sphinx_from_interface($start, $count);
if($next) {
    print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
    $spec = $spec - INDEX_SPHINX_RECORDS;
    $not = pmb_mysql_query("SELECT COUNT(DISTINCT id_notice) FROM notices_fields_global_index");
    $compte = pmb_mysql_result($not, 0, 0);
    $v_state .= netbase::get_display_progress_v_state("[Sphinx] ".$msg["nettoyage_reindex_global"], $compte." ".$msg["nettoyage_res_reindex_global"]);
    print netbase::get_process_state_form($v_state, $spec);
}
