<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
//
// Modifs effectuées pour respecter le paramètrage de la MdN (Médiatèque 
// dépatementale du Nord)
// Pb avec Orphée pour les numéros d'exemplaires. Utilisation du champ h en lieu
// et place du champ f et ajout de 0021 (code de la MdN) en fin de numéro 
// d'exemplaire. fredericg@free.fr
// 
// +-------------------------------------------------+
// $Id: func_bdp59.inc.php,v 1.8 2021/12/09 14:22:19 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_expl_bdp.class.php");

function recup_noticeunimarc_suite($notice) {
	} // fin recup_noticeunimarc_suite = fin récupération des variables propres BDP : rien de plus
	
function import_new_notice_suite() {
	global $notice_id;
	global $index_sujets;
	
	global $info_600_a, $info_600_j, $info_600_x, $info_600_y, $info_600_z;
	global $info_601_a, $info_601_j, $info_601_x, $info_601_y, $info_601_z;
	global $info_602_a, $info_602_j, $info_602_x, $info_602_y, $info_602_z;
	global $info_605_a, $info_605_j, $info_605_x, $info_605_y, $info_605_z;
	global $info_606_a, $info_606_j, $info_606_x, $info_606_y, $info_606_z;
	global $info_607_a, $info_607_j, $info_607_x, $info_607_y, $info_607_z;

	if (is_array($index_sujets)) {
	    $mots_cles = implode (" ",$index_sujets);
	} else {
	    $mots_cles = $index_sujets;
	}
	
	$nb_infos_600_a = count($info_600_a);
	for ($a = 0; $a < $nb_infos_600_a; $a++) {
		$mots_cles .= " " . $info_600_a[$a][0];
		
		$nb_infos_600_j = count($info_600_j[$a]);
		for ($j = 0; $j < $nb_infos_600_j; $j++) {
		    $mots_cles .= " " . $info_600_j[$a][$j];
		}
		
		$nb_infos_600_x = count($info_600_x[$a]);
		for ($j = 0; $j < $nb_infos_600_x; $j++) {
		    $mots_cles .= " " . $info_600_x[$a][$j];
		}
		$nb_infos_600_y = count($info_600_y[$a]);
		for ($j = 0; $j < $nb_infos_600_y; $j++) {
		    $mots_cles .= " " . $info_600_y[$a][$j];
		}
		
		$nb_infos_600_z = count($info_600_z[$a]);
		for ($j = 0; $j < $nb_infos_600_z; $j++) {
		    $mots_cles .= " " . $info_600_z[$a][$j];
		}
	}
	
	$nb_infos_601_a = count($info_601_a);
	for ($a = 0; $a < $nb_infos_601_a; $a++) {
	    $mots_cles .= " " . $info_601_a[$a][0];
	    
	    $nb_infos_601_j = count($info_601_j[$a]);
	    for ($j = 0; $j < $nb_infos_601_j; $j++) {
	        $mots_cles .= " " . $info_601_j[$a][$j];
	    }
	    
	    $nb_infos_601_x = count($info_601_x[$a]);
	    for ($j = 0; $j < $nb_infos_601_x; $j++) {
	        $mots_cles .= " " . $info_601_x[$a][$j];
	    }
	    $nb_infos_601_y = count($info_601_y[$a]);
	    for ($j = 0; $j < $nb_infos_601_y; $j++) {
	        $mots_cles .= " " . $info_601_y[$a][$j];
	    }
	    
	    $nb_infos_601_z = count($info_601_z[$a]);
	    for ($j = 0; $j < $nb_infos_601_z; $j++) {
	        $mots_cles .= " " . $info_601_z[$a][$j];
	    }
	}
	
	$nb_infos_602_a = count($info_602_a);
	for ($a = 0; $a < $nb_infos_602_a; $a++) {
	    $mots_cles .= " " . $info_602_a[$a][0];
	    
	    $nb_infos_602_j = count($info_602_j[$a]);
	    for ($j = 0; $j < $nb_infos_602_j; $j++) {
	        $mots_cles .= " " . $info_602_j[$a][$j];
	    }
	    
	    $nb_infos_602_x = count($info_602_x[$a]);
	    for ($j = 0; $j < $nb_infos_602_x; $j++) {
	        $mots_cles .= " " . $info_602_x[$a][$j];
	    }
	    $nb_infos_602_y = count($info_602_y[$a]);
	    for ($j = 0; $j < $nb_infos_602_y; $j++) {
	        $mots_cles .= " " . $info_602_y[$a][$j];
	    }
	    
	    $nb_infos_602_z = count($info_602_z[$a]);
	    for ($j = 0; $j < $nb_infos_602_z; $j++) {
	        $mots_cles .= " " . $info_602_z[$a][$j];
	    }
	}
	
	$nb_infos_605_a = count($info_605_a);
	for ($a = 0; $a < $nb_infos_605_a; $a++) {
	    $mots_cles .= " " . $info_605_a[$a][0];
	    
	    $nb_infos_605_j = count($info_605_j[$a]);
	    for ($j = 0; $j < $nb_infos_605_j; $j++) {
	        $mots_cles .= " " . $info_605_j[$a][$j];
	    }
	    
	    $nb_infos_605_x = count($info_605_x[$a]);
	    for ($j = 0; $j < $nb_infos_605_x; $j++) {
	        $mots_cles .= " " . $info_605_x[$a][$j];
	    }
	    $nb_infos_605_y = count($info_605_y[$a]);
	    for ($j = 0; $j < $nb_infos_605_y; $j++) {
	        $mots_cles .= " " . $info_605_y[$a][$j];
	    }
	    
	    $nb_infos_605_z = count($info_605_z[$a]);
	    for ($j = 0; $j < $nb_infos_605_z; $j++) {
	        $mots_cles .= " " . $info_605_z[$a][$j];
	    }
	}
	
	$nb_infos_606_a = count($info_606_a);
	for ($a = 0; $a < $nb_infos_606_a; $a++) {
	    $mots_cles .= " " . $info_606_a[$a][0];
	    
	    $nb_infos_606_j = count($info_606_j[$a]);
	    for ($j = 0; $j < $nb_infos_606_j; $j++) {
	        $mots_cles .= " " . $info_606_j[$a][$j];
	    }
	    
	    $nb_infos_606_x = count($info_606_x[$a]);
	    for ($j = 0; $j < $nb_infos_606_x; $j++) {
	        $mots_cles .= " " . $info_606_x[$a][$j];
	    }
	    $nb_infos_606_y = count($info_606_y[$a]);
	    for ($j = 0; $j < $nb_infos_606_y; $j++) {
	        $mots_cles .= " " . $info_606_y[$a][$j];
	    }
	    
	    $nb_infos_606_z = count($info_606_z[$a]);
	    for ($j = 0; $j < $nb_infos_606_z; $j++) {
	        $mots_cles .= " " . $info_606_z[$a][$j];
	    }
	}
	
	$nb_infos_607_a = count($info_607_a);
	for ($a = 0; $a < $nb_infos_607_a; $a++) {
	    $mots_cles .= " " . $info_607_a[$a][0];
	    
	    $nb_infos_607_j = count($info_607_j[$a]);
	    for ($j = 0; $j < $nb_infos_607_j; $j++) {
	        $mots_cles .= " " . $info_607_j[$a][$j];
	    }
	    
	    $nb_infos_607_x = count($info_607_x[$a]);
	    for ($j = 0; $j < $nb_infos_607_x; $j++) {
	        $mots_cles .= " " . $info_607_x[$a][$j];
	    }
	    $nb_infos_607_y = count($info_607_y[$a]);
	    for ($j = 0; $j < $nb_infos_607_y; $j++) {
	        $mots_cles .= " " . $info_607_y[$a][$j];
	    }
	    
	    $nb_infos_607_z = count($info_607_z[$a]);
	    for ($j = 0; $j < $nb_infos_607_z; $j++) {
	        $mots_cles .= " " . $info_607_z[$a][$j];
	    }
	}

	$index_matieres = (!empty($mots_cles) ? strip_empty_words($mots_cles) : '');
	$rqt_maj = "update notices set index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes($index_matieres)." ' where notice_id='$notice_id' ";
	pmb_mysql_query($rqt_maj);
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('bdp59');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	$subfields = array();
	$subfields["a"] = $ex -> lender_libelle;
	$subfields["c"] = $ex -> lender_libelle;
	//$subfields["f"] = $ex -> expl_cb;
	$subfields["h"] = $ex -> expl_cb;
	$subfields["k"] = $ex -> expl_cote;
	$subfields["u"] = $ex -> expl_note;

	if ($ex->statusdoc_codage_import) $subfields["o"] = $ex -> statusdoc_codage_import;
	if ($ex -> tdoc_codage_import) $subfields["r"] = $ex -> tdoc_codage_import;
		else $subfields["r"] = "uu";
	if ($ex -> sdoc_codage_import) $subfields["q"] = $ex -> sdoc_codage_import;
		else $subfields["q"] = "u";
	
	global $export996 ;
	//$export996['f'] = $ex -> expl_cb ;
	$export996['h'] = $ex -> expl_cb ;
	$export996['k'] = $ex -> expl_cote ;
	$export996['u'] = $ex -> expl_note ;

	$export996['m'] = substr($ex -> expl_date_depot, 0, 4).substr($ex -> expl_date_depot, 5, 2).substr($ex -> expl_date_depot, 8, 2) ;
	$export996['n'] = substr($ex -> expl_date_retour, 0, 4).substr($ex -> expl_date_retour, 5, 2).substr($ex -> expl_date_retour, 8, 2) ;

	$export996['a'] = $ex -> lender_libelle;
	$export996['b'] = $ex -> expl_owner;

	$export996['v'] = $ex -> location_libelle;
	$export996['w'] = $ex -> locdoc_codage_import;

	$export996['x'] = $ex -> section_libelle;
	$export996['y'] = $ex -> sdoc_codage_import;

	$export996['e'] = $ex -> tdoc_libelle;
	$export996['r'] = $ex -> tdoc_codage_import;

	$export996['1'] = $ex -> statut_libelle;
	$export996['2'] = $ex -> statusdoc_codage_import;
	$export996['3'] = $ex -> pret_flag;
	
	global $export_traitement_exemplaires ;
	$export996['0'] = $export_traitement_exemplaires ;
	
	return 	$subfields ;

	}	
