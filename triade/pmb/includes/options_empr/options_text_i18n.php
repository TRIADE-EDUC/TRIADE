<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_text_i18n.php,v 1.7 2021/05/10 07:03:49 dgoron Exp $

//Gestion des options de type text_i18n
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";
$base_use_dojo=1;
include ($base_path."/includes/init.inc.php");

require_once ("$class_path/options/options_controller.class.php");
require_once ("$class_path/options/options_text_i18n.class.php");

options_controller::set_model_class_name('options_text_i18n');
options_controller::set_display_type('custom_field');
options_controller::proceed();