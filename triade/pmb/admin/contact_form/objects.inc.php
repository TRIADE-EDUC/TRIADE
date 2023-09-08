<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: objects.inc.php,v 1.1 2016-05-26 13:52:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/contact_form/contact_form_objects.class.php");
require_once($class_path."/contact_form/contact_form_object.class.php");

switch($action) {
	case 'edit':
		$contact_form_object=new contact_form_object($id);
		print $contact_form_object->get_form();
		break;
	case 'save':
		$contact_form_object=new contact_form_object($id);
		$contact_form_object->set_properties_from_form();
		$contact_form_object->save();
		$contact_form_objects=new contact_form_objects();
		print $contact_form_objects->get_display_list();
		break;
	case 'delete':
		$contact_form_object=new contact_form_object($id);
		$contact_form_object->delete();
		$contact_form_objects=new contact_form_objects();
		print $contact_form_objects->get_display_list();
		break;
	default:
		$contact_form_objects=new contact_form_objects();
		print $contact_form_objects->get_display_list();
		break;
}
