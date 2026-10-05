<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: out_set_categ.inc.php,v 1.5 2021/02/23 09:48:28 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;
require_once($class_path."/connecteurs_out_sets.class.php");
require_once($class_path."/configuration/configuration_connecteurs_controller.class.php");

configuration_connecteurs_controller::set_model_class_name('connector_out_setcateg');
configuration_connecteurs_controller::set_list_ui_class_name('list_configuration_connecteurs_categout_sets_ui');
configuration_connecteurs_controller::proceed($id);

?>