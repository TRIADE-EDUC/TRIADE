<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: status.inc.php,v 1.2 2016-01-14 15:15:19 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//dépendances
require_once($class_path.'/scan_request/scan_request_admin_status.class.php');

$scan_request_status=new scan_request_admin_status();

switch($action) {
	case 'save':
		$scan_request_status->save();
		print $scan_request_status->get_list();
		break;
	case 'add':
		print $scan_request_status->get_form(0);
		break;
	case 'edit':
		print $scan_request_status->get_form($id);
		break;
	case 'delete':
		$scan_request_status->delete($id);
		print $scan_request_status->get_list();
		break;
	default:
		print $scan_request_status->get_list();
		break;
}