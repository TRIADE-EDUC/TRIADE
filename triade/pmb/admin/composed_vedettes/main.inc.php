<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2021/02/08 10:30:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'schemes' :
		include("./admin/composed_vedettes/schemes.inc.php");
		break;
	case 'grammars' :
	default:
		include("./admin/composed_vedettes/grammars.inc.php");
		break;
}