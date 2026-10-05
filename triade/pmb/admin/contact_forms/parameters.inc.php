<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parameters.inc.php,v 1.1 2020/08/11 08:57:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $id;

if(!$id) $id = 1;

require_once($class_path."/contact_forms/contact_form_parameters.class.php");

switch($action) {
	case 'save':
		$contact_form_parameters=new contact_form_parameters($id);
		$contact_form_parameters->set_properties_from_form();
		$contact_form_parameters->save();
		print $contact_form_parameters->get_display_list();
		break;
	default:
		$contact_form_parameters=new contact_form_parameters($id);
		print $contact_form_parameters->get_display_list();
		break;
}
