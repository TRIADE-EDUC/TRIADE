<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2017-10-13 13:31:05 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'build':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_mailtpl_title"], $admin_layout);
		print $admin_layout;
		include("./admin/mailtpl/build.inc.php");		
	break;
	case 'print_cart_tpl':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_print_cart_tpl_title"], $admin_layout);
		print $admin_layout;
		include("./admin/mailtpl/print_cart_tpl.inc.php");		
	break;
	case 'img':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_mailtpl_img_menu"], $admin_layout);
		print $admin_layout;
		include("./admin/mailtpl/img.inc.php");		
	break;
	
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg[131].$msg[1003].$msg[1001]);
		include("$include_path/messages/help/$lang/admin_mailtpl.txt");
	break;
}
?>