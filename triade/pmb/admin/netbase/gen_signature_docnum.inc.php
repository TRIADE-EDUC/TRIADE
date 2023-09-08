<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_signature_docnum.inc.php,v 1.7 2017-11-22 11:07:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/explnum.class.php");

// la taille d'un paquet de documents numériques
$lot = NOEXPL_PAQUET_SIZE*1; // defini dans ./params.inc.php

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

if(!$count) {
	$explnums = pmb_mysql_query("SELECT count(1) FROM explnum", $dbh);
	$count = pmb_mysql_result($explnums, 0, 0);
}

print "<br /><br /><h2 class='center'>".htmlentities($msg["gen_signature_docnum"], ENT_QUOTES, $charset)."</h2>";

$result = pmb_mysql_query("SELECT explnum_id FROM explnum LIMIT ".$start.", ".$lot);
if(pmb_mysql_num_rows($result)) {
	print netbase::get_display_progress($start, $count);
   	while ($row = pmb_mysql_fetch_object($result) )  {
   		$explnum = new explnum($row->explnum_id);
		pmb_mysql_query("update explnum set explnum_signature='".$explnum->gen_signature()."' where explnum_id=".$row->explnum_id, $dbh);		
   	}
   	pmb_mysql_free_result($result);
	$next = $start + $lot;
 	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - GEN_SIGNATURE_DOCNUM;
	$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace='3'>".htmlentities($msg["gen_signature_docnum_status"], ENT_QUOTES, $charset);
	$v_state .= $count." ".htmlentities($msg["gen_signature_docnum_status_end"], ENT_QUOTES, $charset);
	$opt = pmb_mysql_query('OPTIMIZE TABLE explnum');
	// mise à jour de l'affichage de la jauge
	print netbase::get_display_final_progress();

	print netbase::get_process_state_form($v_state, $spec);
}