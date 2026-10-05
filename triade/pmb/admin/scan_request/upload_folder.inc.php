<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: upload_folder.inc.php,v 1.2 2022/03/31 14:17:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $msg;

//dépendances
require_once($class_path.'/scan_request/scan_requests.class.php');

switch($action) {
	case 'update':
	case 'save':
		if(scan_requests::save_admin_form()){
			print '<div class="erreur">'.$msg['move_saved_ok'].'</div>';
		}else{
			print '<div class="erreur">'.$msg['histo_save_fail'].'</div>';
		}
		print scan_requests::get_admin_form();
		break;
	default:
		print scan_requests::get_admin_form();
		break;
}