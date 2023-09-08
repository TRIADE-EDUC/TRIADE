<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parameters.inc.php,v 1.1 2016-05-26 13:52:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/contact_form/contact_form_parameters.class.php");

switch($action) {
	case 'save':
		$contact_form_parameters=new contact_form_parameters();
		$contact_form_parameters->set_properties_from_form();
		$contact_form_parameters->save();
		print $contact_form_parameters->get_display_list();
		break;
	default:
		$contact_form_parameters=new contact_form_parameters();
		print $contact_form_parameters->get_display_list();
		break;
}
