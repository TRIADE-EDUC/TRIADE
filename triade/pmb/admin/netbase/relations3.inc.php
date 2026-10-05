<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relations3.inc.php,v 1.15 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg, $charset;
global $start, $v_state, $spec;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_clean_relations_pan"]);

$affected = 0;
pmb_mysql_query("DELETE notices_custom_values FROM notices_custom_values LEFT JOIN notices ON notice_id=notices_custom_origine WHERE notice_id IS NULL ");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("delete notices from notices left join bulletins on num_notice=notice_id where num_notice is null and niveau_hierar='2' and niveau_biblio='b' ");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("delete notices_titres_uniformes from notices_titres_uniformes left join notices on ntu_num_notice=notice_id where notice_id is null ");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("delete notices_categories from notices_categories left join notices on notcateg_notice=notice_id where notice_id is null");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("delete responsability from responsability left join notices on responsability_notice=notice_id where notice_id is null");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("delete responsability from responsability left join authors on responsability_author=author_id where author_id is null");
$affected += pmb_mysql_affected_rows();

$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_relations"], $affected." ".$msg["nettoyage_res_suppr_relations_pan"]);
pmb_mysql_query('OPTIMIZE TABLE notices_categories');
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '4');
