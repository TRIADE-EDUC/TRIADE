<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: series.inc.php,v 1.20 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $start, $v_state, $spec;

require_once("$class_path/serie.class.php");

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_suppr_series"]);

$query = pmb_mysql_query("SELECT serie_id from series left join notices on tparent_id=serie_id where tparent_id is null");
$affected=0;
if(pmb_mysql_num_rows($query)){
	while ($ligne = pmb_mysql_fetch_object($query)) {
		$serie = new serie($ligne->serie_id);
		$deleted = $serie->delete();
		if(!$deleted) {
			$affected++;
		}
	}
}

$query = pmb_mysql_query("update notices left join series on tparent_id=serie_id set tparent_id=0 where serie_id is null");

$spec = $spec - CLEAN_SERIES;
$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_series"], $affected." ".$msg["nettoyage_res_suppr_series"]);

pmb_mysql_query('OPTIMIZE TABLE series');
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);
		
