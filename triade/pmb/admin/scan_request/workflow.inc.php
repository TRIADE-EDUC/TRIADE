<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: workflow.inc.php,v 1.3 2021/01/20 07:27:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//dépendances
require_once($class_path.'/scan_request/scan_request_workflow.class.php');

$scan_request_workflow=new scan_request_workflow();

switch($action) {
	case 'save':
		$scan_request_workflow->save();
		print '<h2>'.$msg['admin_scan_request_workflow_successfully_saved'].'<h2>';
		print $scan_request_workflow->get_form();
		break;
	default:
		print $scan_request_workflow->get_form();
		break;
}