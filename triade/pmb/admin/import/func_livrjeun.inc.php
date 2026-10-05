<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_livrjeun.inc.php,v 1.9 2021/12/09 14:22:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_expl_bdp.class.php");

function recup_noticeunimarc_suite($notice) {

	// on ignore ce qui suit pour l'import livrjeun
	global $lien, $eformat	;
	global $dewey, $dewey_l, $info_686 ;
	$dewey			= array();
	$dewey_l		= array();
	$lien			= array();
	$eformat		= array();
	$info_686		= array();
} // fin recup_noticeunimarc_suite 
	
function import_new_notice_suite() {
	global $notice_id;
	global $index_sujets;
	global $pmb_keyword_sep;
	
	global $info_600_a, $info_600_j, $info_600_x, $info_600_y, $info_600_z;
	global $info_601_a, $info_601_j, $info_601_x, $info_601_y, $info_601_z;
	global $info_602_a, $info_602_j, $info_602_x, $info_602_y, $info_602_z;
	global $info_605_a, $info_605_j, $info_605_x, $info_605_y, $info_605_z;
	global $info_606_a, $info_606_j, $info_606_x, $info_606_y, $info_606_z;
	global $info_607_a, $info_607_j, $info_607_x, $info_607_y, $info_607_z;

	// on laisse tomber les 610 pour livrjeun
	$index_sujets = "";
	$mots_cles = array();
	
	$nb_infos_600_a = count($info_600_a);
	for ($a = 0; $a < $nb_infos_600_a; $a++) {
	    if (!empty($info_600_a[$a][0])) {
	        $mots_cles[] = $info_600_a[$a][0];
	    }
	    
	    $nb_infos_600_j = count($info_600_j[$a]);
	    for ($j = 0; $j < $nb_infos_600_j; $j++) {
	        if (!empty($info_600_j[$a][$j])) {
	            $mots_cles[] = $info_600_j[$a][$j];
	        }
	    }
	    
	    $nb_infos_600_x = count($info_600_x[$a]);
	    for ($j = 0; $j < $nb_infos_600_x; $j++) {
	        if (!empty($info_600_x[$a][$j])) {
	            $mots_cles[] = $info_600_x[$a][$j];
	        }
	    }
	    
	    $nb_infos_600_y = count($info_600_y[$a]);
	    for ($j = 0; $j < $nb_infos_600_y; $j++) {
	        if (!empty($info_600_y[$a][$j])) {
	            $mots_cles[] = $info_600_y[$a][$j];
	        }
	    }
	    
	    $nb_infos_600_z = count($info_600_z[$a]);
	    for ($j = 0; $j < $nb_infos_600_z; $j++) {
	        if (!empty($info_600_z[$a][$j])) {
	            $mots_cles[] = $info_600_z[$a][$j];
	        }
	    }
	}
	
	$nb_infos_601_a = count($info_601_a);
	for ($a = 0; $a < $nb_infos_601_a; $a++) {
	    if (!empty($info_601_a[$a][0])) {
	        $mots_cles[] = $info_601_a[$a][0];
	    }
	    
	    $nb_infos_601_j = count($info_601_j[$a]);
	    for ($j = 0; $j < $nb_infos_601_j; $j++) {
	        if (!empty($info_601_j[$a][$j])) {
	            $mots_cles[] = $info_601_j[$a][$j];
	        }
	    }
	    
	    $nb_infos_601_x = count($info_601_x[$a]);
	    for ($j = 0; $j < $nb_infos_601_x; $j++) {
	        if (!empty($info_601_x[$a][$j])) {
	            $mots_cles[] = $info_601_x[$a][$j];
	        }
	    }
	    
	    $nb_infos_601_y = count($info_601_y[$a]);
	    for ($j = 0; $j < $nb_infos_601_y; $j++) {
	        if (!empty($info_601_y[$a][$j])) {
	            $mots_cles[] = $info_601_y[$a][$j];
	        }
	    }
	    
	    $nb_infos_601_z = count($info_601_z[$a]);
	    for ($j = 0; $j < $nb_infos_601_z; $j++) {
	        if (!empty($info_601_z[$a][$j])) {
	            $mots_cles[] = $info_601_z[$a][$j];
	        }
	    }
	}
	
	$nb_infos_606_a = count($info_606_a);
	for ($a = 0; $a < $nb_infos_606_a; $a++) {
	    if (!empty($info_606_a[$a][0])) {
	        $mots_cles[] = $info_606_a[$a][0];
	    }
	    
	    $nb_infos_606_j = count($info_606_j[$a]);
	    for ($j = 0; $j < $nb_infos_606_j; $j++) {
	        if (!empty($info_606_j[$a][$j])) {
	            $mots_cles[] = $info_606_j[$a][$j];
	        }
	    }
	    
	    $nb_infos_606_x = count($info_606_x[$a]);
	    for ($j = 0; $j < $nb_infos_606_x; $j++) {
	        if (!empty($info_606_x[$a][$j])) {
	            $mots_cles[] = $info_606_x[$a][$j];
	        }
	    }
	    
	    $nb_infos_606_y = count($info_606_y[$a]);
	    for ($j = 0; $j < $nb_infos_606_y; $j++) {
	        if (!empty($info_606_y[$a][$j])) {
	            $mots_cles[] = $info_606_y[$a][$j];
	        }
	    }
	    
	    $nb_infos_606_z = count($info_606_z[$a]);
	    for ($j = 0; $j < $nb_infos_606_z; $j++) {
	        if (!empty($info_606_z[$a][$j])) {
	            $mots_cles[] = $info_606_z[$a][$j];
	        }
	    }
	}

	$nb_infos_607_a = count($info_607_a);
	for ($a = 0; $a < $nb_infos_607_a; $a++) {
	    if (!empty($info_607_a[$a][0])) {
	        $mots_cles[] = $info_607_a[$a][0];
	    }
	    
	    $nb_infos_607_j = count($info_607_j[$a]);
	    for ($j = 0; $j < $nb_infos_607_j; $j++) {
	        if (!empty($info_607_j[$a][$j])) {
	            $mots_cles[] = $info_607_j[$a][$j];
	        }
	    }
	    
	    $nb_infos_607_x = count($info_607_x[$a]);
	    for ($j = 0; $j < $nb_infos_607_x; $j++) {
	        if (!empty($info_607_x[$a][$j])) {
	            $mots_cles[] = $info_607_x[$a][$j];
	        }
	    }
	    
	    $nb_infos_607_y = count($info_607_y[$a]);
	    for ($j = 0; $j < $nb_infos_607_y; $j++) {
	        if (!empty($info_607_y[$a][$j])) {
	            $mots_cles[] = $info_607_y[$a][$j];
	        }
	    }
	    
	    $nb_infos_607_z = count($info_607_z[$a]);
	    for ($j = 0; $j < $nb_infos_607_z; $j++) {
	        if (!empty($info_607_z[$a][$j])) {
	            $mots_cles[] = $info_607_z[$a][$j];
	        }
	    }
	}
	
	$mots_cles = implode($pmb_keyword_sep, $mots_cles);
	$index_matieres = (!empty($mots_cles) ? strip_empty_words($mots_cles) : '');
	$rqt_maj = "update notices set index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes($index_matieres)." ' where notice_id='$notice_id' ";
	pmb_mysql_query($rqt_maj);
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('livrjeun');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}