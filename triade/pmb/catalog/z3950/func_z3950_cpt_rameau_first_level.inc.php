<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_z3950_cpt_rameau_first_level.inc.php,v 1.17 2023/10/11 10:09:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;
global $thesaurus_defaut, $thes;

// enregistrement de la notices dans les catégories
require_once "$include_path/misc.inc.php" ;
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categories.class.php");

//Attention, dans le multithesaurus, le thesaurus dans lequel on importe est le thesaurus par defaut
$thes = new thesaurus($thesaurus_defaut);
 
function traite_categories_enreg($notice_retour,$categories,$thesaurus_traite=0) {
	z3950_notice::traite_categories_enreg($notice_retour, $categories, $thesaurus_traite);
}


function traite_categories_for_form($tableau_600 = array(), $tableau_601 = array(), $tableau_602 = array(), $tableau_605 = array(), $tableau_606 = array(), $tableau_607 = array(), $tableau_608 = array()) {
	global $charset, $rameau;
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
				else $libelle_j .= " ** ".trim($info_606_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_606_a[$a][0]) ; else $libelle_final = trim($info_606_a[$a][0])." ** ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<count($info_606_x[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_606_x[$a][$j]) ;
		}
		for ($j=0; $j<count($info_606_y[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_606_y[$a][$j]) ;
		}
		for ($j=0; $j<count($info_606_z[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_606_z[$a][$j]) ;
		}
		if ($champ_rameau) $champ_rameau.=" @@@ ";
		$champ_rameau.=$libelle_final;
	} 

	$info_607_a = $tableau_607["info_607_a"] ;
	$info_607_j = $tableau_607["info_607_j"] ;
	$info_607_x = $tableau_607["info_607_x"] ;
	$info_607_y = $tableau_607["info_607_y"] ;
	$info_607_z = $tableau_607["info_607_z"] ;
	for ($a=0; $a<count($info_607_a); $a++) {
		$libelle_final="";
		$libelle_j="";
		for ($j=0; $j<count($info_607_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_607_j[$a][$j]) ;
				else $libelle_j .= " ** ".trim($info_607_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_607_a[$a][0]) ; else $libelle_final = trim($info_607_a[$a][0])." ** ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<count($info_607_x[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_607_x[$a][$j]) ;
		}
		for ($j=0; $j<count($info_607_y[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_607_y[$a][$j]) ;
		}
		for ($j=0; $j<count($info_607_z[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_607_z[$a][$j]) ;
		}
		if ($champ_rameau) $champ_rameau.=" @@@ ";
		$champ_rameau.=$libelle_final;
	} 

	// $rameau est la variable traitée par la fonction traite_categories_from_form, 
	// $rameau est normalement POSTée, afin de pouvoir être traitée en lot, donc hors 
	// formulaire, il faut l'affecter.
	$rameau = addslashes($champ_rameau) ;

	return array(
		"form" => "<input type='hidden' name='rameau' value='".htmlentities($champ_rameau,ENT_QUOTES,$charset)."' />",
		"message" => "<br />Rameau sera int&eacute;gr&eacute; au premier niveau des cat&eacute;gories : ".str_replace("@@@", "<br />", htmlentities($champ_rameau,ENT_QUOTES,$charset))
	);
}


function traite_categories_from_form() {
	global $rameau ;
	global $thesaurus_defaut;
	global $thes;
	
	$categories=array();
	$categ_first=explode(" @@@ ",stripslashes($rameau));
	
	for ($i=0; $i<count($categ_first); $i++) {
		$categ_first[$i]=trim($categ_first[$i]);
		if ($categ_first[$i]) {
			$resultat = categories::searchLibelle(addslashes($categ_first[$i]), $thesaurus_defaut, 'fr_FR');	
			if (!$resultat){
				$categories[]["categ_id"] = create_categ_z3950($thes->num_noeud_racine, $categ_first[$i], " ".addslashes(strip_empty_words($categ_first[$i]))." ");
			} else {
				$categories[]["categ_id"] = $resultat;
			}
		}
	}
		
	return $categories ;
}


function create_categ_z3950($num_parent, $libelle, $index) {
    return z3950_notice::create_categ_z3950($num_parent, $libelle, $index);
}	
