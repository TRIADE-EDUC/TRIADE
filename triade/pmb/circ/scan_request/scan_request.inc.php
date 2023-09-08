<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request.inc.php,v 1.6 2019-05-23 07:01:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/scan_request/scan_request.class.php');

$id = intval($id);
$scan_request = new scan_request($id);

switch($action) {
	case 'save':
		$scan_request->get_values_from_form();
		$scan_request->save();
		$action = "";
		print '<META HTTP-EQUIV="Refresh" Content="0; URL='.$base_path.'/circ.php?categ=scan_request&sub=list">'; 
		exit;
		break;
	case 'edit':
		print $scan_request->get_form();
		break;
	case 'delete':
		$scan_request->delete();
		require_once('./circ/scan_request/scan_requests.inc.php');	
		break;
	default:
		print $scan_request->get_form();
		break;
}
