<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.12.10.1 2025/04/16 12:16:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $sub, $include_path, $lang;

switch($sub) {
	case 'import':
	case 'import_expl':
		include("./admin/import/import_expl.inc.php");
		break;
	case 'pointage_expl':
		include("./admin/import/pointage_expl.inc.php");
		break;
	case 'import_skos':
		include("./admin/import/import_skos.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_import.txt");
		break;
}

