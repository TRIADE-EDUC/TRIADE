<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.14.10.1 2025/03/06 08:09:03 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'typdoc':
		include("./admin/docs/typ_doc.inc.php");
		break;
	case 'codstat':
		include("./admin/docs/cod_stat.inc.php");
		break;
	case 'location':
		include("./admin/docs/location.inc.php");
		break;
	case 'sur_location':
		include("./admin/docs/sur_location.inc.php");
		break;
	case 'section':
		include("./admin/docs/section.inc.php");
		break;
	case 'statut':
		include("./admin/docs/statut.inc.php");
		break;
	case 'lenders':
		include("./admin/docs/lender.inc.php");
		break;
	case 'perso':
		include("./admin/docs/perso.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_docs.txt");
		break;
}
