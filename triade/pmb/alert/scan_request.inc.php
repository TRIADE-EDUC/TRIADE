<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request.inc.php,v 1.4 2019-05-29 12:12:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $aff_alerte, $msg, $class_path;

require_once($class_path.'/scan_request/scan_requests.class.php');

function scan_requests_to_validate () {
	global $msg;
	global $status_search, $opac_scan_request_create_status;
	
	$restore_status_search = $status_search;	
	$status_search = $opac_scan_request_create_status;
	
	$scan_requests = new scan_requests(false);
	$requests = $scan_requests->get_scan_requests();
	
	$aff = "";
	if($nb = count($requests)){
		 $aff .= "<li><a href='./circ.php?categ=scan_request&sub=list&status_search=".$status_search."' target='_parent'>".$msg['alerte_scan_requests_to_validate']." (".$nb.")</a></li>" ;
	}
	$status_search = $restore_status_search;
	return $aff;
}

$temp_aff = scan_requests_to_validate();
if ($temp_aff) $aff_alerte .= "<ul>".$msg["alerte_scan_requests"].$temp_aff."</ul>" ;