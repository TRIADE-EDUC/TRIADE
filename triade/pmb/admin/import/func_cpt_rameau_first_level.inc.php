<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_cpt_rameau_first_level.inc.php,v 1.12 2021/12/09 14:22:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

/*
	Fonction personnalisée d'import pour importer au premier niveau des catégories
	les vedettes matières rameau (BnF, SUDOC, etc.)
	Les vedettes sont reconstruites avec les zones 600 à 607, $a, , $x, $y, $z
*/

// enregistrement de la notices dans les catégories
global $include_path, $class_path; //N�cessaire pour certaines inclusions
require_once ($class_path."/import/import_expl_bdp.class.php");
require_once "$include_path/misc.inc.php" ;
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categories.class.php");
global $thes, $thesaurus_defaut;

//Attention, dans le multithesaurus, le thesaurus dans lequel on importe est le thesaurus par defaut
$thes = new thesaurus($thesaurus_defaut);

function recup_noticeunimarc_suite($notice) {
	} // fin recup_noticeunimarc_suite = fin récupération des variables propres BDP : rien de plus
	
function import_new_notice_suite() {
	global $notice_id;
	global $thes, $thesaurus_defaut;
	global $index_sujets;
	global $pmb_keyword_sep;
	
	global $info_600_a, $info_600_j, $info_600_x, $info_600_y, $info_600_z;
	global $info_601_a, $info_601_j, $info_601_x, $info_601_y, $info_601_z;
	global $info_602_a, $info_602_j, $info_602_x, $info_602_y, $info_602_z;
	global $info_605_a, $info_605_j, $info_605_x, $info_605_y, $info_605_z;
	global $info_606_a, $info_606_j, $info_606_x, $info_606_y, $info_606_z;
	global $info_607_a, $info_607_j, $info_607_x, $info_607_y, $info_607_z;
	
	$nb_infos_600_a = count($info_600_a);
	for ($a = 0; $a < $nb_infos_600_a; $a++) {
		$rameau .= "@@@".trim($info_600_a[$a][0]);
		
		$nb_infos_600_j = count($info_600_j[$a]);
		for ($j = 0; $j < $nb_infos_600_j; $j++) {
		    $rameau .= " -- ".trim($info_600_j[$a][$j]);
		}
		
		$nb_infos_600_x = count($info_600_x[$a]);
		for ($j = 0; $j < $nb_infos_600_x; $j++) {
		    $rameau .= " -- ".trim($info_600_x[$a][$j]);
		}
		
		$nb_infos_600_y = count($info_600_y[$a]);
		for ($j = 0; $j < $nb_infos_600_y; $j++) {
		    $rameau .= " -- ".trim($info_600_y[$a][$j]);
		}
		
		$nb_infos_600_z = count($info_600_z[$a]);
		for ($j = 0; $j < $nb_infos_600_z; $j++) {
		    $rameau .= " -- ".trim($info_600_z[$a][$j]);
		}
	}
	
	$nb_infos_601_a = count($info_601_a);
	for ($a = 0; $a < $nb_infos_601_a; $a++) {
	    $rameau .= "@@@".trim($info_601_a[$a][0]);
	    
	    $nb_infos_601_j = count($info_601_j[$a]);
	    for ($j = 0; $j < $nb_infos_601_j; $j++) {
	        $rameau .= " -- ".trim($info_601_j[$a][$j]);
	    }
	    
	    $nb_infos_601_x = count($info_601_x[$a]);
	    for ($j = 0; $j < $nb_infos_601_x; $j++) {
	        $rameau .= " -- ".trim($info_601_x[$a][$j]);
	    }
	    
	    $nb_infos_601_y = count($info_601_y[$a]);
	    for ($j = 0; $j < $nb_infos_601_y; $j++) {
	        $rameau .= " -- ".trim($info_601_y[$a][$j]);
	    }
	    
	    $nb_infos_601_z = count($info_601_z[$a]);
	    for ($j = 0; $j < $nb_infos_601_z; $j++) {
	        $rameau .= " -- ".trim($info_601_z[$a][$j]);
	    }
	}
	
	$nb_infos_602_a = count($info_602_a);
	for ($a = 0; $a < $nb_infos_602_a; $a++) {
	    $rameau .= "@@@".trim($info_602_a[$a][0]);
	    
	    $nb_infos_602_j = count($info_602_j[$a]);
	    for ($j = 0; $j < $nb_infos_602_j; $j++) {
	        $rameau .= " -- ".trim($info_602_j[$a][$j]);
	    }
	    
	    $nb_infos_602_x = count($info_602_x[$a]);
	    for ($j = 0; $j < $nb_infos_602_x; $j++) {
	        $rameau .= " -- ".trim($info_602_x[$a][$j]);
	    }
	    
	    $nb_infos_602_y = count($info_602_y[$a]);
	    for ($j = 0; $j < $nb_infos_602_y; $j++) {
	        $rameau .= " -- ".trim($info_602_y[$a][$j]);
	    }
	    
	    $nb_infos_602_z = count($info_602_z[$a]);
	    for ($j = 0; $j < $nb_infos_602_z; $j++) {
	        $rameau .= " -- ".trim($info_602_z[$a][$j]);
	    }
	}

	$nb_infos_605_a = count($info_605_a);
	for ($a = 0; $a < $nb_infos_605_a; $a++) {
	    $rameau .= "@@@".trim($info_605_a[$a][0]);
	    
	    $nb_infos_605_j = count($info_605_j[$a]);
	    for ($j = 0; $j < $nb_infos_605_j; $j++) {
	        $rameau .= " -- ".trim($info_605_j[$a][$j]);
	    }
	    
	    $nb_infos_605_x = count($info_605_x[$a]);
	    for ($j = 0; $j < $nb_infos_605_x; $j++) {
	        $rameau .= " -- ".trim($info_605_x[$a][$j]);
	    }
	    
	    $nb_infos_605_y = count($info_605_y[$a]);
	    for ($j = 0; $j < $nb_infos_605_y; $j++) {
	        $rameau .= " -- ".trim($info_605_y[$a][$j]);
	    }
	    
	    $nb_infos_605_z = count($info_605_z[$a]);
	    for ($j = 0; $j < $nb_infos_605_z; $j++) {
	        $rameau .= " -- ".trim($info_605_z[$a][$j]);
	    }
	}
	
	$nb_infos_606_a = count($info_606_a);
	for ($a = 0; $a < $nb_infos_606_a; $a++) {
	    $rameau .= "@@@".trim($info_606_a[$a][0]);
	    
	    $nb_infos_606_j = count($info_606_j[$a]);
	    for ($j = 0; $j < $nb_infos_606_j; $j++) {
	        $rameau .= " -- ".trim($info_606_j[$a][$j]);
	    }
	    
	    $nb_infos_606_x = count($info_606_x[$a]);
	    for ($j = 0; $j < $nb_infos_606_x; $j++) {
	        $rameau .= " -- ".trim($info_606_x[$a][$j]);
	    }
	    
	    $nb_infos_606_y = count($info_606_y[$a]);
	    for ($j = 0; $j < $nb_infos_606_y; $j++) {
	        $rameau .= " -- ".trim($info_606_y[$a][$j]);
	    }
	    
	    $nb_infos_606_z = count($info_606_z[$a]);
	    for ($j = 0; $j < $nb_infos_606_z; $j++) {
	        $rameau .= " -- ".trim($info_606_z[$a][$j]);
	    }
	}
	
	$nb_infos_607_a = count($info_607_a);
	for ($a = 0; $a < $nb_infos_607_a; $a++) {
	    $rameau .= "@@@".trim($info_607_a[$a][0]);
	    
	    $nb_infos_607_j = count($info_607_j[$a]);
	    for ($j = 0; $j < $nb_infos_607_j; $j++) {
	        $rameau .= " -- ".trim($info_607_j[$a][$j]);
	    }
	    
	    $nb_infos_607_x = count($info_607_x[$a]);
	    for ($j = 0; $j < $nb_infos_607_x; $j++) {
	        $rameau .= " -- ".trim($info_607_x[$a][$j]);
	    }
	    
	    $nb_infos_607_y = count($info_607_y[$a]);
	    for ($j = 0; $j < $nb_infos_607_y; $j++) {
	        $rameau .= " -- ".trim($info_607_y[$a][$j]);
	    }
	    
	    $nb_infos_607_z = count($info_607_z[$a]);
	    for ($j = 0; $j < $nb_infos_607_z; $j++) {
	        $rameau .= " -- ".trim($info_607_z[$a][$j]);
	    }
	}

	$categ_first = explode("@@@", stripslashes($rameau));
	for ($i = 1; $i < count($categ_first); $i++) {
		$resultat = categories::searchLibelle(addslashes($categ_first[$i]), $thesaurus_defaut, 'fr_FR');
		if (empty($resultat)) {
			/*vérification de l'existence des categs, sinon création */
			$resultat = create_categ_cpt_rameau_first_level($thes->num_noeud_racine, $categ_first[$i], ' '.strip_empty_words($categ_first[$i]).' ');
		} 
		/* ajout de l'indexation à la notice dans la table notices_categories*/
		$rqt_ajout = "insert into notices_categories set notcateg_notice='$notice_id', num_noeud='$resultat', ordre_categorie=".($i-1);
		pmb_mysql_query($rqt_ajout);
	}
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('cpt_rameau_first_level');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

function create_categ_cpt_rameau_first_level($num_parent, $libelle, $index) {
	
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

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}