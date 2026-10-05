<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts.inc.php,v 1.11 2021/01/21 08:52:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $id;

//Gestion des paramètres des abonnements
require_once($class_path.'/type_abt.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");
require_once("$include_path/templates/finance.tpl.php");

configuration_controller::set_model_class_name('type_abt');
configuration_controller::set_list_ui_class_name('list_configuration_finance_abts_ui');
configuration_controller::proceed($id);