<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2022/12/26 13:19:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $lang, $sub, $ac;

require_once("$class_path/acces.class.php");

//recuperation de la liste des domaines d'acces
$ac = new acces();

switch ($sub) {
	case 'domain' :
		require_once("./admin/acces/domain.inc.php");
		break;
	case 'user_prf' :
		require_once("./admin/acces/user_prf.inc.php");
		break;
	case 'res_prf' :
		require_once("./admin/acces/res_prf.inc.php");
		break;
	default :
		require_once("$include_path/messages/help/$lang/admin_acces.txt");
		break;
}			
?>