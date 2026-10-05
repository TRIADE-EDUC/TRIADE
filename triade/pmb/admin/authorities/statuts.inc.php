<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statuts.inc.php,v 1.5 2021/01/21 07:42:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;
// Page de gestion des statuts d'autorités

//dépendances
require_once($class_path.'/authorities_statut.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('authorities_statut');
configuration_controller::set_list_ui_class_name('list_configuration_authorities_statuts_ui');
configuration_controller::proceed($id);