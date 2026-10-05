<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.6 2021/02/08 10:30:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'build':
		include("./admin/mailtpl/build.inc.php");		
		break;
	case 'img':
		include("./admin/mailtpl/img.inc.php");		
		break;
	case 'attachments':
		include("./admin/mailtpl/attachments.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_mailtpl.txt");
		break;
}
?>