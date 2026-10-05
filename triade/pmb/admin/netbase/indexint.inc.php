<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.inc.php,v 1.8 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $start, $v_state, $spec;

require_once("$class_path/indexint.class.php");

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_suppr_indexint"]);

$query = pmb_mysql_query("SELECT indexint_id from indexint left join notices on indexint=indexint_id where notice_id is null");
$affected=0;
if(pmb_mysql_num_rows($query)){
	while ($ligne = pmb_mysql_fetch_object($query)) {
		$indexint = new indexint($ligne->indexint_id);
		$deleted = $indexint->delete();
		if(!$deleted) {
			$affected++;
		}
	}
}

$query = pmb_mysql_query("update notices left join indexint ON indexint=indexint_id SET indexint=0 WHERE indexint_id is null");

$spec = $spec - CLEAN_INDEXINT;
$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_indexint"], $affected." ".$msg["nettoyage_res_suppr_indexint"]);

pmb_mysql_query('OPTIMIZE TABLE indexint');
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);
		
