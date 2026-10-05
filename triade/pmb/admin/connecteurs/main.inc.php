<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.7.10.1 2025/04/16 12:16:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

switch($sub) {
	case 'in':
		include('./admin/connecteurs/in.inc.php');
		break;
	case 'out':
		include('./admin/connecteurs/out.inc.php');
		break;
	case 'categ':
		include('./admin/connecteurs/categ.inc.php');
		break;
	case 'out_sets':
		include('./admin/connecteurs/out_sets.inc.php');
		break;
	case 'categout_sets':
		include('./admin/connecteurs/out_set_categ.inc.php');
		break;
	case 'out_auth':
		include('./admin/connecteurs/out_auth.inc.php');
		break;
	case 'enrichment' :
		include('./admin/connecteurs/enrichment.inc.php');
		break;
	default:
		include("$include_path/messages/help/$lang/admin_connecteurs.txt");
		break;
}

