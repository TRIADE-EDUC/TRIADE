<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.8 2017-01-25 16:43:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
    case 'abo':
		include_once("./dsi/bannettes/abo.inc.php");
		break;
    case 'infos':
		include_once("./dsi/bannettes/infos.inc.php");
		break;
    case 'pro':
		include_once("./dsi/bannettes/pro.inc.php");
		break;
    case 'facettes':
		include_once("./dsi/bannettes/bannette_facettes.inc.php");
		break;
    default:
		echo window_title($database_window_title.$msg['dsi_menu_title']);
        // include("$include_path/messages/help/$lang/dsi.txt");
        break;
}

