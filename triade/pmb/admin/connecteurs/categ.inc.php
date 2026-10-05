<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ.inc.php,v 1.10 2021/02/23 12:47:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
global $id;

require_once($class_path."/connecteurs.class.php");
require_once($class_path."/connectors/connectors_categ.class.php");
require_once($class_path."/configuration/configuration_connecteurs_controller.class.php");

configuration_connecteurs_controller::set_model_class_name('connectors_categ');
configuration_connecteurs_controller::set_list_ui_class_name('list_configuration_connecteurs_categ_ui');
configuration_connecteurs_controller::proceed($id);