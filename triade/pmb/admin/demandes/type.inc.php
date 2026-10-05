<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: type.inc.php,v 1.5 2021/01/21 08:52:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id, $id_liste;

require_once($class_path."/demandes_type.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('demandes_type');
configuration_controller::set_list_ui_class_name('list_configuration_demandes_type_ui');
configuration_controller::proceed((!empty($id) ? $id : $id_liste));