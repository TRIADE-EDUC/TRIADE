<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_other.inc.php,v 1.22 2023/10/11 10:09:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// enregistrement de la notices dans les catégories
function traite_categories_enreg($notice_retour,$categories,$thesaurus_traite=0) {
	z3950_notice::traite_categories_enreg($notice_retour, $categories, $thesaurus_traite);
}

function traite_categories_for_form($tableau_600 = array(), $tableau_601 = array(), $tableau_602 = array(), $tableau_605 = array(), $tableau_606 = array(), $tableau_607 = array(), $tableau_608 = array()) {
	global $charset, $msg, $pmb_keyword_sep, $rameau;
	$rameau = "";
	$info_606_a = (isset($tableau_606["info_606_a"]) ? $tableau_606["info_606_a"] : '');
	$info_606_j = (isset($tableau_606["info_606_j"]) ? $tableau_606["info_606_j"] : '');
	$info_606_x = (isset($tableau_606["info_606_x"]) ? $tableau_606["info_606_x"] : '');
	$info_606_y = (isset($tableau_606["info_606_y"]) ? $tableau_606["info_606_y"] : '');
	$info_606_z = (isset($tableau_606["info_606_z"]) ? $tableau_606["info_606_z"] : '');
	
	$champ_rameau = "";
	$nb_infos_606_a = (is_array($info_606_a) ? count($info_606_a) : 0);
	for ($a = 0; $a < $nb_infos_606_a; $a++) {
		$libelle_final = "";
		$libelle_j = "";
		
		$nb_infos_606_j = count($info_606_j[$a]);
		for ($j = 0; $j < $nb_infos_606_j; $j++) {
			if (empty($libelle_j)) {
				$libelle_j .= trim($info_606_j[$a][$j]);
			} else {
				$libelle_j .= " ** ".trim($info_606_j[$a][$j]);
			}
		}
		
		if (empty($libelle_j)) {
			$libelle_final = trim($info_606_a[$a][0]);
		} else {
			$libelle_final = trim($info_606_a[$a][0])." ** $libelle_j";
		}
		if (empty($libelle_final)) {
			break;
		}
		
		$nb_infos_606_x = count($info_606_x[$a]);
		for ($j = 0; $j < $nb_infos_606_x; $j++) {
			$libelle_final .= " : ".trim($info_606_x[$a][$j]);
		}
		
		$nb_infos_606_y = count($info_606_y[$a]);
		for ($j = 0; $j < $nb_infos_606_y; $j++) {
			$libelle_final .= " : ".trim($info_606_y[$a][$j]);
		}
		
		$nb_infos_606_z = count($info_606_z[$a]);
		for ($j = 0; $j < $nb_infos_606_z; $j++) {
			$libelle_final .= " : ".trim($info_606_z[$a][$j]);
		}
		
		if (!empty($champ_rameau)) {
			$champ_rameau .= " @@@ ";
		}
		$champ_rameau .= $libelle_final;
	}
	
	return array(
			"form" => "",
			"message" => htmlentities($msg['traite_categ_ignore'].$champ_rameau, ENT_QUOTES, $charset)
	);
}


function traite_categories_from_form() {
	return z3950_notice::traite_categories_from_form();
}

function traite_concepts_for_form($tableau_606 = array()) {
	global $charset, $msg, $pmb_keyword_sep, $rameau;
	$rameau = "";
	$info_606_a = $tableau_606["info_606_a"];
	$info_606_j = $tableau_606["info_606_j"];
	$info_606_x = $tableau_606["info_606_x"];
	$info_606_y = $tableau_606["info_606_y"];
	$info_606_z = $tableau_606["info_606_z"];

	$champ_rameau = "";
	$nb_infos_606_a = count($info_606_a);
	for ($a = 0; $a < $nb_infos_606_a; $a++) {
		$libelle_final = "";
		$libelle_j = "";
		
		$nb_infos_606_j = count($info_606_j[$a]);
		for ($j = 0; $j < $nb_infos_606_j; $j++) {
		    if (empty($libelle_j)) {
		        $libelle_j .= trim($info_606_j[$a][$j]);
		    } else {
		        $libelle_j .= " -- ".trim($info_606_j[$a][$j]);
		    }
		}
		
		if (empty($libelle_j)) {
		    $libelle_final = trim($info_606_a[$a][0]);
		} else {
		    $libelle_final = trim($info_606_a[$a][0])." -- $libelle_j";
		}
		if (empty($libelle_final)) {
		    break;
		}
		
		$nb_infos_606_x = count($info_606_x[$a]);
		for ($j = 0; $j < $nb_infos_606_x; $j++) {
			$libelle_final .= " -- ".trim($info_606_x[$a][$j]);
		}
		
		$nb_infos_606_y = count($info_606_y[$a]);
		for ($j = 0; $j < $nb_infos_606_y; $j++) {
			$libelle_final .= " -- ".trim($info_606_y[$a][$j]);
		}
		
		$nb_infos_606_z = count($info_606_z[$a]);
		for ($j = 0; $j < $nb_infos_606_z; $j++) {
			$libelle_final .= " -- ".trim($info_606_z[$a][$j]);
		}
		
		if (!empty($champ_rameau)) {
		    $champ_rameau .= " @@@ ";
		}
		$champ_rameau .= $libelle_final;
	}

	return array(
			"form" => "",
			"message" => htmlentities($champ_rameau, ENT_QUOTES, $charset)
	);
}
