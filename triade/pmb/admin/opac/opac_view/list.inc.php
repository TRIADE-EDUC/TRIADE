<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list.inc.php,v 1.1 2011-04-20 06:27:21 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch vues Opac

// inclusions principales
require_once("$class_path/opac_view.class.php");

$opac_view= new opac_view($opac_view_id);
switch($action) {	
	case "add":
		// affichage du formulaire de vue Opac, en création
		print $opac_view->do_form();			
	break;
	case "form":
		// affichage du formulaire, en modification => $id
		print $opac_view->do_form();	
	break;
	case "param":
		// gere le formulaire et la mémorisation des parametre de subtitution
		$ret= $opac_view->get_form_param();
		if($ret) print $ret;// c'est le formulaire de parametre
		else print $opac_view->do_form();	
	break;
	case "save":
		// sauvegarde issu du formulaire
		$opac_view->update_form();
		if($opac_view_id) print $opac_view->do_list();
		else print $opac_view->do_form();	// sert à compléter le formulaire après sa création
	break;	
	case "delete":
		// effacement, issu du formulaire
		$opac_view->delete();
		print $opac_view->do_list();
	break;	
	case "gen":
		// genère la liste des id de notices des vues opac
		print $opac_view->gen();
		print $opac_view->do_list();			
	break;		
	default :
		// affiche liste des recherches personalisée
		print $opac_view->do_list();
	break;
}


