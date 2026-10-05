<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_global.inc.php,v 1.29.2.2 2024/12/03 08:31:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $msg;
global $start, $v_state, $spec, $count, $pass2, $pmb_clean_mode, $step_position;

require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/noeuds.class.php');

$v_state=urldecode($v_state);

if (!isset($count) || !$count) {
    $notices = pmb_mysql_query("SELECT count(1) FROM notices");
    $count = pmb_mysql_result($notices, 0, 0);
}
//On traite d'abord la table notice_global_index
if(!isset($pass2) || !$pass2) {
    // la taille d'un paquet de notices
    $lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php
    if(defined('REINDEX_GLOBAL_PAQUET_SIZE') && REINDEX_GLOBAL_PAQUET_SIZE > $lot) {
        $lot = REINDEX_GLOBAL_PAQUET_SIZE;
    }
	// initialisation de la borne de départ
	if (empty($start)) {
		$start=0;
		//remise a zero de la table au début
		pmb_mysql_query("TRUNCATE notices_global_index");
		pmb_mysql_query("ALTER TABLE notices_global_index DISABLE KEYS");
	}
	print netbase::get_display_progress_title($msg["nettoyage_reindex_global"]." (Partie 1 / 2)");
	print netbase::get_display_progress($start, $count);
	
	$nb_indexed = netbase_records::global_index_from_query("select notice_id as id from notices order by notice_id LIMIT $start, $lot");
	if($nb_indexed) {
		$next = $start + $lot;
		print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
	} else {
		print netbase::get_process_state_form($v_state, $spec, '', '1');
		pmb_mysql_query("ALTER TABLE notices_global_index ENABLE KEYS");
	}
} elseif ($pass2==1) {
	// initialisation de la borne de départ
    if (empty($start) && empty($step_position)) {
		$start=0;
		//remise a zero de la table au début
		netbase_records::raz_index();
	}
	print netbase::get_display_progress_title($msg["nettoyage_reindex_global"]." (Partie 2 / 2)");
	
	// Indexation par champ activée ? (sera activée par défaut par la suite))
	if(!empty($pmb_clean_mode)) {
	    netbase_records::set_indexation_by_fields(true);
	    mysql_set_wait_timeout(300);
	}
	if(!empty($step_position)) {
	    netbase_records::set_step_position($step_position);
	}
    $next = netbase_records::index_from_interface($start, $count);
    $next_position = netbase_records::get_step_position();
	
	if($next || $next_position) {
	    print netbase::get_current_state_form($v_state, $spec, '', $next, $count, $pass2, $next_position);
	} else {
	    $spec = $spec - INDEX_GLOBAL;
	    $not = pmb_mysql_query("SELECT COUNT(DISTINCT id_notice) FROM notices_fields_global_index");
	    $compte = pmb_mysql_result($not, 0, 0);
	    $v_state .= netbase::get_display_progress_v_state($msg["nettoyage_reindex_global"], $compte." ".$msg["nettoyage_res_reindex_global"]);
	    print netbase::get_process_state_form($v_state, $spec);
	    netbase_records::enable_index();
	}
}