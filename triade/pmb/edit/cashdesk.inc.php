<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cashdesk.inc.php,v 1.3.8.1 2024/09/11 12:23:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $dest;


switch($dest) {
	case "TABLEAU":
		list_cashdesk_comptes_ui::get_instance()->get_display_spreadsheet_list();
		break;
	case "TABLEAUHTML":
	    print list_cashdesk_comptes_ui::get_instance()->get_display_html_list();
		exit;
		break;
	default:
	    print list_cashdesk_comptes_ui::get_instance()->get_display_list();
		break;
}
