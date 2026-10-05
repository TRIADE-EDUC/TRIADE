<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: status.inc.php,v 1.4 2021/01/21 08:52:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

//dépendances
require_once($class_path.'/scan_request/scan_request_status.class.php');
require_once($class_path.'/scan_request/scan_request_admin_status.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");
require_once($class_path."/list/configuration/scan_request/list_configuration_scan_request_status_ui.class.php");

configuration_controller::set_model_class_name('scan_request_status');
configuration_controller::set_list_ui_class_name('list_configuration_scan_request_status_ui');
configuration_controller::proceed($id);