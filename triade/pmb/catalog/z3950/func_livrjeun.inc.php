<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_livrjeun.inc.php,v 1.9 2023/10/11 10:09:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path;

// enregistrement de la notices dans les catégories
require_once "$include_path/misc.inc.php" ;

function traite_categories_enreg($notice_retour, $categories, $thesaurus_traite = 0) {
	z3950_notice::traite_categories_enreg($notice_retour, $categories, $thesaurus_traite);
	
	// on ignore ce qui suit pour l'import livrjeun
	$rqt_maj = "update notices set lien='', eformat='', indexint=0 where notice_id='$notice_retour' " ;
	pmb_mysql_query($rqt_maj);
}

function traite_categories_for_form($tableau_600 = array(), $tableau_601 = array(), $tableau_602 = array(), $tableau_605 = array(), $tableau_606 = array(), $tableau_607 = array(), $tableau_608 = array()) {
	global $charset, $rameau;
	global $pmb_keyword_sep ;
	global $index_sujets ;
	
	$champ_rameau="";
	$mots_cles=array();

	$info_600_a = $tableau_600["info_600_a"] ;
	$info_600_j = $tableau_600["info_600_j"] ;
	$info_600_x = $tableau_600["info_600_x"] ;
	$info_600_y = $tableau_600["info_600_y"] ;
	$info_600_z = $tableau_600["info_600_z"] ;
	for ($a=0; $a<count($info_600_a); $a++) {
		if ($info_600_a[$a][0]) $mots_cles[] = $info_600_a[$a][0] ;
		for ($j=0; $j<count($info_600_j[$a]); $j++) if ($info_600_j[$a][$j]) $mots_cles[] = $info_600_j[$a][$j] ;
		for ($j=0; $j<count($info_600_x[$a]); $j++) if ($info_600_x[$a][$j]) $mots_cles[] = $info_600_x[$a][$j] ;
		for ($j=0; $j<count($info_600_y[$a]); $j++) if ($info_600_y[$a][$j]) $mots_cles[] = $info_600_y[$a][$j] ;
		for ($j=0; $j<count($info_600_z[$a]); $j++) if ($info_600_z[$a][$j]) $mots_cles[] = $info_600_z[$a][$j] ;
		}

	$info_600_a = $tableau_601["info_601_a"] ;
	$info_600_j = $tableau_601["info_601_j"] ;
	$info_600_x = $tableau_601["info_601_x"] ;
	$info_600_y = $tableau_601["info_601_y"] ;
	$info_600_z = $tableau_601["info_601_z"] ;
	for ($a=0; $a<count($info_600_a); $a++) {
		if ($info_600_a[$a][0]) $mots_cles[] = $info_600_a[$a][0] ;
		for ($j=0; $j<count($info_600_j[$a]); $j++) if ($info_600_j[$a][$j]) $mots_cles[] = $info_600_j[$a][$j] ;
		for ($j=0; $j<count($info_600_x[$a]); $j++) if ($info_600_x[$a][$j]) $mots_cles[] = $info_600_x[$a][$j] ;
		for ($j=0; $j<count($info_600_y[$a]); $j++) if ($info_600_y[$a][$j]) $mots_cles[] = $info_600_y[$a][$j] ;
		for ($j=0; $j<count($info_600_z[$a]); $j++) if ($info_600_z[$a][$j]) $mots_cles[] = $info_600_z[$a][$j] ;
		}
		
	$info_600_a = $tableau_606["info_606_a"] ;
	$info_600_j = $tableau_606["info_606_j"] ;
	$info_600_x = $tableau_606["info_606_x"] ;
	$info_600_y = $tableau_606["info_606_y"] ;
	$info_600_z = $tableau_606["info_606_z"] ;
	for ($a=0; $a<count($info_600_a); $a++) {
		if ($info_600_a[$a][0]) $mots_cles[] = $info_600_a[$a][0] ;
		for ($j=0; $j<count($info_600_j[$a]); $j++) if ($info_600_j[$a][$j]) $mots_cles[] = $info_600_j[$a][$j] ;
		for ($j=0; $j<count($info_600_x[$a]); $j++) if ($info_600_x[$a][$j]) $mots_cles[] = $info_600_x[$a][$j] ;
		for ($j=0; $j<count($info_600_y[$a]); $j++) if ($info_600_y[$a][$j]) $mots_cles[] = $info_600_y[$a][$j] ;
		for ($j=0; $j<count($info_600_z[$a]); $j++) if ($info_600_z[$a][$j]) $mots_cles[] = $info_600_z[$a][$j] ;
		}

	$info_600_a = $tableau_607["info_607_a"] ;
	$info_600_j = $tableau_607["info_607_j"] ;
	$info_600_x = $tableau_607["info_607_x"] ;
	$info_600_y = $tableau_607["info_607_y"] ;
	$info_600_z = $tableau_607["info_607_z"] ;
	for ($a=0; $a<count($info_600_a); $a++) {
		if ($info_600_a[$a][0]) $mots_cles[] = $info_600_a[$a][0] ;
		for ($j=0; $j<count($info_600_j[$a]); $j++) if ($info_600_j[$a][$j]) $mots_cles[] = $info_600_j[$a][$j] ;
		for ($j=0; $j<count($info_600_x[$a]); $j++) if ($info_600_x[$a][$j]) $mots_cles[] = $info_600_x[$a][$j] ;
		for ($j=0; $j<count($info_600_y[$a]); $j++) if ($info_600_y[$a][$j]) $mots_cles[] = $info_600_y[$a][$j] ;
		for ($j=0; $j<count($info_600_z[$a]); $j++) if ($info_600_z[$a][$j]) $mots_cles[] = $info_600_z[$a][$j] ;
		}
		
	$champ_rameau = implode($pmb_keyword_sep, $mots_cles);
	
	// $rameau est la variable traitée par la fonction traite_categories_from_form, 
	// $rameau est normalement POSTée, afin de pouvoir être traitée en lot, donc hors 
	// formulaire, il faut l'affecter.
	$index_sujets = $champ_rameau ;
	$rameau = addslashes($champ_rameau) ;
	// <input type='hidden' name='rameau' value='".htmlentities($champ_rameau,ENT_QUOTES,$charset)."' />
	return array(
		"form" => "",
		"message" => "Les champs 600, 601, 606 et 607 seront intégrés en zone d'indexation libre : ".$champ_rameau
	);
}


function traite_categories_from_form() {
	global $rameau ;
	global $f_free_index ;
	global $pmb_keyword_sep ;
	if (!$pmb_keyword_sep) $pmb_keyword_sep=" ";
	
	$f_free_index=$rameau;
	
	return z3950_notice::traite_categories_from_form();
}
