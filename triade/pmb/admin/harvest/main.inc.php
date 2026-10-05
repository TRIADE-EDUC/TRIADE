<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2023/12/14 11:43:22 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
use Pmb\Harvest\Controller\HarvestController;

$controller = new HarvestController();
switch($sub) {
	case 'profil':
		$controller->proceedProfile($action);
		break;
	case 'profil_import':
		$controller->proceedProfileImport($action);
		break;
	default:
		include("$include_path/messages/help/$lang/admin_harvest.txt");
		break;
}
