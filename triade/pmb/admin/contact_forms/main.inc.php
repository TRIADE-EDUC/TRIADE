<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2021/02/09 07:25:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch formulaire de contact
global $class_path, $sub, $id;

require_once ($class_path."/contact_forms/contact_forms_controller.class.php");

switch($sub) {
	case 'objects':
		include("./admin/contact_forms/objects.inc.php");
		break;
	case 'recipients':
		include("./admin/contact_forms/recipients.inc.php");
		break;
	case 'parameters':
		include("./admin/contact_forms/parameters.inc.php");
		break;
	default :
		contact_forms_controller::proceed($id);
		break;
}