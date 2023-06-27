<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: instrument.inc.php,v 1.1 2015-01-12 15:32:17 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/nomenclature_instrument_admin.class.php");

switch($action) {
	case 'form':
		$nomenclature_instrument=new nomenclature_instrument_admin($id);
		print $nomenclature_instrument->get_form();
		break;
	case 'save':
		$nomenclature_instrument=new nomenclature_instrument_admin($id);
		print $nomenclature_instrument->save();
		$nomenclature_instruments=new nomenclature_instrument_admins();
		print $nomenclature_instruments->get_list();
		break;
	case 'delete':
		$nomenclature_instrument=new nomenclature_instrument_admin($id);
		print $nomenclature_instrument->delete();
		$nomenclature_instruments=new nomenclature_instrument_admins();
		print $nomenclature_instruments->get_list();
		break;
	default:
		$nomenclature_instruments=new nomenclature_instrument_admins();
		print $nomenclature_instruments->get_list();
		break;
}
