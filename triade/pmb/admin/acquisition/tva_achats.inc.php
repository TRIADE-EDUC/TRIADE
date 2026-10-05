<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tva_achats.inc.php,v 1.18 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

// gestion des comptes de tva achats
require_once("$class_path/tva_achats.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('tva_achats');
configuration_controller::set_list_ui_class_name('list_configuration_acquisition_tva_ui');
configuration_controller::proceed($id);
