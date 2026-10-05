<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.8 2021/02/08 10:30:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'origins':
		include("./admin/authorities/origins.inc.php");
		break;
	case 'perso':
		include("./admin/authorities/perso.inc.php");
		break;
	case 'authperso':
		include("./admin/authorities/authperso.inc.php");
		break;
	case 'templates':
		include("./admin/authorities/auth_templates.inc.php");
		break;
	case 'statuts' :
		include("./admin/authorities/statuts.inc.php");
		break;		
	default:
		include("$include_path/messages/help/$lang/admin_authorities.txt");
		break;
}
