<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: priorities.inc.php,v 1.3 2021/01/21 08:52:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

//dépendances
require_once($class_path.'/scan_request/scan_request_priority.class.php');
require_once($class_path.'/scan_request/scan_request_priorities.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");
require_once($class_path."/list/configuration/scan_request/list_configuration_scan_request_priorities_ui.class.php");

configuration_controller::set_model_class_name('scan_request_priority');
configuration_controller::set_list_ui_class_name('list_configuration_scan_request_priorities_ui');
configuration_controller::proceed($id);