<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_signature_notice.inc.php,v 1.9 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset;
global $start, $v_state, $spec, $count;

require_once($class_path."/notice_doublon.class.php");

$sign= new notice_doublon();

// la taille d'un paquet de notices
$lot = NOEXPL_PAQUET_SIZE*10; // defini dans ./params.inc.php

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

if(!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM notices");
	$count = pmb_mysql_result($notices, 0, 0);
}

print netbase::get_display_progress_title($msg["gen_signature_notice"]);

$query = pmb_mysql_query("SELECT notice_id FROM notices LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {
	print netbase::get_display_progress($start, $count);
   	while ($row = pmb_mysql_fetch_row($query) )  { 		
   		$val= $sign->gen_signature($row[0]);
		pmb_mysql_query("update notices set signature='$val', update_date=update_date where notice_id=".$row[0]);		
   	}
   	pmb_mysql_free_result($query);
	$next = $start + $lot;
 	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - GEN_SIGNATURE_NOTICE;
	$v_state .= netbase::get_display_progress_v_state($msg["gen_signature_notice_status"], $count." ".$msg["gen_signature_notice_status_end"]);
	pmb_mysql_query('OPTIMIZE TABLE notices');
	// mise à jour de l'affichage de la jauge
	print netbase::get_display_final_progress();

	print netbase::get_process_state_form($v_state, $spec);
}	