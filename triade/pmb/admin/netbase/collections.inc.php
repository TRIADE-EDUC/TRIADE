<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collections.inc.php,v 1.18 2024/04/17 13:55:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $v_state, $spec, $start;

require_once("$class_path/collection.class.php");

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_suppr_collections"]);

$query = pmb_mysql_query("SELECT collection_id from collections left join notices on collection_id=coll_id left join sub_collections on sub_coll_parent=collection_id where coll_id is null and sub_coll_parent is null ");
$affected=0;
if(pmb_mysql_num_rows($query)){
	while ($ligne = pmb_mysql_fetch_object($query)) {
		$coll = new collection($ligne->collection_id);
		$deleted = $coll->delete();
		if(!$deleted) {
			$affected++;
		}
	}
}

//Nettoyage des informations d'autorités pour les collections
collection::delete_autority_sources();

$query = pmb_mysql_query("update notices left join collections ON collection_id=coll_id SET coll_id=0, subcoll_id=0 WHERE collection_id is null");

$spec = $spec - CLEAN_COLLECTIONS;
$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_collections"], $affected." ".$msg["nettoyage_res_suppr_collections"]);

pmb_mysql_query('OPTIMIZE TABLE collections');
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);
