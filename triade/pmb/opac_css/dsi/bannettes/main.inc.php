<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1 2021/04/12 10:38:43 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub;

switch ($sub) {
    case 'facettes':
		include_once "./dsi/bannettes/bannette_facettes.inc.php";
		break;
    default:
        break;
}