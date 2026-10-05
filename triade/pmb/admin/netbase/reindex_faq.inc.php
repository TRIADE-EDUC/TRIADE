<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_faq.inc.php,v 1.12 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $msg;
global $start, $v_state, $spec, $count;

require_once($base_path.'/classes/indexation.class.php');

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (empty($start)) {
	$start=0;
	//remise a zero de la table au début
	pmb_mysql_query("TRUNCATE faq_questions_words_global_index");
	pmb_mysql_query("ALTER TABLE faq_questions_words_global_index DISABLE KEYS");
	
	pmb_mysql_query("TRUNCATE faq_questions_fields_global_index");
	pmb_mysql_query("ALTER TABLE faq_questions_fields_global_index DISABLE KEYS");
}

$v_state=urldecode($v_state);

if (!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM faq_questions");
	$count = pmb_mysql_result($notices, 0, 0);
}

print netbase::get_display_progress_title($msg["nettoyage_reindex_faq"]);

$query = pmb_mysql_query("select id_faq_question from faq_questions order by id_faq_question LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {
	print netbase::get_display_progress($start, $count);
	$indexation = indexations_collection::get_indexation(AUT_TABLE_FAQ);
	$indexation->set_deleted_index(true);
	while($row = pmb_mysql_fetch_assoc($query)) {		
		// permet de charger la bonne langue, mot vide...
		$indexation->maj($row['id_faq_question']);
	}
	pmb_mysql_free_result($query);

	$next = $start + $lot;
	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - INDEX_FAQ;
	$not = pmb_mysql_query("SELECT count(distinct id_faq_question) FROM faq_questions_words_global_index");
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_reindex_faq"], $compte." ".$msg["nettoyage_res_reindex_faq"]);
	print netbase::get_process_state_form($v_state, $spec);
	pmb_mysql_query("ALTER TABLE faq_questions_words_global_index ENABLE KEYS");
	pmb_mysql_query("ALTER TABLE faq_questions_fields_global_index ENABLE KEYS");
}