<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie.inc.php,v 1.9 2021/05/03 07:59:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $include_path, $sub;
global $idcaddie, $idemprcaddie;

if(!isset($idcaddie)) $idcaddie = 0;
if(!isset($idemprcaddie)) $idemprcaddie = 0;

// functions particulières à ce module
require_once("$include_path/templates/cart.tpl.php");
require_once("$include_path/templates/empr_cart.tpl.php");
require_once("$class_path/empr_caddie.class.php");
require_once("$class_path/parameters.class.php") ;
require_once("$class_path/emprunteur.class.php") ;
require_once("$include_path/empr_cart.inc.php");
require_once("$include_path/cart.inc.php");
require_once("$base_path/circ/empr/empr_func.inc.php");

require_once($class_path."/caddie/empr_caddie_controller.class.php");

$idcaddie = empr_caddie::check_rights($idcaddie) ;

switch($sub) {
	case "action" :
		include('./circ/caddie/action/main.inc.php');
		break;
	case "gestion" :
	default:
		include('./circ/caddie/gestion/main.inc.php');
		break;
	}

