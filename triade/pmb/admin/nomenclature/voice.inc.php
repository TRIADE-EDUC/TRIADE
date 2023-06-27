<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: voice.inc.php,v 1.1 2015-01-20 14:08:53 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/nomenclature_voice_admin.class.php");

switch($action) {
	case 'form':
		$nomenclature_voice=new nomenclature_voice_admin($id);
		print $nomenclature_voice->get_form();
		break;
	case 'save':
		$nomenclature_voice=new nomenclature_voice_admin($id);
		print $nomenclature_voice->save();
		$nomenclature_voice=new nomenclature_voice_admins();
		print $nomenclature_voice->get_list();
		break;
	case 'delete':
		$nomenclature_voice=new nomenclature_voice_admin($id);
		print $nomenclature_voice->delete();
		$nomenclature_voice=new nomenclature_voice_admins();
		print $nomenclature_voice->get_list();
		break;
	case "up":		
		$nomenclature_voice=new nomenclature_voice_admins();
		$nomenclature_voice->order_up($id);
		print $nomenclature_voice->get_list();
		break;
	case "down":
		$nomenclature_voice=new nomenclature_voice_admins();
		$nomenclature_voice->order_down($id);
		print $nomenclature_voice->get_list();
		break;
	default:
		$nomenclature_voice=new nomenclature_voice_admins();
		print $nomenclature_voice->get_list();
		break;
}
