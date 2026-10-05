<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_query_list.php,v 1.22 2021/07/02 08:55:22 dgoron Exp $

//Gestion des otpions de type query_list

$base_path="../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";
include($base_path."/includes/init.inc.php");

require_once ("$class_path/options/options_controller.class.php");
require_once ("$class_path/options/options_query_list.class.php");

options_controller::set_model_class_name('options_query_list');
options_controller::set_display_type('custom_field');
options_controller::proceed();