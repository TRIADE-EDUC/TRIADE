<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2012-08-08 14:42:08 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// inclusions principales

if(!$section && $elements){
	$section = "affect";
}
switch($section) {
	case "affect" :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["opac_view_admin_menu"], $admin_layout);
		print $admin_layout;
		print $admin_menu_opac_views;
		include($base_path."/admin/opac/opac_view/affect.inc.php");
		break;
	case "list":
	default :
		// affichage de la liste des recherches en opac
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["opac_view_admin_menu"], $admin_layout);
		print $admin_layout;		
		print $admin_menu_opac_views;
		include("./admin/opac/opac_view/list.inc.php");
	break;
}


