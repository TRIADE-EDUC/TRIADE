<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sur_location.inc.php,v 1.2 2021/01/21 12:31:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

// gestion des sur-localisations
require_once("$class_path/sur_location.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('sur_location');
configuration_controller::set_list_ui_class_name('list_configuration_docs_sur_location_ui');
configuration_controller::proceed($id);