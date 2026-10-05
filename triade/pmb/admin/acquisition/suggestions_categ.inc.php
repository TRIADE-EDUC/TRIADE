<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_categ.inc.php,v 1.12 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion des listes de suggestions
global $class_path, $id;
require_once("$class_path/suggestions_categ.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('suggestions_categ');
configuration_controller::set_list_ui_class_name('list_configuration_acquisition_categ_ui');
configuration_controller::proceed($id);