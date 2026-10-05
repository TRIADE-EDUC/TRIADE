<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main_ajax.inc.php,v 1.3 2019/06/05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $moyen;

switch ($moyen) {
	case 'douchette':
		include ("./catalog/caddie/pointage/douchette_ajax.inc.php");
	break;
	case 'manu':
		include ("./catalog/caddie/pointage/manu_ajax.inc.php");
	break;
	default:

	break;
}
