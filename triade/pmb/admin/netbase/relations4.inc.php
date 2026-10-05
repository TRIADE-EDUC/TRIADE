<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relations4.inc.php,v 1.18 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $start, $v_state, $spec;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_clean_relations_cat2"]);

$affected = 0;

pmb_mysql_query("delete notices_global_index from notices_global_index left join notices on num_notice=notice_id where notice_id is null");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("delete notices_mots_global_index from notices_mots_global_index left join notices on id_notice=notice_id where notice_id is null");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("delete audit from audit left join notices on object_id=notice_id where notice_id is null and type_obj=1");
$affected += pmb_mysql_affected_rows();

$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_relations"], $affected." ".$msg["nettoyage_res_suppr_relations_cat2"]);
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '5');

