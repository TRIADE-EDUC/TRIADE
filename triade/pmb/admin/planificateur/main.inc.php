<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5 2023/03/16 14:16:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub, $include_path, $lang;

switch($sub) {
	case 'manager':
		include("./admin/planificateur/manager.inc.php");
		break;
	case 'reporting':
		include("./admin/planificateur/reporting.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_planificateur.txt");
		break;
}

