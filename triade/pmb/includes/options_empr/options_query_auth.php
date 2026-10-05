<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_query_auth.php,v 1.1 2021/05/10 07:25:40 dgoron Exp $

//Gestion des options de type intervalle de date
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";

include ($base_path."/includes/init.inc.php");

require_once ("$class_path/options/options_controller.class.php");
require_once ("$class_path/options/options_query_auth.class.php");

options_controller::set_model_class_name('options_query_auth');
options_controller::set_display_type('custom_field');
options_controller::proceed();