<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5 2016-03-30 13:13:27 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'family':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_nomenclature_family"], $admin_layout);
		print $admin_layout;
		include("./admin/nomenclature/family.inc.php");		
		break;
	case 'formation':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_nomenclature_formation"], $admin_layout);
		print $admin_layout;
		include("./admin/nomenclature/formation.inc.php");		
		break;
	case 'voice':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_nomenclature_voice"], $admin_layout);
		print $admin_layout;
		include("./admin/nomenclature/voice.inc.php");		
		break;
	case 'instrument':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_nomenclature_instruments"], $admin_layout);
		print $admin_layout;
		include("./admin/nomenclature/instrument.inc.php");
		break;
	case 'material':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_nomenclature_material"], $admin_layout);
		print $admin_layout;
		include("./admin/nomenclature/material.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg[131].$msg[1003].$msg[1001]);
		break;
}
?>