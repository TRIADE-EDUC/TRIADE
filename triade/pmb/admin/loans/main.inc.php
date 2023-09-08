<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2016-04-29 14:14:38 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	default:
	case 'perso':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_loans_perso'], $admin_layout);
		echo window_title($database_window_title.$msg['admin_menu_loans_perso'].$msg['1003'].$msg['1001']);
		print $admin_layout;
		include("./admin/loans/perso.inc.php");
		break;
}
