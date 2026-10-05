<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2021/02/08 10:30:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'emplacement':
		include("./admin/collstate/emplacement.inc.php");
		break;
	case 'support':
		include("./admin/collstate/support.inc.php");
		break;		
	case 'perso':
		include("./admin/collstate/perso.inc.php");
		break;
	case 'statut':
		include("./admin/collstate/statut.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_collstate.txt");
		break;
}
