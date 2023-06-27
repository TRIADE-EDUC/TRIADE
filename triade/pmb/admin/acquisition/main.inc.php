<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.12 2017-01-25 16:43:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

echo window_title($database_window_title.$msg[7].$msg[1003].$msg[1001]);

switch($sub) {
	case 'entite':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_entite'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_entite'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/entite.inc.php');
		break;
	case 'compta':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_compta'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_compta'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/comptabilite.inc.php');
		break;
	case 'type':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_type'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_type'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/types_produits.inc.php');
		break;
	case 'tva':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_tva'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_tva'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/tva_achats.inc.php');
		break;
	case 'frais':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_frais'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_frais'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/frais.inc.php');
		break;
	case 'mode':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_mode'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_mode'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/modes_paiements.inc.php');
		break;	
	case 'budget':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_budget'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_budget'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/budgets.inc.php');
		break;
	case 'categ':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_categ'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_categ'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/suggestions_categ.inc.php');
		break;
	case 'src':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_src'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_src'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/suggestions_src.inc.php');
		break;
	case 'lgstat':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_ref_lgstat'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_ref_lgstat'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/lgstat.inc.php');
		break;
	case 'pricing_systems':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_pricing_systems'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_pricing_systems'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/pricing_systems.inc.php');
		break;
	case 'account_types':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_account_types'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_account_types'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/account_types.inc.php');
		break;
	case 'thresholds':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['acquisition_menu_thresholds'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['acquisition_menu_thresholds'].$msg[1003].$msg[1001]);
		include('./admin/acquisition/thresholds.inc.php');
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout."<br />";
		echo window_title($database_window_title.$msg[7].$msg[1003].$msg[1001]);
		include("$include_path/messages/help/$lang/admin_acquisitions.txt");
		break;
}
?>
