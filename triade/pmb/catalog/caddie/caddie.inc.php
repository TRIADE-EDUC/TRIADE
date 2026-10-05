<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie.inc.php,v 1.15 2021/04/28 06:52:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $idcaddie, $sub, $include_path, $class_path;

if(!isset($idcaddie)) $idcaddie = 0;

// functions particulières à ce module
require_once("./catalog/caddie/caddie_func.inc.php");
require_once("$include_path/templates/cart.tpl.php");
require_once("$include_path/expl_info.inc.php");
require_once("$class_path/caddie.class.php");
require_once("$class_path/serials.class.php");
require_once("$class_path/parameters.class.php") ;
require_once("$class_path/emprunteur.class.php") ;
require_once("$include_path/cart.inc.php");
require_once("$include_path/bull_info.inc.php");

$idcaddie = caddie::check_rights($idcaddie) ;

switch($sub) {
	case "pointage" :
		include('./catalog/caddie/pointage/main.inc.php');
		break;
	case "action" :
		include('./catalog/caddie/action/main.inc.php');
		break;
	case "collecte" :
		include('./catalog/caddie/collecte/main.inc.php');
		break;
	case "remplir":
		include('./catalog/caddie/remplir/main.inc.php');
		break;
	case "gestion" :
	default:
		include('./catalog/caddie/gestion/main.inc.php');
		break;
	}

