<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.inc.php,v 1.22 2018-08-03 10:17:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($idcaddie)) $idcaddie = 0; else $idcaddie += 0;
if(!isset($item)) $item = '';

require_once($class_path.'/caddie/resa_caddie_controller.class.php');
require_once($class_path.'/elements_list/elements_records_list_ui.class.php');

// inclusions principales
require_once("$include_path/cart.inc.php");
require_once("$include_path/templates/cart.tpl.php");

resa_caddie_controller::set_id_empr($id_empr);
resa_caddie_controller::set_groupID($groupID);
resa_caddie_controller::proceed($idcaddie, $item);