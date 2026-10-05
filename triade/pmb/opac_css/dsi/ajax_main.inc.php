<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.1 2021/04/12 10:38:43 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $categ, $sub, $action;

switch ($categ) {	
	case 'bannettes':
		switch ($sub) {
			default:
				switch ($action) {
					default:
						include "./dsi/bannettes/main.inc.php";
						break;
				}
				break;
		}
		break;		
	break;
	default:
    	break;		
}