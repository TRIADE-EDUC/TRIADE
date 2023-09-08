<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2017-01-25 16:43:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
    case 'classements':
    default:
		echo window_title($database_window_title.$msg['dsi_menu_title']);
		include_once("./dsi/options/classements.inc.php");
        break;
    }

