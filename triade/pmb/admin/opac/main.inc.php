<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.13 2024/01/31 07:35:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch recherche notice
global $base_path, $class_path, $include_path, $sub, $lang, $id;

// inclusions principales

switch($sub) {
	case "opac_view": 
		// affichage de la liste des vues Opac
		include("./admin/opac/opac_view/main.inc.php");
	break;	
	case "search_persopac":
		// affichage de la liste des recherches en opac
		include("./admin/opac/search_persopac/main.inc.php");
	break;	
	case "stat":
		//affichage des statistiques pour l'opac
		include("./admin/opac/stat/main.inc.php");
		break;
	case 'navigopac':
		include("./admin/opac/navigation_opac.inc.php");
		break;
	case "facettes":
	case "facettes_authorities":
	case "facettes_external":
	case "facettes_comparateur":
		require_once($class_path.'/modules/module_admin.class.php');
		$module_admin = new module_admin();
		$module_admin->set_url_base($base_path."/admin.php?categ=opac");
		$id = intval($id);
	    $module_admin->set_object_id($id);
		$module_admin->proceed_opac_facets();
		break;
	case "maintenance":
		// définition de la page de maintenance
		include("./admin/opac/maintenance/main.inc.php");
		break;
	case "analytics_services":
		// gestion des services web analytiques
		include("./admin/opac/analytics_services/main.inc.php");
		break;
	default :
        include("$include_path/messages/help/$lang/admin_opac.txt");
	break;
}
?>