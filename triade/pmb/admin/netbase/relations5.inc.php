<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relations5.inc.php,v 1.20 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $start, $v_state, $spec;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_clean_relations_pan2"]);

$query = pmb_mysql_query("delete caddie_content from caddie join caddie_content on (idcaddie=caddie_id and type='EXPL') left join exemplaires on object_id=expl_id where expl_id is null");
$affected = pmb_mysql_affected_rows();
$query = pmb_mysql_query("delete explnum from explnum left join notices on notice_id=explnum_notice where notice_id is null and explnum_bulletin=0");
$affected = pmb_mysql_affected_rows();
$query = pmb_mysql_query("delete explnum from explnum left join bulletins on bulletin_id=explnum_bulletin where bulletin_id is null and explnum_notice=0 ");
$affected = pmb_mysql_affected_rows();
$query = pmb_mysql_query("delete acces_res_1 from acces_res_1 left join notices on res_num=notice_id where notice_id is null ");
if($query) $affected = pmb_mysql_affected_rows();

$query = pmb_mysql_query("delete acces_res_2 from acces_res_2 left join notices on res_num=notice_id where notice_id is null ");
if($query) $affected = pmb_mysql_affected_rows();

$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_relations"], $affected." ".$msg["nettoyage_res_suppr_relations_pan2"]);

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '6');
