<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2021/02/08 10:30:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'theme':
		include("./admin/faq/theme.inc.php");		
		break;
	case 'type':
		include("./admin/faq/type.inc.php");		
		break;
	default:
		break;
}
?>