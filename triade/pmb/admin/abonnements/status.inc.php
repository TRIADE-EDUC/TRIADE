<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: status.inc.php,v 1.3 2021/01/21 07:42:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path.'/abts_status.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('abts_status');
configuration_controller::set_list_ui_class_name('list_configuration_abonnements_status_ui');
configuration_controller::proceed($id);