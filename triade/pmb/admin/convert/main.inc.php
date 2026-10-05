<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.11 2021/03/15 09:02:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$include_path/templates/export_param.tpl.php");

switch($sub) {
	case 'import':
		include("./admin/convert/import.inc.php");
		break;
	case 'export':
		include("./admin/convert/export.inc.php");
		break;
	case 'paramopac':
		include("./admin/convert/export_param.php");
		break;
	case 'paramgestion':
		include("./admin/convert/export_param.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_convert.txt");
		break;
}
