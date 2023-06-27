<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.inc.php,v 1.3 2018-06-27 10:05:35 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($class_path."/list/list_pnb_ui.class.php");

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
