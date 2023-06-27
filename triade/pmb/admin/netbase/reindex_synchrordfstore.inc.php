<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_synchrordfstore.inc.php,v 1.7 2017-11-22 11:07:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/synchro_rdf.class.php');
require_once($class_path.'/notice.class.php');
require_once($class_path.'/serials.class.php');

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

$synchro_rdf = new synchro_rdf();

// initialisation de la borne de départ
if (empty($start)) {
	$start=0;
	//remise a zero des tables de synchro rdf
	$synchro_rdf->truncateStore();
}

$v_state=urldecode($v_state);

if (!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
	$count = pmb_mysql_result($notices, 0, 0);
}
	
print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_synchrordfstore_reindexation"], ENT_QUOTES, $charset)."</h2>";

$NoIndex = 1;

$query = pmb_mysql_query("select notice_id from notices order by notice_id LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {
	print netbase::get_display_progress($start, $count);
	
	while($mesNotices = pmb_mysql_fetch_assoc($query)) {		
		$synchro_rdf->addRdf($mesNotices['notice_id'],0); 
		$notice=new notice($mesNotices['notice_id']);
		$niveauB=strtolower($notice->biblio_level);
		//Si c'est un article, il faut réindexer son bulletin
		if($niveauB=='a'){
			$bulletin=analysis::getBulletinIdFromAnalysisId($mesNotices['notice_id']);
			$synchro_rdf->addRdf(0,$bulletin);
		}
	}
	pmb_mysql_free_result($query);

	$next = $start + $lot;
	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - INDEX_SYNCHRORDFSTORE;
	$compte=0;
	$q ="SELECT *
			WHERE {
			   FILTER (!regex(?p, rdf:type,'i')) .
			   ?s ?p ?o
			}";
	$r = $synchro_rdf->store->query($q);
	if (is_array($r['result']['rows'])) {
		$compte=count($r['result']['rows']);
	}
	$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_synchrordfstore_reindexation"], ENT_QUOTES, $charset)." :";
	$v_state .= $compte." ".htmlentities($msg["nettoyage_synchrordfstore_reindex_total"], ENT_QUOTES, $charset);
	print netbase::get_process_state_form($v_state, $spec);
}