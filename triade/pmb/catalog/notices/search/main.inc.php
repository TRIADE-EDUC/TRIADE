<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.18 2021/04/28 06:52:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $class_path, $info_authpersos;
global $mode, $id, $layout_end;

// page de switch recherche notice

// inclusions principales
require_once("$include_path/templates/notice_search.tpl.php");
require_once("$class_path/authperso.class.php");

$authpersos= new authpersos();
$info_authpersos=$authpersos->get_data();

if($id) {
	// notice sélectionnée -> création de la page de notice
	// include du fichier des opérations d'affichage
	include('./catalog/notices/isbd.inc.php');
} else {
	switch($mode) {
		case 1:
			// recherche catégorie/sujet INDEXATION INTERNE
			include('./catalog/notices/search/subjects/main.inc.php');
			break;
		case 5:
			// recherche par termes
			include('./catalog/notices/search/terms/main.inc.php');
			break;
		case 2:
			// recherche éditeur/collection
			include('./catalog/notices/search/publishers/main.inc.php');
			break;
		case 3:
			// accès aux paniers
			include('./catalog/notices/search/cart.inc.php');
			break;
		case 4:
			// autres recherches
			include('./catalog/notices/search/others.inc.php');
			break;		
		case 6:
			// recherches avancees
			include('./catalog/notices/search/extended/main.inc.php');
			break;
		case 7:
			// recherches externe
			include('./catalog/notices/search/external/main.inc.php');
			break;	
		case 8:
			// recherches exemplaires
			include('./catalog/notices/search/expl/main.inc.php');
			break;		
		case 9:
			// recherches titres uniformes
			include('./catalog/notices/search/titres_uniformes/main.inc.php');
			break;
		case 10:
			// recherches titres de série
			include('./catalog/notices/search/titre_serie/main.inc.php');
			break;
		case 11:
			// recherches cartes
			include('./catalog/notices/search/map/main.inc.php');
			break;
		default :
			if($mode>1000){				
				// authperso				
				if($info_authpersos[$mode-1000]){
					include('./catalog/notices/search/authperso/main.inc.php');					
					break;
				}	
			}
			// recherche auteur/titre
			include('./catalog/notices/search/authors/main.inc.php');
			break;
	}
	print $layout_end;
}
