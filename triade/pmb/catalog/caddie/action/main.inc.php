<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.18 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $quelle, $msg, $catalog_layout;

switch ($quelle) {
	case 'changebloc':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_change_bloc"], $catalog_layout);
		print $catalog_layout ;
		break;
	case 'transfert':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_transfert"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/transfert.inc.php");
		break;
	case 'export':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_export"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/export.inc.php");
		break;
	case 'supprpanier':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_suppr_panier"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/supprpanier.inc.php");
		break;
	case 'supprbase':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_suppr_base"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/supprbase.inc.php");
		break;
	case 'edition':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_edition"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/edition.inc.php");
		break;
	case 'selection':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_selection"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/selection.inc.php");
		break;
	case 'impr_cote':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_impr_cote"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/impr_cote.inc.php");
		break;
	case 'expdocnum':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_exp_docnum"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/expdocnum.inc.php");
		break;
	case 'reindex':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_reindex"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/reindex.inc.php");
		break;
	case 'access_rights':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_select_access_rights"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/access_rights.inc.php");
		break;
	case 'scan_request':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["scan_request_record_button"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/scan_request.inc.php");
		break;
	case 'transfert_to_location':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_action_transfert_to_location"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/action/transfert_to_location.inc.php");
		break;
	default:
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', "", $catalog_layout);
		print $catalog_layout ;
		print "<br /><br /><b>".$msg["caddie_select_action"]."</b>" ;
		break;
	}
