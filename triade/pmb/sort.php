<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort.php,v 1.18 2024/03/01 14:29:08 dgoron Exp $

$base_path = ".";
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[histo_title]";
$base_nobody = 1;  
$base_use_dojo = 1;

require ($base_path . "/includes/init.inc.php");

global $class_path, $include_path;
global $origin, $num_segment;

include ($include_path . "/error_report.inc.php");
require_once ($class_path . "/sort.class.php");

//permet de préciser sur quoi vont s'appliquer les tris (par defaut:notices)
if (isset($_REQUEST["type_tri"])) {
    if (is_numeric($_REQUEST["type_tri"])) {
        $triType = entities::get_sort_string_from_const_type($_REQUEST["type_tri"]);
    } else {
    	$triType = $_REQUEST["type_tri"];
    }
} else {
	//par defaut affichage de la liste des tris
	$triType = "notices";
}

//action (par defaut:affliste)
if (isset($_REQUEST["action_tri"])) {
	$actionTri = $_REQUEST["action_tri"];
} else {
	//par defaut affichage de la liste des tris
	$actionTri = "affliste";
}

//echo "action:".$actionTri."<br />";

//déclaration de la classe
$sort = new sort($triType,'base');
$sort->caller = $_REQUEST['caller'] ?? "";
switch ($actionTri) {
	case "enreg" :
		//insertion ou modification d'un tri

		if ($_REQUEST['id_tri']) {
			//c'est une modification car on a un identifiant
			$id_tri = $_REQUEST['id_tri'];
		} else {
			//c'est une insertion car on a pas d'id
			$id_tri = "";
		}
		
		if ($_REQUEST['nom_tri']) {
			$nom_tri = $_REQUEST['nom_tri'];
		}
		
		if ((isset ($_REQUEST['liste_sel'])) && !empty ($_REQUEST['liste_sel'])) {
			$liste_sel = $_REQUEST['liste_sel'];
		}
		
		//on a un nom et une liste de parametres
		if (($nom_tri) && ($liste_sel)) {
			//on enregistre le tri
			$affichage = $sort->sauvegarder($id_tri, $nom_tri, $liste_sel);
			echo $affichage;
		}
		//apres la sauvegarde on affiche la liste
		global $popup;
		if (!empty($popup) && $popup) {
		    echo $sort->show_popup_tris_form();
		} else {
		    echo $sort->show_tris_form();
		}
		break;
	case "modif" :
		//modification d'un tri
		 
		if ($_REQUEST['id_tri']) {
			//modification du tri précisé
			$id_tri = intval($_REQUEST['id_tri']);
		} else {
			//ce n'est pas une modif mais un ajout
			$id_tri = 0;
		}
		//pour les segments de recherche
		if ($_REQUEST['index_tri']) {
		    //modification du tri précisé
		    $index_tri = intval($_REQUEST['index_tri']);
		} else {
		    $index_tri = 0;
		}
		if ($_REQUEST['sort_string']) {
		    $string_sort = $_REQUEST['sort_string'];
		} else {
		    $string_sort = "";
		}
		
		//affichage de l'écran de modification du tri
		echo $sort->show_sel_formAdmin($id_tri,$index_tri, $string_sort);
		if (isset($origin) && "search_segment" == $origin) {
		    $translation = new translation($num_segment, 'search_segments');
		    if($index_tri !== null) {
		        $data_translation = $translation->get_data();
		        if (!empty($data_translation['segment_sort'])) {
		            foreach ($data_translation['segment_sort'] as $translated_language=>$translated_text) {
		                $translated_sorts = explode('||',$translated_text);
		                if (!empty(explode('|',$translated_sorts[$index_tri])[1])){
		                    $data_translation['segment_sort'][$translated_language] = trim(explode('|',$translated_sorts[$index_tri])[1]);
		                } else {
		                    $data_translation['segment_sort'][$translated_language] = '';
		                }
		            }
		            $translation->set_data($data_translation);
		        }
		    }
		} else {
		    $translation = new translation($id_tri, 'tris');
		}
		echo $translation->connect('sort_form');
		break;
	case "supp" :
		//suppression d'un tri
		 
		if ($_REQUEST['id_tri']) {
			//on a bien un id
			$id_tri = $_REQUEST['id_tri'];
			
			//c'est le tri actif
			if ($id_tri == $_SESSION["tri"]) {
				//on le désactive
				$_SESSION["tri"] = "";
			}
			
			//on supprime le tri
			$sort->supprimer($id_tri);
		}
		//apres la suppression on affiche la liste
		echo $sort->show_tris_form();
		break;
	case "affliste" : 
	default:
	    if (isset($_REQUEST['categ']) && "search_universes" == $_REQUEST['categ'] ) {
	        echo $sort->show_popup_tris_form_segment();
	        break;
	    }
	    
	    //affichage de la liste
	    global $popup;
	    if (!empty($popup) && $popup) {
    		echo $sort->show_popup_tris_form();
	    } else {
    		echo $sort->show_tris_form();
	    }
		break;
}
