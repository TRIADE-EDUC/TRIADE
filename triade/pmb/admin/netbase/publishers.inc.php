<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publishers.inc.php,v 1.22 2024/04/17 13:55:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $start, $v_state, $spec;

require_once("$class_path/editor.class.php");

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_suppr_editeurs"]);

$requete="SELECT DISTINCT ed_id FROM publishers LEFT JOIN notices n1 ON n1.ed1_id=ed_id LEFT JOIN notices n2 ON n2.ed2_id=ed_id LEFT JOIN collections ON ed_id=collection_parent WHERE n1.notice_id IS NULL AND  n2.notice_id IS NULL AND collection_id IS NULL";
$res=pmb_mysql_query($requete);
$affected=0;
if(pmb_mysql_num_rows($res)){
	while ($ligne = pmb_mysql_fetch_object($res)) {
		$editeur = new editeur($ligne->ed_id);
		$deleted = $editeur->delete();
		if(!$deleted) {
			$affected++;
		}
	}
}

$spec = $spec - CLEAN_PUBLISHERS;
$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_editeurs"], $affected." ".$msg["nettoyage_res_suppr_editeurs"]);

pmb_mysql_query('OPTIMIZE TABLE publishers');
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);
