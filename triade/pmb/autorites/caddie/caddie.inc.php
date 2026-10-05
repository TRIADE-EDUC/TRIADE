<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie.inc.php,v 1.6 2021/04/28 06:52:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $idcaddie, $quoi, $moyen, $quelle, $class_path, $sub;
global $callback, $elements;

if(!isset($idcaddie)) $idcaddie = 0;
if(!isset($quoi)) $quoi = '';
if(!isset($moyen)) $moyen = '';
if(!isset($quelle)) $quelle = '';

require_once($class_path."/caddie/authorities_caddie_controller.class.php") ;

$idcaddie = authorities_caddie::check_rights($idcaddie) ;

switch($sub) {
	case "pointage" :
		authorities_caddie_controller::proceed_module_pointage($moyen, $idcaddie);
		break;
	case "action" :
		authorities_caddie_controller::proceed_module_action($quelle, $idcaddie);
		break;
	case "collecte" :
		authorities_caddie_controller::proceed_module_collecte($moyen, $idcaddie);
		break;
	case "remplir":
		authorities_caddie_controller::proceed_module_remplir($callback, $elements);
		break;
	case "gestion" :
	default:
		authorities_caddie_controller::proceed_module_gestion($quoi, $idcaddie);
		break;
}