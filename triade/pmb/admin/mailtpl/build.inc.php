<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: build.inc.php,v 1.2 2017-08-29 12:20:10 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/mailtpl.class.php");

switch($action) {
	case 'form':
		$mailtpl=new mailtpl($id_mailtpl);
		print $mailtpl->get_form();
	break;
	case 'save':
		$mailtpl=new mailtpl($id_mailtpl);
		$data['name']=$name;
		$data['objet']=$f_objet_mail;
		$data['tpl']=$f_message;
		$data['users']=$userautorisation;
		print $mailtpl->save($data);
		$mailtpls=new mailtpls();
		print $mailtpls->get_list();
	break;	
	case 'delete':
		$mailtpl=new mailtpl($id_mailtpl);
		print $mailtpl->delete();
		$mailtpls=new mailtpls();
		print $mailtpls->get_list();
	break;
	case 'duplicate':
		$mailtpl=new mailtpl($id_mailtpl);
		print $mailtpl->get_form(true);
		break;

	default:
		$mailtpls=new mailtpls();
		print $mailtpls->get_list();
	break;
}
