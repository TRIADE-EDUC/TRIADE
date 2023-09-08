<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2019-03-26 14:05:19 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'schemes' :
		print $admin_layout;
		include("./admin/composed_vedettes/schemes.inc.php");
		break;
	case 'grammars' :
	default:
		print $admin_layout;
		include("./admin/composed_vedettes/grammars.inc.php");
		break;
}