<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere.inc.php,v 1.22 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $action, $idetagere;

switch ($action) {
	case 'new_etagere':
		$myEtagere = new etagere();
		print $myEtagere->get_form();
		break;
	case 'edit_etagere':
		$myEtagere = new etagere($idetagere);
		print $myEtagere->get_form();
		break;
	case 'del_etagere':
		$myEtagere= new etagere($idetagere);
		$myEtagere->delete();
		aff_etagere("edit_etagere",1);
		break;
	case 'save_etagere':
		$myEtagere= new etagere($idetagere);
		$myEtagere->set_properties_from_form();
		$myEtagere->save_etagere();
		aff_etagere("edit_etagere",1);
		break;
	case 'valid_new_etagere':
		$myEtagere = new etagere(0);
		$myEtagere->create_etagere();
		$myEtagere->set_properties_from_form();
		$myEtagere->save_etagere();
		aff_etagere("edit_etagere",1);
		break;
	default:
		aff_etagere("edit_etagere",1);
	}
