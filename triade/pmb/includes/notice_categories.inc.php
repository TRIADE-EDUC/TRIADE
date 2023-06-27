<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_categories.inc.php,v 1.20 2019-05-31 11:47:50 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/notice.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/notice_relations.class.php");

// récupération des categories d'une notice

// get_notice_categories : retourne un tableau avec les categories d'une notice donnée
function get_notice_categories($notice=0) {
	global $dbh;
	$categories = array() ;

	$rqt = "SELECT noeuds.id_noeud as categ_id, noeuds.num_parent as categ_parent, noeuds.num_renvoi_voir as categ_see ";
	$rqt.= "FROM notices_categories, noeuds ";
	$rqt.= "WHERE notices_categories.notcateg_notice='$notice' ";
	$rqt.= "AND notices_categories.num_noeud=noeuds.id_noeud ";
	$rqt.= "ORDER BY ordre_categorie";
	//$rqt.= "ORDER BY num_thesaurus, ordre_categorie";

	$res_sql = pmb_mysql_query($rqt, $dbh);
	while ($notice=pmb_mysql_fetch_object($res_sql)) {
		$categ = authorities_collection::get_authority(AUT_TABLE_CATEG, $notice->categ_id);
		$categories[] = array( 
				'categ_id' => $notice->categ_id,
				'categ_parent' => $notice->categ_parent,
				'categ_see' => $notice->categ_see,
				'categ_libelle' => $categ->catalog_form
				) ;
		}
	return $categories;
}
	
function update_notice_categories_from_form($id_notice=0, $id_bulletin=0) {
	global $dbh;
	global $f_nb_categ;
	if (!$id_notice && $id_bulletin) {
		$query = "select * from bulletins where bulletin_id=".$id_bulletin;
		$result = pmb_mysql_query($query,$dbh);
		if ($result) {
			$row = pmb_mysql_fetch_object($result);
			if ($row->num_notice) {
				$id_notice = $row->num_notice; 
			} else {
				//on crée la notice de bulletin
				global $xmlta_doctype_bulletin,$deflt_notice_statut;
				pmb_mysql_query("INSERT INTO notices SET 
					tit1 = '".$row->bulletin_numero.($row->mention_date ?" - ".$row->mention_date:"").($row->bulletin_titre?" - ".$row->bulletin_titre:"")."',
					statut = '".$deflt_notice_statut."',		
					typdoc = '".$xmlta_doctype_bulletin."',
					create_date=sysdate(), update_date=sysdate() ", $dbh);
				$id_notice = pmb_mysql_insert_id($dbh);
				// Mise à jour des index de la notice
				notice::majNoticesTotal($id_notice);
				audit::insert_creation (AUDIT_NOTICE, $id_notice) ;
				
				//Mise à jour du bulletin
				$requete="update bulletins set num_notice=".$id_notice." where bulletin_id=".$id_bulletin;
				pmb_mysql_query($requete);
				//Mise à jour des liens bulletin -> notice mère
				notice_relations::insert($id_notice, $row->bulletin_notice, 'b', 1, 'up', false);
			}
		}
	}
	if (!$id_notice) return;
	
	$query = "SELECT max(ordre_categorie) as ordre FROM notices_categories WHERE notcateg_notice='".$id_notice."' ";
	$result = pmb_mysql_query($query);
	$ordre_categ = 0;
	if ($result) {
		$row = pmb_mysql_fetch_object($result);
		if (isset($row->ordre)) {
			$ordre_categ = $row->ordre;
		}
	}
	if ($f_nb_categ) {
		$rqt_ins = "INSERT INTO notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ";
		for ($i=0; $i< $f_nb_categ; $i++) {
			$var_categ = "f_categ$i" ;
			global ${$var_categ};
			if (${$var_categ}) {
				$var_categid = "f_categ_id$i" ;
				global ${$var_categid};
				$rqt_sel = "SELECT notcateg_notice FROM notices_categories WHERE notcateg_notice='".$id_notice."' and num_noeud='".${$var_categid}."' ";
				$res_sel = pmb_mysql_query($rqt_sel, $dbh);
				if ($res_sel && !pmb_mysql_num_rows($res_sel)) {
					$ordre_categ++;
					$rqt = $rqt_ins . " ('".$id_notice."','".${$var_categid}."',$ordre_categ) " ;
					$res_ins = @pmb_mysql_query($rqt, $dbh);
				}
			}
		}
	}
}

require_once("$class_path/marc_table.class.php");
// get_notice_langues : retourne un tableau avec les langues d'une notice donnée
function get_notice_langues($notice=0, $quelle_langues=0) {
	global $dbh;

	global $marc_liste_langues ;
	if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');

	$langues = array() ;
	$rqt = "select code_langue from notices_langues where num_notice='$notice' and type_langue=$quelle_langues order by ordre_langue ";
	$res_sql = pmb_mysql_query($rqt, $dbh);
	while ($notice=pmb_mysql_fetch_object($res_sql)) {
	    if ($notice->code_langue && isset($marc_liste_langues->table[$notice->code_langue])) {
			$langues[] = array( 
				'lang_code' => $notice->code_langue,
				'langue' => $marc_liste_langues->table[$notice->code_langue]
				);
	    }
	}
	return $langues;
}

function construit_liste_langues($tableau) {
	$langues = "";
	for ($i = 0 ; $i < sizeof($tableau) ; $i++) {
		if ($langues) $langues.=" ";
		$langues .= $tableau[$i]["langue"]." (<i>".$tableau[$i]["lang_code"]."</i>)";
		}
	return $langues;
}

