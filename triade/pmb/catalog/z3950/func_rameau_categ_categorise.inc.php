<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_rameau_categ_categorise.inc.php,v 1.15 2023/10/11 10:09:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// enregistrement de la notices dans les catégories
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categories.class.php");
global $thes, $thesaurus_defaut;

//Attention, dans le multithesaurus, le thesaurus dans lequel on importe est le thesaurus par defaut
$thes = new thesaurus($thesaurus_defaut);
$rac = $thes->num_noeud_racine;
// DEBUT paramétrage propre à la base de données d'importation :
// récupération du 606 : récup en catégories en essayant de classer :
//	les sujets sous le terme "Recherche par terme" 
	$id_rech_theme = categories::searchLibelle('Recherche par terme', $thesaurus_defaut, 'fr_FR');
    if (!$id_rech_theme) $id_rech_theme = create_categ_z3950($rac, 'Recherche par terme', strip_empty_words('Recherche par terme', 'fr_FR'));
		
//	les précisions géographiques sous le terme "Recherche géographique" 
	$id_rech_geo = categories::searchLibelle('Recherche géographique', $thesaurus_defaut, 'fr_FR');
    if (!$id_rech_geo) 	$id_rech_geo = create_categ_z3950($rac, 'Recherche géographique', strip_empty_words('Recherche géographique', 'fr_FR'));

//	les précisions de période sous le terme "Recherche chronologique" 
	$id_rech_chrono = categories::searchLibelle('Recherche chronologique', $thesaurus_defaut, 'fr_FR');
    if (!$id_rech_chrono) $id_rech_chrono = create_categ_z3950($rac, 'Recherche chronologique', strip_empty_words('Recherche chronologique', 'fr_FR'));

// FIN paramétrage 

function traite_categories_enreg($notice_retour, $categories, $thesaurus_traite = 0) {
	z3950_notice::traite_categories_enreg($notice_retour, $categories, $thesaurus_traite);
}

function traite_categories_for_form($tableau_600 = array(), $tableau_601 = array(), $tableau_602 = array(), $tableau_605 = array(), $tableau_606 = array(), $tableau_607 = array(), $tableau_608 = array()) {
	
	global $charset, $pmb_keyword_sep, $rameau;
	$info_606_a = $tableau_606["info_606_a"] ;
	$info_606_j = $tableau_606["info_606_j"] ;
	$info_606_x = $tableau_606["info_606_x"] ;
	$info_606_y = $tableau_606["info_606_y"] ;
	$info_606_z = $tableau_606["info_606_z"] ;
	
	$champ_rameau="";
	for ($a=0; $a<count($info_606_a); $a++) {
		$libelle_final="";
		$libelle_j="";
		for ($j=0; $j<count($info_606_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_606_j[$a][$j]) ;
				else $libelle_j .= " $pmb_keyword_sep ".trim($info_606_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_606_a[$a][0]) ; else $libelle_final = trim($info_606_a[$a][0])." $pmb_keyword_sep ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<count($info_606_x[$a]); $j++) {
			$libelle_final .= " $pmb_keyword_sep ".trim($info_606_x[$a][$j]) ;
		}
		for ($j=0; $j<count($info_606_y[$a]); $j++) {
			$libelle_final .= " $pmb_keyword_sep ".trim($info_606_y[$a][$j]) ;
		}
		for ($j=0; $j<count($info_606_z[$a]); $j++) {
			$libelle_final .= " $pmb_keyword_sep ".trim($info_606_z[$a][$j]) ;
		}
		if ($champ_rameau) $champ_rameau.=" $pmb_keyword_sep ";
		$champ_rameau.=$libelle_final;
	} 

	$rameau_form = serialize($tableau_606) ;
	
	// $rameau est la variable traitée par la fonction traite_categories_from_form, 
	// $rameau est normalement POSTée, afin de pouvoir être traitée en lot, donc hors 
	// formulaire, il faut l'affecter.
	$rameau = addslashes(serialize($tableau_606)) ;

	return array(
		"form" => "<input type='hidden' name='rameau' value='".htmlentities($rameau_form,ENT_QUOTES,$charset)."' />",
		"message" => "Rameau sera int&eacute;gr&eacute; sous forme d'arborescence r&eacute;partie Recherche par terme \$a \$x Recherche g&eacute;ographique \$y Recherche chronologique \$z : <b>".htmlentities($champ_rameau,ENT_QUOTES,$charset)."</b>"
	);
}


function traite_categories_from_form() {
		
	global $rameau ;
	global $dbh;
	global $thes;
	global $id_rech_geo, $id_rech_chrono, $id_rech_theme ;
	
	$tableau_606 = unserialize(stripslashes($rameau)) ;
	
	$info_606_a = $tableau_606["info_606_a"] ;
	$info_606_j = $tableau_606["info_606_j"] ;
	$info_606_x = $tableau_606["info_606_x"] ;
	$info_606_y = $tableau_606["info_606_y"] ;
	$info_606_z = $tableau_606["info_606_z"] ;
	
	// ici récupération du code de admin/import/func_cnl.inc.php puis modif pour création du tableau des catégories, ce qui doit être retourné par la fonction
	$libelle_j = "" ;
	for ($a=0; $a<count($info_606_a); $a++) {
		for ($j=0; $j<count($info_606_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_606_j[$a][$j]) ;
				else $libelle_j .= " ** ".trim($info_606_j[$a][$j]) ;
			}
		if (!$libelle_j) $libelle_final = trim($info_606_a[$a][0]) ;
			else $libelle_final = trim($info_606_a[$a][0])." ** ".$libelle_j ;
		if (!$libelle_final) break ; 
		$res_a = categories::searchLibelle(addslashes($libelle_final), $thes->id_thesaurus, 'fr_FR', $id_rech_theme);
		if ($res_a) {
			$categid_a = $res_a;
		} else {
			$categid_a = create_categ_z3950($id_rech_theme, $libelle_final, strip_empty_words($libelle_final));
		}
		// récup des sous-categ en cascade sous $a
		$categ_parent =  $categid_a ;
		for ($x=0 ; $x < count($info_606_x[$a]) ; $x++) {
			$res_x = categories::searchLibelle(addslashes(trim($info_606_x[$a][$x])), $thes->id_thesaurus, 'fr_FR', $categ_parent);
			if ($res_x) {
				$categ_parent = $res_x;
			} else {
				$categ_parent = create_categ_z3950($categ_parent, trim($info_606_x[$a][$x]), strip_empty_words($info_606_x[$a][$x]));
			}
		} // fin récup des $x en cascade sous l'id de la catégorie 606$a
		
		if ($categ_parent != $id_rech_theme) {
			$categ_retour[]['categ_id'] = $categ_parent ;
			}
		
		// récup des categ géo à loger sous la categ géo principale
		$categ_parent =  $id_rech_geo ;
		for ($y=0 ; $y < count($info_606_y[$a]) ; $y++) {
			$res_y = categories::searchLibelle(addslashes(trim($info_606_y[$a][$y])), $thes->id_thesaurus, 'fr_FR', $categ_parent);
			if ($res_y) {
				$categ_parent = $res_y;		
			} else {
				$categ_parent = create_categ_z3950($categ_parent, trim($info_606_y[$a][$y]), strip_empty_words($info_606_y[$a][$y]));
			}
		} // fin récup des $y en cascade sous l'id de la catégorie principale thème géo
		
		if ($categ_parent != $id_rech_geo) {
			$categ_retour[]['categ_id'] = $categ_parent ;
			}
		
		// récup des categ chrono à loger sous la categ chrono principale
		$categ_parent =  $id_rech_chrono ;
		for ($z=0 ; $z < count($info_606_z[$a]) ; $z++) {
			$res_z = categories::searchLibelle(addslashes(trim($info_606_z[$a][$z])), $thes->id_thesaurus, 'fr_FR', $categ_parent);
			if ($res_z) {
				$categ_parent = $res_z;
			} else {
				$categ_parent = create_categ_z3950($categ_parent, trim($info_606_z[$a][$z]), strip_empty_words($info_606_z[$a][$z]));
			}
		} // fin récup des $z en cascade sous l'id de la catégorie principale thème chrono
		
		if ($categ_parent != $id_rech_chrono) {
			$categ_retour[]['categ_id'] = $categ_parent ;
		}
	}
// DEBUG echo "<pre>"; print_r($categ_retour) ; echo "</pre>"; exit ;
return $categ_retour ;
}


function create_categ_z3950($num_parent, $libelle, $index) {
    return z3950_notice::create_categ_z3950($num_parent, $libelle, $index);
}	
