<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users.inc.php,v 1.5 2019-05-13 13:29:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$admin_layout = str_replace('!!menu_sous_rub!!', htmlentities($msg[26],ENT_QUOTES, $charset), $admin_layout);
print $admin_layout;
		

require_once('./admin/users/users_func.inc.php');
require_once($class_path.'/user.class.php');

print $admin_user_javascript;

switch($action) {
	case 'pwd':
		include("./admin/users/user_pwd.inc.php");
		break;
	case 'modif':
		include("./admin/users/user_modif.inc.php");
		break;
	case 'update':
		include("./admin/users/user_update.inc.php");
		break;
	case 'add':
		echo window_title($database_window_title.$msg[347].$msg[1003].$msg[1001]);
		$user = new user();
		print $user->get_user_form();
		echo form_focus('userform', 'form_login');
		break;
	case 'del':
		include("./admin/users/user_del.inc.php");
		break;
	case 'duplicate':
		include("./admin/users/user_duplicate.inc.php");
		break;
	default:
		echo window_title($database_window_title.$msg[25].$msg[1003].$msg[1001]);
		show_users();
		break;
}
?>