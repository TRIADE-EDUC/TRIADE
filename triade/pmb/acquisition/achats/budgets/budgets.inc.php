<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: budgets.inc.php,v 1.19 2021/04/22 09:00:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $acquisition_no_html, $include_path, $action, $id_bibli, $id_bud;

// gestion des budgets
require_once("$class_path/entites.class.php");
require_once("$class_path/budgets.class.php");

if (!$acquisition_no_html) {
	require_once("$include_path/templates/budgets.tpl.php");
}

switch($action) {
	case 'list':
		entites::setSessionBibliId($id_bibli);
		echo budgets::show_list_bud($id_bibli);
		break;
	case 'show':
	    echo budgets::show_bud($id_bibli, $id_bud);
		break;
	case 'print_budget':
	    budgets::print_bud($id_bibli, $id_bud);
		break;
	default:
		echo entites::show_list_biblio('show_list_bud', 'budgets');	
		break;
}
