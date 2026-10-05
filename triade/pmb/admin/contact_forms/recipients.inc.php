<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recipients.inc.php,v 1.2 2020/12/16 08:02:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $id, $mode, $recipient_key;

if(!$id) $id = 1;

require_once($class_path."/contact_forms/contact_form_recipients.class.php");

switch($action) {
	case 'save':
		$contact_form_recipients=new contact_form_recipients($id, $mode);
		$contact_form_recipients->set_properties_from_form();
		$contact_form_recipients->save();
		print $contact_form_recipients->get_display_list();
		break;
	case 'add':
		switch ($mode) {
			case 'by_persons':
				$contact_form_recipients=new contact_form_recipients($id, $mode);
				$contact_form_recipients->add();
				$contact_form_recipients->save();
				print $contact_form_recipients->get_display_list();
				break;
		}
		break;
	case 'delete':
		switch ($mode) {
			case 'by_persons':
				$contact_form_recipients=new contact_form_recipients($id, $mode);
				$contact_form_recipients->delete($recipient_key);
				$contact_form_recipients->save();
				print $contact_form_recipients->get_display_list();
				break;
		}
		break;
	default:
		$contact_form_recipients=new contact_form_recipients($id, $mode);
		print $contact_form_recipients->get_display_list();
		break;
}
