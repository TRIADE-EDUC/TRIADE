<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_pass4.inc.php,v 1.16 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $v_state, $spec, $start, $count;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

if(!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM responsability where responsability_author<>0 ");
	$count = pmb_mysql_result($notices, 0, 0) ;
}

print netbase::get_display_progress_title($msg["nettoyage_responsabilites"]." : 2");

pmb_mysql_query("delete responsability from responsability left join authors on responsability_author=author_id where author_id is null ");
$affected = pmb_mysql_affected_rows();

$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_responsabilites"], $affected." ".$msg["nettoyage_res_responsabilites"]);

pmb_mysql_query('OPTIMIZE TABLE authors');
// mise à jour de l'affichage de la jauge
$spec = $spec - CLEAN_AUTHORS;
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, $affected, '0');
