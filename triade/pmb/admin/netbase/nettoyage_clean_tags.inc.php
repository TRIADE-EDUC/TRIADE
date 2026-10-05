<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nettoyage_clean_tags.inc.php,v 1.8 2024/04/17 13:55:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset;
global $start, $v_state, $spec, $count;

require_once($class_path."/notice.class.php");

// la taille d'un paquet de notices
$lot = NOEXPL_PAQUET_SIZE*10; // defini dans ./params.inc.php

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

if(!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM notices");
	$count = pmb_mysql_result($notices, 0, 0);
}

print netbase::get_display_progress_title($msg["nettoyage_clean_tags"]);

$query = pmb_mysql_query("SELECT notice_id FROM notices LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {
	print netbase::get_display_progress($start, $count);
   	while ($row = pmb_mysql_fetch_row($query) )  { 		
		notice::majNotices_clean_tags($row[0]);
   	}
   	pmb_mysql_free_result($query);
	$next = $start + $lot;
 	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - NETTOYAGE_CLEAN_TAGS;
	$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_clean_tags_status"], ENT_QUOTES, $charset);
	$v_state .= $count." ".htmlentities($msg["nettoyage_clean_tags_status_end"], ENT_QUOTES, $charset);
	pmb_mysql_query('OPTIMIZE TABLE notices');
	// mise à jour de l'affichage de la jauge
	print netbase::get_display_final_progress();

	print netbase::get_process_state_form($v_state, $spec);
}	