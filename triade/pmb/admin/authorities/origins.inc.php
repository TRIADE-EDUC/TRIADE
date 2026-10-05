<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: origins.inc.php,v 1.3 2021/01/21 12:30:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $id;

require_once($class_path."/origins.class.php");
require_once($include_path."/templates/origin.tpl.php");
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('origin');
configuration_controller::set_list_ui_class_name('list_configuration_authorities_origins_ui');
configuration_controller::proceed($id);