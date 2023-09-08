<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.10 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $quoi, $catalog_layout, $msg, $baseLink, $categ, $action;

require_once("$class_path/classementGen.class.php") ;

// inclusions principales
switch ($quoi) {
	case 'procs':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_gestion_procs"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/gestion/procs.inc.php");
		break;
	case 'remote_procs':
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["remote_procedures_catalog_title"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/gestion/remote_procs.inc.php");
		break;
	case "classementGen" :
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["classementGen_list_libelle"], $catalog_layout);
		print $catalog_layout ;
		$baseLink="./catalog.php?categ=caddie&sub=gestion&quoi=classementGen";
		$classementGen = new classementGen($categ,0);
		$classementGen->proceed($action);
		break;
	case 'panier':
	default:
		$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_gestion_panier"], $catalog_layout);
		print $catalog_layout ;
		include ("./catalog/caddie/gestion/panier.inc.php");
		break;
	}
