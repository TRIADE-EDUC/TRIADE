<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.11 2013-08-14 15:23:29 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'import':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg[500], $admin_layout);
		print $admin_layout;
		include("./admin/import/import_expl.inc.php");
		break;
	case 'import_expl':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg[520], $admin_layout);
		print $admin_layout;
		include("./admin/import/import_expl.inc.php");
		break;
	case 'pointage_expl':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg[569], $admin_layout);
		print $admin_layout;
		include("./admin/import/pointage_expl.inc.php");
		break;
	case 'import_skos':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["ontology_skos_admin_import"], $admin_layout);
		print $admin_layout;
		include("./admin/import/import_skos.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg[7].$msg[1003].$msg[1001]);
		include("$include_path/messages/help/$lang/admin_import.txt");
		break;
}

?>
