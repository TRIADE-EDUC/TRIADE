<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1 2014-04-01 13:45:46 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'theme':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["faq_theme"], $admin_layout);
		print $admin_layout;
		include("./admin/faq/theme.inc.php");		
		break;
	case 'type':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["faq_type"], $admin_layout);
		print $admin_layout;
		include("./admin/faq/type.inc.php");		
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg[131].$msg[1003].$msg[1001]);
		break;
}
?>