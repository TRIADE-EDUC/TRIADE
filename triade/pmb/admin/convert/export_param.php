<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export_param.php,v 1.4 2021/03/15 09:02:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $class_path, $action, $sub, $form_param, $msg;

require_once("$include_path/templates/export_param.tpl.php");
require_once("$class_path/export_param.class.php");

switch ($action) {
	case 'update':
		if ($sub == 'paramopac') {
			$export_param_context = new export_param(EXP_GLOBAL_CONTEXT);
			$export_param_context->get_parametres(EXP_DEFAULT_OPAC);
			$export_param_context->update();
			$export_param_context->check_default_param();
		} elseif ($sub == 'paramgestion') {
			$export_param_context = new export_param(EXP_GLOBAL_CONTEXT);
			$export_param_context->get_parametres(EXP_DEFAULT_GESTION);
			$export_param_context->update();
			$export_param_context->check_default_param();
		}
		$action = '';
		break;
	default:
		if ($sub == 'paramopac') {
			$export_param_opac = new export_param(EXP_DEFAULT_OPAC);
			$export_param_opac->check_default_param();
		} else {
			$export_param_gestion = new export_param(EXP_DEFAULT_GESTION);
			$export_param_gestion->check_default_param();
		}
		break;
}
$interface_form = new interface_admin_form('export_param_form');
if ($sub == 'paramopac') {
	$interface_form->set_label($msg["admin_param_export_opac"]);
} else {
	$interface_form->set_label($msg["admin_param_export_gestion"]);
}
$interface_form->set_content_form($form_param);
print $interface_form->get_display_parameters();
?>