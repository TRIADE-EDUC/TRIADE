<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.11 2016-11-03 10:21:40 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'orinot':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['orinot_origine'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['orinot_origine'].$msg['1003'].$msg['1001']);
		include("./admin/notices/origine_notice.inc.php");
		break;
	case 'perso':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_noti_perso'], $admin_layout);
		echo window_title($database_window_title.$msg['admin_menu_noti_perso'].$msg['1003'].$msg['1001']);
		print $admin_layout;
		include("./admin/notices/perso.inc.php");
		break;
	case 'map_echelle':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_noti_map_echelle'], $admin_layout);
		print $admin_layout;
		include("./admin/notices/map_echelle.inc.php");
		break;
	case 'map_projection':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_noti_map_projection'], $admin_layout);
		print $admin_layout;
		include("./admin/notices/map_projection.inc.php");
		break;
	case 'map_ref':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_noti_map_ref'], $admin_layout);
		print $admin_layout;
		include("./admin/notices/map_ref.inc.php");
		break;
	case 'statut':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_noti_statut'], $admin_layout);
		echo window_title($database_window_title.$msg['admin_menu_noti_statut'].$msg['1003'].$msg['1001']);
		print $admin_layout;
		include("./admin/notices/statut.inc.php");
		break;
	case 'onglet':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_noti_onglet'], $admin_layout);
		echo window_title($database_window_title.$msg['admin_menu_noti_onglet'].$msg['1003'].$msg['1001']);
		print $admin_layout;
		include("./admin/notices/onglet.inc.php");
		break;
	case 'notice_usage':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_notice_usage'], $admin_layout);
		echo window_title($database_window_title.$msg['admin_menu_notice_usage'].$msg['1003'].$msg['1001']);
		print $admin_layout;
		include("./admin/notices/notice_usage.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_menu_notices'].$msg['1003'].$msg['1001']);
		include("$include_path/messages/help/$lang/admin_notices.txt");
		break;
}
