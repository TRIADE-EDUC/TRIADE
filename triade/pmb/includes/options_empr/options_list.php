<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_list.php,v 1.35.4.1 2025/02/12 12:34:06 dbellamy Exp $

global $base_path, $base_auth, $base_title, $include_path, $first, $options, $param, $MULTIPLE, $AUTORITE, $CHECKBOX, $NUM_AUTO, $VALUE, $ITEM;
global $ORDRE, $msg, $idchamp, $_custom_prefixe_, $UNSELECT_ITEM_VALUE, $UNSELECT_ITEM_LIB, $DEFAULT_VALUE, $CHECKBOX_NB_ON_LINE, $name, $charset;
global $current_module, $type_list_empr, $type, $DEL;

//Gestion des options de type list
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";
$base_use_dojo = 1;

include($base_path."/includes/init.inc.php");

require_once ("$class_path/options/options_controller.class.php");
require_once ("$class_path/options/options_list.class.php");

function tonum($n) {
    return intval($n);
}

options_controller::set_model_class_name('options_list');
options_controller::set_display_type('custom_field');
options_controller::proceed();
