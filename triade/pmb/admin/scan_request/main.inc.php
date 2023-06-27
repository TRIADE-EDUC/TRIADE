<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2016-01-25 10:21:08 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'status' :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_scan_request_status'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_scan_request_status'].$msg[1003].$msg[1001]);
		include("./admin/scan_request/status.inc.php");
		break;		
	case 'workflow' :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_scan_request_workflow'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_scan_request_workflow'].$msg[1003].$msg[1001]);
		include("./admin/scan_request/workflow.inc.php");
		break;	
	case 'priorities' :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_scan_request_priorities'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_scan_request_priorities'].$msg[1003].$msg[1001]);
		include("./admin/scan_request/priorities.inc.php");
		break;
	case 'upload_folder':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['upload_folder_storage'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['upload_folder_storage'].$msg[1003].$msg[1001]);
		include("./admin/scan_request/upload_folder.inc.php");		
		break;	
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_scan_request'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title. $msg['admin_scan_request'].$msg[1003].$msg[1001]);
		include("$include_path/messages/help/$lang/admin_scan_request.txt");
		break;
}
