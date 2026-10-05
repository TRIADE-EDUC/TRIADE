<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_src.inc.php,v 1.4 2021/01/21 08:52:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $act, $id, $id_src;

require_once($class_path."/suggestion_source.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

if(empty($action) && !empty($act)) {
	$action = $act;
}

configuration_controller::set_model_class_name('suggestion_source');
configuration_controller::set_list_ui_class_name('list_configuration_acquisition_src_ui');
configuration_controller::proceed((!empty($id_src) ? $id_src : $id));
?>