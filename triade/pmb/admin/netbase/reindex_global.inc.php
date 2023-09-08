<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_global.inc.php,v 1.19 2018-06-19 11:18:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/noeuds.class.php');

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (empty($start)) {
	$start=0;
	//remise a zero de la table au début
	pmb_mysql_query("TRUNCATE notices_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE notices_global_index DISABLE KEYS",$dbh);
	
	pmb_mysql_query("TRUNCATE notices_mots_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE notices_mots_global_index DISABLE KEYS",$dbh);
	
	pmb_mysql_query("TRUNCATE notices_fields_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE notices_fields_global_index DISABLE KEYS",$dbh);
}

$v_state=urldecode($v_state);

if (!isset($count) || !$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
	$count = pmb_mysql_result($notices, 0, 0);
}
	
print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_global"], ENT_QUOTES, $charset)."</h2>";

$NoIndex = 1;

$query = pmb_mysql_query("select notice_id from notices order by notice_id LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {
	print netbase::get_display_progress($start, $count);
	notice::set_deleted_index(true);
	while($mesNotices = pmb_mysql_fetch_assoc($query)) {
		// Mise à jour de tous les index de la notice
		notice::majNoticesTotal($mesNotices['notice_id']);
	}
	pmb_mysql_free_result($query);

	$next = $start + $lot;
	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - INDEX_GLOBAL;
	$not = pmb_mysql_query("SELECT count(1) FROM notices_global_index", $dbh);
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_global"], ENT_QUOTES, $charset)." :";
	$v_state .= $compte." ".htmlentities($msg["nettoyage_res_reindex_global"], ENT_QUOTES, $charset);
	print netbase::get_process_state_form($v_state, $spec);
	pmb_mysql_query("ALTER TABLE notices_global_index ENABLE KEYS",$dbh);
	pmb_mysql_query("ALTER TABLE notices_mots_global_index ENABLE KEYS",$dbh);
	pmb_mysql_query("ALTER TABLE notices_fields_global_index ENABLE KEYS",$dbh);
}