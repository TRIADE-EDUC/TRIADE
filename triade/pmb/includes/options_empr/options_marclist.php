<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_marclist.php,v 1.10 2021/05/11 06:46:22 dgoron Exp $

global $base_path, $class_path;

//Gestion des options de type text
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";
include ($base_path."/includes/init.inc.php");

require_once ("$class_path/options/options_controller.class.php");
require_once ("$class_path/options/options_marclist.class.php");

options_controller::set_model_class_name('options_marclist');
options_controller::set_display_type('custom_field');
options_controller::proceed();