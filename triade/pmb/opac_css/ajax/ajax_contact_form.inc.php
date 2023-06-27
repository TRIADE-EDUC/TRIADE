<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_contact_form.inc.php,v 1.3 2018-09-18 09:08:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/contact_form/contact_form.class.php");
require_once($class_path."/encoding_normalize.class.php");
switch($sub){
	case 'form':
		switch ($action){
			case 'send_mail':
				$contact_form = new contact_form();
				$form_fields = json_decode(encoding_normalize::utf8_normalize(stripslashes($form_fields)));
				if($charset != 'utf-8') {
					$form_fields = (object) pmb_utf8_decode($form_fields);
				}
				$contact_form->set_form_fields($form_fields);
				if($contact_form->check_form()) {
					$contact_form->send_mail();
				}
				print encoding_normalize::json_encode(array('sended' => $contact_form->is_sended(), 'messages' => $contact_form->get_messages()));
				break;
		}
		break;
}
?>