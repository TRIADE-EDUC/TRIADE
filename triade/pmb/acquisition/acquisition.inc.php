<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acquisition.inc.php,v 1.25.8.1 2024/07/19 07:01:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $categ, $charset, $acquisition_gestion_tva, $sub, $plugin, $acquisition_sugg_to_cde, $error_msg;
global $acquisition_rent_requests_activate, $id;

require_once("$class_path/entites.class.php");
require_once("$class_path/paiements.class.php");
require_once("$class_path/frais.class.php");
require_once("$class_path/types_produits.class.php");
require_once("$class_path/offres_remises.class.php");
require_once("$class_path/tva_achats.class.php");

//Recherche des etablissements auxquels a acces l'utilisateur
$q = entites::list_biblio(SESSuserid);
$list_bib = pmb_mysql_query($q);
$nb_bib=pmb_mysql_num_rows($list_bib);
$tab_bib=array();
while ($row=pmb_mysql_fetch_object($list_bib)) {
	$tab_bib[0][]=$row->id_entite;
	$tab_bib[1][]=$row->raison_sociale;
}		

switch($categ) {
	case 'ach':
		if(!$nb_bib) {
			//Pas de bibliothèques définies pour l'utilisateur
			$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";	
			error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
			die;
		}

		//Gestion de la tva
		if ($acquisition_gestion_tva) {
			$nbr = tva_achats::countTva();
			
			//Gestion de TVA et pas de taux de tva définis
			if (!$nbr) {
				$error_msg.= htmlentities($msg["acquisition_err_tva"],ENT_QUOTES, $charset)."<div class='row'></div>";	
				error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
				die;
			}
		}
		include_once('./acquisition/achats/achats.inc.php');
		break;

	case 'sug':
		
		switch($sub) {			
			case 'multi':
				include_once('./acquisition/suggestions/suggestions_multi.inc.php');
			break;
			case 'import':
				include_once('./acquisition/suggestions/suggestions_import.inc.php');
			break;
			case 'export':
				include_once('./acquisition/suggestions/suggestions_export.inc.php');
			break;
			case 'export_tableau':
				include_once('./acquisition/suggestions/suggestions_export_tableau.inc.php');
			break;
			case 'empr_sug':
				include_once('./acquisition/suggestions/suggestions_empr.inc.php');
			break;
			default:
				include_once('./acquisition/suggestions/suggestions.inc.php');
			break;
		}		
	break;

	case 'rent':
	    if ($acquisition_rent_requests_activate) {
            $module_acquisition = module_acquisition::get_instance();
            $module_acquisition->set_object_id($id);
            $module_acquisition->proceed_rent();
	    }
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed("acquisition",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	
	default:
		if (!$nb_bib && !$acquisition_sugg_to_cde) {
			include_once('./acquisition/suggestions/suggestions.inc.php');
		} else {
			if(!$nb_bib) {
				//Pas de bibliothèques définies pour l'utilisateur
				$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";	
				error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
				die;
			}
			
			//Gestion de la tva
			if ($acquisition_gestion_tva) {
				$nbr = tva_achats::countTva();
				//Gestion de TVA et pas de taux de tva définis
				if (!$nbr) {
					$error_msg.= htmlentities($msg["acquisition_err_tva"],ENT_QUOTES, $charset)."<div class='row'></div>";	
					error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
					die;
				}
			}
			include_once('./acquisition/achats/achats.inc.php');
		}
		break;
}