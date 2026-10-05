<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: setup_initialization.inc.php,v 1.3 2021/12/23 08:02:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once($class_path."/aut_link.class.php");
require_once($class_path."/indexation_authority.class.php");
require_once($class_path."/emprunteur.class.php");
require_once($class_path."/notice.class.php");

function pmb_indexation_display($title="", $message="") {
	global $charset;

	echo "<tr><td>".htmlentities($title, ENT_QUOTES, $charset)."</td><td>".htmlentities($message, ENT_QUOTES, $charset)."</td></tr>";
	flush();
	ob_flush();
}

function pmb_init_indexation_authorities() {
	global $msg;
	global $include_path;
	
	// => Authors
	$result = pmb_mysql_query("SELECT author_id as id from authors");
	if($result) {
		$count = pmb_mysql_num_rows($result);
		if($count) {
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/authors/champs_base.xml", "authorities", AUT_TABLE_AUTHORS);
			while($row = pmb_mysql_fetch_object($result)) {
				$indexation_authority->maj($row->id);
			}
			pmb_indexation_display($msg["nettoyage_reindex_authors"], $count." ".$msg["nettoyage_res_reindex_authors"]);
		}
	}
	
	// => Publishers
	$result = pmb_mysql_query("SELECT ed_id as id from publishers");
	if($result) {
		$count = pmb_mysql_num_rows($result);
		if($count) {
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/publishers/champs_base.xml", "authorities", AUT_TABLE_PUBLISHERS);
			while($row = pmb_mysql_fetch_object($result)) {
				$indexation_authority->maj($row->id);
			}
			pmb_indexation_display($msg["nettoyage_reindex_publishers"], $count." ".$msg["nettoyage_res_reindex_publishers"]);
		}
	}
	
	// => Categories
	$result = pmb_mysql_query("select distinct num_noeud as id from categories");
	if($result) {
		$count = pmb_mysql_num_rows($result);
		if($count) {
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/categories/champs_base.xml", "authorities", AUT_TABLE_CATEG);
			while($row = pmb_mysql_fetch_object($result)) {
				$indexation_authority->maj($row->id);
			}
			pmb_indexation_display($msg["nettoyage_reindex_categories"], $count." ".$msg["nettoyage_res_reindex_categories"]);
		}
	}
	
	// => Collections
	$result = pmb_mysql_query("SELECT collection_id as id from collections");
	if($result) {
		$count = pmb_mysql_num_rows($result);
		if($count) {
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/collections/champs_base.xml", "authorities", AUT_TABLE_COLLECTIONS);
			while($row = pmb_mysql_fetch_object($result)) {
				$indexation_authority->maj($row->id);
			}
			pmb_indexation_display($msg["nettoyage_reindex_collections"], $count." ".$msg["nettoyage_res_reindex_collections"]);
		}
	}
	
	// => Sous collections
	$result = pmb_mysql_query("SELECT sub_coll_id as id from sub_collections");
	if($result) {
		$count = pmb_mysql_num_rows($result);
		if($count) {
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/subcollections/champs_base.xml", "authorities", AUT_TABLE_SUB_COLLECTIONS);
			while($row = pmb_mysql_fetch_object($result)) {
				$indexation_authority->maj($row->id);
			}
			pmb_indexation_display($msg["nettoyage_reindex_sub_collections"], $count." ".$msg["nettoyage_res_reindex_sub_collections"]);
		}
	}
	
	// => Séries
	$result = pmb_mysql_query("SELECT serie_id as id from series");
	if($result) {
		$count = pmb_mysql_num_rows($result);
		if($count) {
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/series/champs_base.xml", "authorities", AUT_TABLE_SERIES);
			while($row = pmb_mysql_fetch_object($result)) {
				$indexation_authority->maj($row->id);
			}
			pmb_indexation_display($msg["nettoyage_reindex_series"], $count." ".$msg["nettoyage_res_reindex_series"]);
		}
	}
	
	// => Index. Décimales
	$result = pmb_mysql_query("SELECT indexint_id as id from indexint");
	if($result) {
		$count = pmb_mysql_num_rows($result);
		if($count) {
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/indexint/champs_base.xml", "authorities", AUT_TABLE_INDEXINT);
			while($row = pmb_mysql_fetch_object($result)) {
				$indexation_authority->maj($row->id);
			}
			pmb_indexation_display($msg["nettoyage_reindex_indexint"], $count." ".$msg["nettoyage_res_reindex_indexint"]);
		}
	}
	
	// => Titres uniformes
	$result = pmb_mysql_query("SELECT tu_id as id from titres_uniformes");
	if($result) {
		$count = pmb_mysql_num_rows($result);
		if($count) {
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/titres_uniformes/champs_base.xml", "authorities", AUT_TABLE_TITRES_UNIFORMES);
			while($row = pmb_mysql_fetch_object($result)) {
				$indexation_authority->maj($row->id);
			}
			pmb_indexation_display($msg["nettoyage_reindex_titres_uniformes"], $count." ".$msg["nettoyage_res_reindex_titres_uniformes"]);
		}
	}
}

function pmb_init_indexation_records() {
	global $msg;
	
	$result = pmb_mysql_query("select notice_id from notices");
	if($result) {
		$count = pmb_mysql_num_rows($result);
		if($count) {
			while($mesNotices = pmb_mysql_fetch_assoc($result)) {
				// permet de charger la bonne langue, mot vide...
				$info=notice::indexation_prepare($mesNotices['notice_id']);
				// Mise à jour de la table "notices_global_index"
				notice::majNoticesGlobalIndex($mesNotices['notice_id']);
				// Mise à jour de la table "notices_mots_global_index"
				notice::majNoticesMotsGlobalIndex($mesNotices['notice_id']);
				// restaure l'environnement de langue
				notice::indexation_restaure($info);
			}
			pmb_indexation_display($msg["nettoyage_reindex_notices"], $count." ".$msg["nettoyage_res_reindex_notices"]);
		}
	}
}

function pmb_init_hash_passwords() {
	global $msg;
	
	//Encodage des mots de passe lecteurs
	 $result = pmb_mysql_query("SELECT id_empr, empr_password, empr_login FROM empr where empr_password_is_encrypted=0");
	 if($result) {
	 	$count = pmb_mysql_num_rows($result);
	 	if($count) {
	 		while($row = pmb_mysql_fetch_object($result)) {
	 			emprunteur::update_digest($row->empr_login,$row->empr_password);
	 			emprunteur::hash_password($row->empr_login,$row->empr_password);
	 		}
	 		pmb_indexation_display($msg["hash_empr_password_status"], $count." ".$msg["hash_empr_password_status_end"]);
	 	}
	 }
}

echo "<table>";

// Authorities indexation
pmb_init_indexation_authorities();

// Records indexation
pmb_init_indexation_records();

// Readers encoding passwords
pmb_init_hash_passwords();

pmb_mysql_query("update parametres set valeur_param='0' where type_param='pmb' and sstype_param='indexation_must_be_initialized'");
echo "</table>";