<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2018-06-25 15:30:44 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case "quotas_simultaneous_loans" :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_pnb_quotas_simultaneous_loans"], $admin_layout);
		print $admin_layout;
		include($base_path."/admin/pnb/quotas_simultaneous_loans.inc.php");
		break;
	case "quotas_prolongation" :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_pnb_quotas_prolongation"], $admin_layout);
		print $admin_layout;
		include($base_path."/admin/pnb/quotas_prolongation.inc.php");
		break;
	case "drm_parameters" :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_pnb_drm_parameters_title"], $admin_layout);
		print $admin_layout;
		include($base_path."/admin/pnb/drm_parameters.inc.php");
		break;
	default:
	case 'param' :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_pnb_param'], $admin_layout);
		echo window_title($database_window_title.$msg['admin_menu_pnb_param'].$msg['1003'].$msg['1001']);
		print $admin_layout;
		include("./admin/pnb/param.inc.php");
		break;
}
