<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titres_uniformes.inc.php,v 1.9 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $start, $v_state, $spec;

require_once("$class_path/titre_uniforme.class.php");

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_suppr_titres_uniformes"]);

$query = pmb_mysql_query("SELECT tu_id from titres_uniformes left join notices_titres_uniformes on ntu_num_tu=tu_id where ntu_num_tu is null");
$affected=0;
if(pmb_mysql_num_rows($query)){
	while ($ligne = pmb_mysql_fetch_object($query)) {
		$tu = new titre_uniforme($ligne->tu_id);
		$deleted = $tu->delete();
		if(!$deleted) {
			$affected++;
		}
	}
}

//Nettoyage des informations d'autorités pour les titres uniformes
titre_uniforme::delete_autority_sources();

$query = pmb_mysql_query("delete notices_titres_uniformes from notices_titres_uniformes left join titres_uniformes on ntu_num_tu=tu_id where tu_id is null");

$spec = $spec - CLEAN_TITRES_UNIFORMES;
$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_titres_uniformes"], $affected." ".$msg["nettoyage_res_suppr_titres_uniformes"]);

pmb_mysql_query('OPTIMIZE TABLE titres_uniformes');
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);
		
