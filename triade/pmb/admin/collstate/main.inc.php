<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2016-04-29 14:14:38 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'emplacement':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_collstate_emplacement'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_menu_collstate_emplacement'].$msg['1003'].$msg['1001']);
		include("./admin/collstate/emplacement.inc.php");
		break;
	case 'support':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_collstate_support'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_menu_collstate_support'].$msg['1003'].$msg['1001']);
		include("./admin/collstate/support.inc.php");
		break;		
	case 'perso':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_collstate_perso'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_menu_collstate_perso'].$msg['1003'].$msg['1001']);
		include("./admin/collstate/perso.inc.php");
		break;
	case 'statut':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_collstate_statut'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_menu_collstate_statut'].$msg['1003'].$msg['1001']);
		include("./admin/collstate/statut.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_menu_collstate'].$msg['1003'].$msg['1001']);
		include("$include_path/messages/help/$lang/admin_collstate.txt");
		break;
}
