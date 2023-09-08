<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2017-07-10 15:50:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'manager':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['planificateur_admin_manager'], $admin_layout);
		print $admin_layout;
		include("./admin/planificateur/manager.inc.php");
		break;
	case 'reporting':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['planificateur_admin_reporting'], $admin_layout);
		print $admin_layout;
		include("./admin/planificateur/reporting.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg[7].$msg[1003].$msg[1001]);
		include("$include_path/messages/help/$lang/admin_planificateur.txt");
		break;
}

