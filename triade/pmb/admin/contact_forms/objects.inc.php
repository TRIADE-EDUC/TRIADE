<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: objects.inc.php,v 1.3 2021/03/25 09:33:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $id, $num_contact_form;

$id = intval($id);
$num_contact_form = intval($num_contact_form);
if(!$num_contact_form) $num_contact_form = 1;

require_once($class_path."/contact_forms/contact_form_object.class.php");

switch($action) {
	case 'edit':
		$contact_form_object=new contact_form_object($id);
		if(!$id && !empty($num_contact_form)) {
		    $contact_form_object->set_num_contact_form($num_contact_form);
		}
		print $contact_form_object->get_form();
		break;
	case 'save':
		$contact_form_object=new contact_form_object($id);
		if(!$id && !empty($num_contact_form)) {
		    $contact_form_object->set_num_contact_form($num_contact_form);
		}
		$contact_form_object->set_properties_from_form();
		$contact_form_object->save();
		print list_contact_forms_objects_ui::get_instance(array('num_contact_form' => $num_contact_form))->get_display_list();
		break;
	case 'delete':
		$contact_form_object=new contact_form_object($id);
		$contact_form_object->delete();
		print list_contact_forms_objects_ui::get_instance(array('num_contact_form' => $num_contact_form))->get_display_list();
		break;
	default:
		print list_contact_forms_objects_ui::get_instance(array('num_contact_form' => $num_contact_form))->get_display_list();
		break;
}
