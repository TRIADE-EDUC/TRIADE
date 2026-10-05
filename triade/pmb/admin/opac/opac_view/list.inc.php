<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list.inc.php,v 1.2 2021/02/04 07:39:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch vues Opac
global $class_path, $action, $opac_view_id;

// inclusions principales
require_once("$class_path/opac_view.class.php");

$opac_view= new opac_view($opac_view_id);
switch($action) {	
	case "add":
	case "edit":
	case "form":
		print $opac_view->get_form();			
		break;
	case "param":
		// gere le formulaire et la mémorisation des parametre de subtitution
		$ret= $opac_view->get_form_param();
		if($ret) print $ret;// c'est le formulaire de parametre
		else print $opac_view->get_form();	
		break;
	case "save":
		// sauvegarde issu du formulaire
		$opac_view->update_form();
		if($opac_view_id) print list_configuration_opac_opac_view_ui::get_instance()->get_display_list();
		else print $opac_view->get_form();	// sert à compléter le formulaire après sa création
		break;	
	case "delete":
		// effacement, issu du formulaire
		opac_view::delete($opac_view_id);
		print list_configuration_opac_opac_view_ui::get_instance()->get_display_list();
		break;	
	case "gen":
		// genère la liste des id de notices des vues opac
		print $opac_view->gen();
		print list_configuration_opac_opac_view_ui::get_instance()->get_display_list();
		break;		
	default :
		// affiche liste des recherches personalisée
		print list_configuration_opac_opac_view_ui::get_instance()->get_display_list();
	break;
}


