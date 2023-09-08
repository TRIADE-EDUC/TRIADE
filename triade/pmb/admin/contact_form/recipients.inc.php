<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recipients.inc.php,v 1.1 2016-05-26 13:52:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/contact_form/contact_form_recipients.class.php");

switch($action) {
	case 'save':
		$contact_form_recipients=new contact_form_recipients($mode);
		$contact_form_recipients->set_properties_from_form();
		$contact_form_recipients->save();
		print $contact_form_recipients->get_display_list();
		break;
	case 'add':
		switch ($mode) {
			case 'by_persons':
				$contact_form_recipients=new contact_form_recipients($mode);
				$contact_form_recipients->add();
				$contact_form_recipients->save();
				print $contact_form_recipients->get_display_list();
				break;
		}
		break;
	case 'delete':
		switch ($mode) {
			case 'by_persons':
				$contact_form_recipients=new contact_form_recipients($mode);
				$contact_form_recipients->delete($id);
				$contact_form_recipients->save();
				print $contact_form_recipients->get_display_list();
				break;
		}
		break;
	default:
		$contact_form_recipients=new contact_form_recipients($mode);
		print $contact_form_recipients->get_display_list();
		break;
}
