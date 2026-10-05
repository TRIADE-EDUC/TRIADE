<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autorites.inc.php,v 1.28 2023/12/27 08:04:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $database_window_title, $msg, $user_input, $pmb_javascript_office_editor, $base_path, $categ, $plugin, $sub, $file;

//initialisation pour toutes les autorités
if (!isset($user_input)) $user_input = '';

if($pmb_javascript_office_editor){
	print $pmb_javascript_office_editor;
	print "<script type='text/javascript'>
        pmb_include('$base_path/javascript/tinyMCE_interface.js');
    </script>";
}

switch($categ) {
	case 'series':
		include('./autorites/series/series.inc.php');
		break;
	case 'indexint':
		include('./autorites/indexint/indexint.inc.php');
		break;
	case 'auteurs':
		include('./autorites/authors/authors.inc.php');
		break;
	case 'categories':
		if (SESSrights & THESAURUS_AUTH) include('./autorites/subjects/categories.inc.php');
		break;
	case 'editeurs':
		include('./autorites/publishers/publishers.inc.php');
		break;
	case 'collections':
		include('./autorites/collections/collections.inc.php');
		break;
	case 'souscollections':
		include('./autorites/subcollections/subcollections.inc.php');
		break;
	case 'concepts':
		if (SESSrights & CONCEPTS_AUTH) include('./autorites/onto/main.inc.php');
		break;
	case 'semantique':
		include('./autorites/semantique/semantique_main.inc.php');
		break;
	case 'titres_uniformes':
		include('./autorites/titres_uniformes/titres_uniformes.inc.php');
		break;
	case 'import':
		include('./autorites/import/main.inc.php');
		break;
	case 'onto' :
		include('./autorites/onto/main.inc.php');
		break;
	case 'authperso' :
		include('./autorites/authperso/authperso.inc.php');
		break;
	case 'see' :
		include('./autorites/see/main.inc.php');
		break;
	case 'search':
		include('./autorites/search/main.inc.php');
		break;
	case 'search_perso':
		include('./autorites/search_perso/main.inc.php');
		break;
	case 'caddie':
		include('./autorites/caddie/caddie.inc.php');
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed("autorites",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	default:
		include('./autorites/authors/authors.inc.php');
		break;
}
