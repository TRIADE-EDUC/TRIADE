<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emplacement.inc.php,v 1.7 2021/01/22 15:23:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path."/arch_emplacement.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('arch_emplacement');
configuration_controller::set_list_ui_class_name('list_configuration_collstate_emplacement_ui');
configuration_controller::proceed($id);