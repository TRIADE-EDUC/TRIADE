<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relations9.inc.php,v 1.8 2024/04/17 13:55:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $start, $v_state, $spec;

require_once($class_path."/notice_relations.class.php");

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);
print netbase::get_display_progress_title($msg["nettoyage_update_relations"]);

$affected = notice_relations::upgrade_notices_relations_table();

$spec = $spec - CLEAN_RELATIONS;
$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_relations"], $affected." ".$msg["nettoyage_res_update_relations"]);

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);
