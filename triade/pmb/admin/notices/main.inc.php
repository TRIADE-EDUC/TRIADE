<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.12 2021/02/08 10:30:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'orinot':
		include("./admin/notices/origine_notice.inc.php");
		break;
	case 'perso':
		include("./admin/notices/perso.inc.php");
		break;
	case 'map_echelle':
		include("./admin/notices/map_echelle.inc.php");
		break;
	case 'map_projection':
		include("./admin/notices/map_projection.inc.php");
		break;
	case 'map_ref':
		include("./admin/notices/map_ref.inc.php");
		break;
	case 'statut':
		include("./admin/notices/statut.inc.php");
		break;
	case 'onglet':
		include("./admin/notices/onglet.inc.php");
		break;
	case 'notice_usage':
		include("./admin/notices/notice_usage.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_notices.txt");
		break;
}
