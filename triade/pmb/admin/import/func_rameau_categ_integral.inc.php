<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_rameau_categ_integral.inc.php,v 1.17 2021/12/09 14:22:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path; //Nécessaire pour certaines inclusions
require_once ($class_path."/import/import_expl_bdp.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categories.class.php");
global $thes, $thesaurus_defaut, $id_rech_theme;


// DEBUT paramétrage propre à la base de données d'importation :

// récupération du 606 : récup en catégories en essayant de classer :
// RECUP intégrale : 

// Attention, dans le multithesaurus, l'identifiant de recherche par terme est
// la racine du thesaurus par defaut

//	les sujets sous le terme "Recherche par terme" 
		$thes = new thesaurus($thesaurus_defaut);
		$id_rech_theme = $thes->num_noeud_racine;
		
//	les précisions géographiques sous le terme "Recherche géographique" 
//		$id_rech_geo = 2 ;
//	les précisions de période sous le terme "Recherche chronologique" 
//		$id_rech_chrono = 3 ;
// FIN paramétrage 

function recup_noticeunimarc_suite($notice) {
} // fin recup_noticeunimarc_suite = fin récupération des variables propres au CNL
	
function import_new_notice_suite() {
	global $notice_id ;
	
	global $info_606_a, $info_606_j, $info_606_x, $info_606_y, $info_606_z ;
	global $id_rech_theme ; 
	global $thesaurus_defaut;
	global $thes;
	
	/* 
	echo "<pre>";
	print_r ($info_949);
	print_r ($info_997);
	echo "</pre>";
	*/
	
	// les champs $606 sont stockés dans les catégories
	//	$a >> en sous catégories de $id_rech_theme
	// 		$j en complément de $a
	//		$x en sous catégories de $a
	// $y >> en sous catégories de $id_rech_geo
	// $z >> en sous catégories de $id_rech_chrono
	// TRAITEMENT :
	// pour $a=0 à size_of $info_606_a
	//	pour $j=0 à size_of $info_606_j[$a]
	//		concaténer $libelle_j .= $info_606_j[$a][$j]
	//	$libelle_final = $info_606_a[0]." ** ".$libelle_j
	//	Rechercher si l'enregistrement existe déjà dans categories = 
	//	$categid = categories::searchLibelle(addslashes($libelle_final), $thesaurus_defaut, 'fr_FR', $id_rech_theme)

	//	Créer si besoin et récupérer l'id $categid_a
	//	$categid_parent =  $categid_a
	//	pour $x=0 à size_of $info_606_x[$a]
	//		Rechercher si l'enregistrement existe déjà dans categories = 
	//	$categid = categories::searchLibelle(addslashes($info_606_x[$a][$x]), $thesaurus_defaut, 'fr_FR', $categ_parent)

	//		Créer si besoin et récupérer l'id $categid_parent
	//
	//	$categid_parent =  $id_rech_geo
	//	pour $y=0 à size_of $info_606_y[$a]
	//		Rechercher si l'enregistrement existe déjà dans categories = 
	//	$categid = categories::searchLibelle(addslashes($info_606_y[$a][$y]), $thesaurus_defaut, 'fr_FR', $categ_parent)

	//		Créer si besoin et récupérer l'id $categid_parent
	//
	//	$categid_parent =  $id_rech_chrono
	//	pour $y=0 à size_of $info_606_z[$a]
	//		Rechercher si l'enregistrement existe déjà dans categories = 
	//	$categid = categories::searchLibelle(addslashes($info_606_z[$a][$y]]), $thesaurus_defaut, 'fr_FR', $categ_parent)

	//		Créer si besoin et récupérer l'id $categid_parent
	//
	$libelle_j="";
	for ($a=0; $a<count($info_606_a); $a++) {
		for ($j=0; $j<count($info_606_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_606_j[$a][$j]) ;
				else $libelle_j .= " ** ".trim($info_606_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_606_a[$a][0]) ;
			else $libelle_final = trim($info_606_a[$a][0])." ** ".$libelle_j ;
		if (!$libelle_final) break ; 
		$res_a = categories::searchLibelle(addslashes($libelle_final), $thesaurus_defaut, 'fr_FR', $id_rech_theme);
		if ($res_a) {
			$categid_a = $res_a;
		} else {
			$categid_a = create_categ($id_rech_theme, $libelle_final, strip_empty_words($libelle_final, 'fr_FR'));
		}
		// récup des sous-categ en cascade sous $a
		$categ_parent =  $categid_a ;
		for ($x=0 ; $x < count($info_606_x[$a]) ; $x++) {
			$res_x = categories::searchLibelle(addslashes(trim($info_606_x[$a][$x])), $thesaurus_defaut, 'fr_FR', $categ_parent);
			if ($res_x) {
				$categ_parent = $res_x;
			} else {
				$categ_parent = create_categ($categ_parent, trim($info_606_x[$a][$x]), strip_empty_words($info_606_x[$a][$x], 'fr_FR'));
			}
		} // fin récup des $x en cascade sous l'id de la catégorie 606$a
		
		if ($categ_parent != $id_rech_theme) {
			// insertion dans la table notices_categories
			$rqt_ajout = "insert into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$categ_parent."' " ;
			pmb_mysql_query($rqt_ajout);
		}
		
		// récup TOUT EN CASCADE
		$id_rech_geo = $categ_parent ;		
		// récup des categ géo à loger sous la categ géo principale
		$categ_parent =  $id_rech_geo ;
		for ($y=0 ; $y < count($info_606_y[$a]) ; $y++) {
			$res_y = categories::searchLibelle(addslashes(trim($info_606_y[$a][$y])), $thesaurus_defaut, 'fr_FR', $categ_parent);
			if($res_y) {
				$categ_parent = $res_y;
			} else {
				$categ_parent = create_categ($categ_parent, trim($info_606_y[$a][$y]), strip_empty_words($info_606_y[$a][$y], 'fr_FR'));
			}
		} // fin récup des $y en cascade sous l'id de la catégorie principale thème géo
		
		if ($categ_parent != $id_rech_geo) {
			// insertion dans la table notices_categories
			$rqt_ajout = "insert into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$categ_parent."' " ;
			pmb_mysql_query($rqt_ajout);
		}

		// récup TOUT EN CASCADE
		$id_rech_chrono = $categ_parent ;		
		// récup des categ chrono à loger sous la categ chrono principale
		$categ_parent =  $id_rech_chrono ;
		for ($z=0 ; $z < count($info_606_z[$a]) ; $z++) {
			$res_z = categories::searchLibelle(addslashes(trim($info_606_z[$a][$z])), $thesaurus_defaut, 'fr_FR', $categ_parent);
			if ($res_z) {
				$categ_parent = $res_z;
			} else {
				$categ_parent = create_categ($categ_parent, trim($info_606_z[$a][$z]), strip_empty_words($info_606_z[$a][$z], 'fr_FR'));
			}
		} // fin récup des $z en cascade sous l'id de la catégorie principale thème chrono
		
		if ($categ_parent != $id_rech_chrono) {
			// insertion dans la table notices_categories
			$rqt_ajout = "insert into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$categ_parent."' " ;
			pmb_mysql_query($rqt_ajout);
		}
	}
	
} // fin import_new_notice_suite
			
			
function create_categ($num_parent, $libelle, $index) {
	
	global $thes;
	$n = new noeuds();
	$n->num_thesaurus = $thes->id_thesaurus;
	$n->num_parent = $num_parent;
	$n->save();
	
	$c = new categories($n->id_noeud, 'fr_FR');
	$c->libelle_categorie = $libelle;
	$c->index_categorie = $index;
	$c->save();
	
	return $n->id_noeud;
}			
			
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('rameau_categ_integral');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}