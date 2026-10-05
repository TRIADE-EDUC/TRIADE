<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: general.inc.php,v 1.8 2022/04/28 12:21:01 dgoron Exp $

//Administration générale des droits des services externes

global $class_path, $include_path;
global $es_admin_general;

// require_once($class_path."/external_services.class.php");
require_once($class_path."/list/configuration/external_services/list_configuration_external_services_general_ui.class.php");
require_once($include_path."/templates/external_services.tpl.php");

$table_rights = list_configuration_external_services_general_ui::get_instance()->get_display_list();

$interface_form = new interface_admin_form('es_rights');
$interface_form->set_label("D&eacute;finition des droits pour les groupes et les m&eacute;thodes");
$interface_form->set_content_form(str_replace("!!table_rights!!",$table_rights,$es_admin_general));
print $interface_form->get_display_parameters();
?>