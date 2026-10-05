<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_sphinx_concepts.inc.php,v 1.1.2.2 2024/12/18 15:30:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $start, $v_state, $spec, $count;

// initialisation de la borne de départ
if (!isset($start)) {
    $start=0;
    //remise a zero de la table au début
	
}

$v_state=urldecode($v_state);

if (empty($count)) {
    $count = netbase_concepts::get_count_index();
}

print netbase::get_display_progress_title("[Sphinx] ".$msg["nettoyage_reindex_concept"]);
$next = netbase_concepts::index_sphinx_from_interface($start, $count);
if($next) {
    print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
    $spec = $spec - INDEX_SPHINX_CONCEPTS;
    $not = pmb_mysql_query("SELECT count(distinct id_item) FROM skos_words_global_index");
    $compte = pmb_mysql_result($not, 0, 0);
    $v_state .= netbase::get_display_progress_v_state("[Sphinx] ".$msg["nettoyage_reindex_concept"], $compte." ".$msg["nettoyage_res_reindex_concept"]);
    print netbase::get_process_state_form($v_state, $spec);
}
