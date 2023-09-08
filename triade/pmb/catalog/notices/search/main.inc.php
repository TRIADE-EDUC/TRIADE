<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.17 2019-06-07 08:05:38 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $authperso_list_name, $include_path, $class_path, $authperso, $info_authpersos, $tpl_authperso, $menu_search_tpl, $charset, $tpl_elt;
global $selected, $mode, $menu_search, $id, $layout_end;

// page de switch recherche notice

// inclusions principales
require_once("$include_path/templates/notice_search.tpl.php");
require_once("$class_path/authperso.class.php");

$authpersos= new authpersos();
$info_authpersos=$authpersos->get_data();

$tpl_authperso = '';
foreach($info_authpersos as $authperso){
	if($authperso['gestion_search'] != 2) continue; // pas de boutton

	$tpl_elt=$menu_search_tpl;
	$tpl_elt = str_replace("!!label!!",htmlentities(stripslashes($authperso['name']),ENT_QUOTES, $charset),$tpl_elt);
	$tpl_elt = str_replace("!!mode!!",$authperso['id']+1000,$tpl_elt);
	$selected="";
	if($mode==$authperso['id']+1000)$selected ="class=\"selected\"";
	$tpl_elt = str_replace("!!selected!!",$selected,$tpl_elt);
	$tpl_authperso.=$tpl_elt;
	$authperso_list_name[$authperso['id']+1000]=$authperso['name'];
}

for($i=0;$i<count($menu_search);$i++) {
	if(isset($menu_search[$i])) {
		$menu_search[$i] = str_replace("<!-- !!authpersos!! -->",$tpl_authperso,$menu_search[$i]);
	}
}
// spécifique autorités perso
if(isset($menu_search[1000])) {
	$menu_search[1000] = str_replace("<!-- !!authpersos!! -->",$tpl_authperso,$menu_search[1000]);
}

if($id) {
	// notice sélectionnée -> création de la page de notice
	// include du fichier des opérations d'affichage
	include('./catalog/notices/isbd.inc.php');
} else {
	switch($mode) {
		case 1:
			// recherche catégorie/sujet INDEXATION INTERNE
			print $menu_search[1];
			include('./catalog/notices/search/subjects/main.inc.php');
			break;
		case 5:
			// recherche par termes
			print $menu_search[5];
			include('./catalog/notices/search/terms/main.inc.php');
			break;
		case 2:
			// recherche éditeur/collection
			print $menu_search[2];
			include('./catalog/notices/search/publishers/main.inc.php');
			break;
		case 3:
			// accès aux paniers
			print $menu_search[3];
			include('./catalog/notices/search/cart.inc.php');
			break;
		case 4:
			// autres recherches
			print $menu_search[4];
			include('./catalog/notices/search/others.inc.php');
			break;		
		case 6:
			// recherches avancees
			print $menu_search[6];
			include('./catalog/notices/search/extended/main.inc.php');
			break;
		case 7:
			// recherches externe
			print $menu_search[7];
			include('./catalog/notices/search/external/main.inc.php');
			break;	
		case 8:
			// recherches exemplaires
			print $menu_search[8];
			include('./catalog/notices/search/expl/main.inc.php');
			break;		
		case 9:
			// recherches titres uniformes
			print $menu_search[9];
			include('./catalog/notices/search/titres_uniformes/main.inc.php');
			break;
		case 10:
			// recherches titres de série
			print $menu_search[10];
			include('./catalog/notices/search/titre_serie/main.inc.php');
			break;
		case 11:
			// recherches cartes
			print $menu_search[11];
			include('./catalog/notices/search/map/main.inc.php');
			break;
		default :
			if($mode>1000){				
				// authperso				
				if($info_authpersos[$mode-1000]){
					$menu_search[1000]=str_replace("!!authperso_search_title!!",$authperso_list_name[$mode],$menu_search[1000]);
					print $menu_search[1000];
					include('./catalog/notices/search/authperso/main.inc.php');					
					break;
				}	
			}
			// recherche auteur/titre
			print $menu_search[0];
			include('./catalog/notices/search/authors/main.inc.php');
			break;
	}
	print $layout_end;
}
