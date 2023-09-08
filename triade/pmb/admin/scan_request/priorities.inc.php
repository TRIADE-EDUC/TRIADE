<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: priorities.inc.php,v 1.1 2016-01-07 07:48:53 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//dépendances
require_once($class_path.'/scan_request/scan_request_priorities.class.php');

$scan_request_priorities=new scan_request_priorities();

switch($action) {
	case 'save':
		$scan_request_priorities->save();
		print $scan_request_priorities->get_list();
		break;
	case 'add':
		print $scan_request_priorities->get_form(0);
		break;
	case 'edit':
		print $scan_request_priorities->get_form($id);
		break;
	case 'delete':
		$scan_request_priorities->delete($id);
		print $scan_request_priorities->get_list();
		break;
	default:
		print $scan_request_priorities->get_list();
		break;
}