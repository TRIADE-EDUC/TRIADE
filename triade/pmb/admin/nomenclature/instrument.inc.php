<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: instrument.inc.php,v 1.2 2021/01/28 08:54:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;
require_once($class_path."/nomenclature/nomenclature_instrument.class.php");
require_once($class_path."/configuration/configuration_nomenclature_controller.class.php");

configuration_nomenclature_controller::set_model_class_name('nomenclature_instrument');
configuration_nomenclature_controller::set_list_ui_class_name('list_configuration_nomenclatures_instruments_ui');
configuration_nomenclature_controller::proceed($id);