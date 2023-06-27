<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests.inc.php,v 1.1 2016-01-15 15:37:39 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/scan_request/scan_requests.class.php');

switch($action) {

	default:
		$scan_requests = new scan_requests();
		print $scan_requests->get_display_list();
		break;
}
