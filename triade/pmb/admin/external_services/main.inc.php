<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2021/02/08 10:30:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'general':
		include('./admin/external_services/general.inc.php');
		break;
	case 'peruser':
		include('./admin/external_services/peruser.inc.php');
		break;
	case 'esusers':
		include('./admin/external_services/esusers.inc.php');
		break;
	case 'esusergroups':
		include('./admin/external_services/esusergroups.inc.php');
		break;
/*	case 'es_tests':
		include('./admin/external_services/tests.inc.php');
		break;*/
	default:
		include("$include_path/messages/help/$lang/admin_external_services.txt");
		break;
}
?>
