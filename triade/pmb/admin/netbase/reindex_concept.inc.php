<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_concept.inc.php,v 1.14.2.4 2024/12/03 08:31:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $start, $v_state, $spec, $count, $pmb_clean_mode;

// Indexation par champ activée ? (sera activée par défaut par la suite))
if(!empty($pmb_clean_mode)) {
    netbase_concepts::set_indexation_by_fields(true);
    mysql_set_wait_timeout(300);
}

// initialisation de la borne de départ
if (empty($start)) {
	$start=0;
	//remise a zero de la table au début
	netbase_concepts::raz_index();
}

$v_state=urldecode($v_state);

if (empty($count)) {
	$count = netbase_concepts::get_count_index();
}
	
print netbase::get_display_progress_title($msg["nettoyage_reindex_concept"]);

$next = netbase_concepts::index_from_interface($start, $count);
if($next) {
	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - INDEX_CONCEPT;
	$not = pmb_mysql_query("SELECT count(distinct id_item) FROM skos_words_global_index");
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_reindex_concept"], $compte." ".$msg["nettoyage_res_reindex_concept"]);

	print netbase::get_process_state_form($v_state, $spec);
	netbase_concepts::enable_index();
}