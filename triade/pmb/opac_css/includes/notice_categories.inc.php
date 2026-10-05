<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_categories.inc.php,v 1.11.6.1.2.1 2025/01/30 09:08:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once("$class_path/marc_table.class.php");
// récupération des categories d'une notice

// get_notice_categories : retourne un tableau avec les categories d'une notice donnée
function get_notice_categories($notice=0) {
	$categories = array() ;
	
	$rqt = "SELECT noeuds.id_noeud as categ_id, noeuds.num_parent as categ_parent, noeuds.num_renvoi_voir as categ_see ";
	$rqt.= "FROM notices_categories, noeuds ";
	$rqt.= "WHERE notices_categories.notcateg_notice='$notice' ";
	$rqt.= "AND notices_categories.num_noeud=noeuds.id_noeud ";
	$rqt.= "ORDER BY ordre_categorie";

	$res_sql = pmb_mysql_query($rqt);
	while ($notice=pmb_mysql_fetch_object($res_sql)) {
		$categ = authorities_collection::get_authority(AUT_TABLE_CATEG, $notice->categ_id);
		$categories[] = array( 
				'categ_id' => $notice->categ_id,
				'categ_parent' => $notice->categ_parent,
				'categ_see' => $notice->categ_see,
				'categ_libelle' => $categ->libelle,
				'categ_parent_libelle' => $categ->parent_libelle
				) ;
		}
	return $categories;
}

// get_notice_langues : retourne un tableau avec les langues d'une notice donnée
function get_notice_langues($notice=0, $quelle_langues=0) {
	global $marc_liste_langues ;
	if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');

	$langues = array() ;
	$rqt = "select code_langue from notices_langues where num_notice='$notice' and type_langue=$quelle_langues order by ordre_langue ";
	$res_sql = pmb_mysql_query($rqt);
	while ($notice=pmb_mysql_fetch_object($res_sql)) {
		if ($notice->code_langue)
			$langues[] = array( 
				'lang_code' => $notice->code_langue,
				'langue' => $marc_liste_langues->table[$notice->code_langue]
				) ;
		}
	return $langues;
}

function construit_liste_langues($tableau) {
	$langues = "";
	if (is_countable($tableau)) {
		for ($i = 0 ; $i < sizeof($tableau) ; $i++) {
			if ($langues) {
				$langues.=" ";
			}
			$langues .= $tableau[$i]["langue"]." (<i>".$tableau[$i]["lang_code"]."</i>)";
		}
	}
	return $langues;
}
	
