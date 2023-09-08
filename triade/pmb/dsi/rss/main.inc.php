<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2017-01-25 16:43:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
    case 'definition':
    default:
		echo window_title($database_window_title.$msg['dsi_menu_flux']);
		include_once("./dsi/rss/rss.inc.php");
		break;
    }

