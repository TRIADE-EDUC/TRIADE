<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2021/02/08 10:30:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'status' :
		include("./admin/scan_request/status.inc.php");
		break;		
	case 'workflow' :
		include("./admin/scan_request/workflow.inc.php");
		break;	
	case 'priorities' :
		include("./admin/scan_request/priorities.inc.php");
		break;
	case 'upload_folder':
		include("./admin/scan_request/upload_folder.inc.php");		
		break;	
	default:
		include("$include_path/messages/help/$lang/admin_scan_request.txt");
		break;
}
