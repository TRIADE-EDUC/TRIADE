<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_docnum.inc.php,v 1.5 2017-11-22 11:07:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once("$class_path/indexation_docnum.class.php");


// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (!isset($start)) $start=0;

$v_state=urldecode($v_state);

if (!$count) {
	$explnum = pmb_mysql_query("SELECT count(1) FROM explnum", $dbh);
	$count = pmb_mysql_result($explnum, 0, 0);
}

print "<br /><br /><h2 class='center'>".htmlentities($msg["docnum_reindexation"], ENT_QUOTES, $charset)."</h2>";

$requete = "select explnum_id as id from explnum order by id LIMIT $start, $lot";
$res_explnum = pmb_mysql_query($requete,$dbh);
if(pmb_mysql_num_rows($res_explnum)) {
	print netbase::get_display_progress($start, $count);
	
	while(($explnum = pmb_mysql_fetch_object($res_explnum))){
		
		$index = new indexation_docnum($explnum->id);
		$index->indexer();
	}
	
	$next = $start + $lot;
	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - INDEX_DOCNUM;
	$not = pmb_mysql_query("SELECT count(1) FROM explnum", $dbh);
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg['docnum_reindexation'], ENT_QUOTES, $charset)." : ";
	$v_state .= $compte." ".htmlentities($msg['docnum_reindex_expl'], ENT_QUOTES, $charset);
	print netbase::get_process_state_form($v_state, $spec);
}

?>