<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.inc.php,v 1.4 2020/11/05 10:22:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case "loans":
		break;
	case "orders":
	default:		
	    if(!isset($applied_sort)){
	        $applied_sort = array();
	    }
		$pnb_ui = new list_pnb_ui(array(), array(), $applied_sort);
		$pnb_ui->set_applied_sort_from_form();
		break;
}

switch($dest) {
	case "TABLEAU":
		$pnb_ui->get_display_spreadsheet_list();
		break;
	case "TABLEAUHTML":
		print $pnb_ui->get_display_html_list();
		break;
	default:
		print $pnb_ui->get_display_list();
		break;
}
