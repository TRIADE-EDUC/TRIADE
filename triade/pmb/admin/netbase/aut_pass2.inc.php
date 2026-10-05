<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_pass2.inc.php,v 1.20 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $v_state, $spec, $start;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_renvoi_auteurs"]);

pmb_mysql_query("update authors A1 left join authors A2 on A1.author_see=A2.author_id set A1.author_see=0 where A2.author_id is null");
$affected += pmb_mysql_affected_rows();

$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_auteurs"], $affected." ".$msg["nettoyage_res_suppr_auteurs"]);

pmb_mysql_query('OPTIMIZE TABLE authors');
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, $affected, '2');
