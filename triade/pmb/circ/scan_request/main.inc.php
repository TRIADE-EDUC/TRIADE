<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2016-10-07 13:59:00 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub){	
	case 'request':
		 require_once('./circ/scan_request/scan_request.inc.php');		
		break;
	case 'list':
		if($action == "clean_filters" ){
			unset( $_SESSION['scan_requests_filter']);
		}
		require_once('./circ/scan_request/scan_requests.inc.php');	
		break;
	default:
		break;
}
