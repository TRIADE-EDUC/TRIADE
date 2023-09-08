<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.9 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $moyen, $catalog_layout, $msg;

switch ($moyen) {
	case 'import':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_collecte_import"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/collecte/import.inc.php");
		break;
	case 'selection':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_collecte_selection"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/collecte/selection.inc.php");
		break;
	case 'douchette':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_collecte_cb"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/collecte/douchette.inc.php");
		break;
	default:
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', "", $catalog_layout);
		print $catalog_layout ;
		print "<br /><br /><b>".$msg["caddie_select_collecte"]."</b>" ;
		break;
	}
