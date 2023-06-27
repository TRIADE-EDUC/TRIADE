<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1 2016-05-26 13:52:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch formulaire de contact

switch($sub) {
	case 'objects':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_opac_contact_form_objects"], $admin_layout);
		print $admin_layout;
		include("./admin/contact_form/objects.inc.php");
		break;
	case 'recipients':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_opac_contact_form_recipients'], $admin_layout);
		print $admin_layout;
		include("./admin/contact_form/recipients.inc.php");
		break;
	default :
	case 'parameters':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_opac_contact_form_parameters'], $admin_layout);
		print $admin_layout;
		include("./admin/contact_form/parameters.inc.php");
		break;
}