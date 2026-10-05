<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_usage.inc.php,v 1.7 2021/01/21 08:52:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;
require_once($class_path."/notice_usage.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

function get_translated_usage_libelle($id_usage=0, $usage_libelle='') {
	return translation::get_translated_text($id_usage, 'notice_usage', 'usage_libelle',  $usage_libelle);
}

configuration_controller::set_model_class_name('notice_usage');
configuration_controller::set_list_ui_class_name('list_configuration_notices_notice_usage_ui');
configuration_controller::proceed($id);