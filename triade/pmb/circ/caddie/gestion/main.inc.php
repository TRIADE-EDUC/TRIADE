<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.11 2019-03-20 16:34:06 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/classementGen.class.php") ;

if (empty($quoi)) $quoi = '';

switch ($quoi) {
	case 'razpointage':
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["empr_caddie_menu_pointage_raz"]);
		$empr_menu_panier_pointage = str_replace('!!sous_menu_choisi!!', $msg["empr_caddie_menu_pointage_raz"], $empr_menu_panier_pointage);
		print $empr_menu_panier_pointage ;
		include ("./circ/caddie/gestion/pointage_raz.inc.php");
		break;
	case 'pointage':
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["empr_caddie_menu_pointage_selection"]);
		$empr_menu_panier_pointage = str_replace('!!sous_menu_choisi!!', $msg["empr_caddie_menu_pointage_selection"], $empr_menu_panier_pointage);
		print $empr_menu_panier_pointage ;
		include ("./circ/caddie/gestion/pointage_selection.inc.php");
		break;
	case 'pointagebarcode':
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["empr_caddie_menu_pointage_barcode"]);
		$empr_menu_panier_pointage = str_replace('!!sous_menu_choisi!!', $msg["empr_caddie_menu_pointage_barcode"], $empr_menu_panier_pointage);
		print $empr_menu_panier_pointage ;
		include ("./circ/caddie/gestion/pointage_barcode.inc.php");
		break;
	case 'selection':
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["empr_caddie_menu_collecte_selection"]);
		$empr_menu_panier_collecte = str_replace('!!sous_menu_choisi!!', $msg["empr_caddie_menu_collecte_selection"], $empr_menu_panier_collecte);
		print $empr_menu_panier_collecte ;
		include ("./circ/caddie/gestion/collecte_selection.inc.php");
		break;
	case 'barcode':
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["empr_caddie_menu_collecte_barcode"]);
		$empr_menu_panier_collecte = str_replace('!!sous_menu_choisi!!', $msg["empr_caddie_menu_collecte_barcode"], $empr_menu_panier_collecte);
		print $empr_menu_panier_collecte ;
		include ("./circ/caddie/gestion/collecte_barcode.inc.php");
		break;
	case 'procs':
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["empr_caddie_menu_gestion_procs"]);
		$empr_menu_panier_gestion = str_replace('!!sous_menu_choisi!!', $msg["empr_caddie_menu_gestion_procs"], $empr_menu_panier_gestion);
		print $empr_menu_panier_gestion ;
		include ("./circ/caddie/gestion/procs.inc.php");
		break;
	case 'remote_procs':
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["remote_procedures_circ_title"]);
		$empr_menu_panier_gestion = str_replace('!!sous_menu_choisi!!', $msg["remote_procedures_circ_title"], $empr_menu_panier_gestion);
		print $empr_menu_panier_gestion ;
		include ("./circ/caddie/gestion/remote_procs.inc.php");
		break;
	case "classementGen" :
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["classementGen_list_libelle"]);
		$empr_menu_panier_gestion = str_replace('!!sous_menu_choisi!!', $msg["classementGen_list_libelle"], $empr_menu_panier_gestion);
		print $empr_menu_panier_gestion ;
		$baseLink="./circ.php?categ=caddie&sub=gestion&quoi=classementGen";
		$classementGen = new classementGen("empr_caddie",0);
		$classementGen->proceed($action);
		break;
	case 'pointagepanier':
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["empr_caddie_menu_pointage_panier"]);
		$empr_menu_panier_pointage = str_replace('!!sous_menu_choisi!!', $msg["empr_caddie_menu_pointage_panier"], $empr_menu_panier_pointage);
		print $empr_menu_panier_pointage ;
		empr_caddie_controller::proceed_by_caddie($idemprcaddie);
		break;
	case 'panier':
	default:
		echo window_title($database_window_title.$msg['empr_caddie_menu']." : ".$msg["empr_caddie_menu_gestion"]);
		$empr_menu_panier_gestion = str_replace('!!sous_menu_choisi!!', $msg["caddie_menu_gestion_panier"], $empr_menu_panier_gestion);
		print $empr_menu_panier_gestion ;
		include ("./circ/caddie/gestion/panier.inc.php");
		break;
	}
