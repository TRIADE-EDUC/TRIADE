<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_list.inc.php,v 1.53 2019-04-26 15:49:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($class_path."/list/readers/list_readers_edition_ui.class.php");
include_once($include_path."/templates/empr.tpl.php");
require_once($base_path."/circ/empr/empr_func.inc.php");
require_once($class_path."/emprunteur.class.php");

switch($sub) {
	case "limite" :
		$list_readers_edition_ui = new list_readers_edition_ui(array('date_expiration_start' => date('Y-m-d'), 'date_expiration_end' =>'', 'date_expiration_limit' => '((to_days(empr_date_expiration) - to_days(now()) ) <=  '.$pmb_relance_adhesion.' )', 'change_categ' => ''));
		break;
	case "depasse" :
		$list_readers_edition_ui = new list_readers_edition_ui(array('date_expiration_start' => '', 'date_expiration_end' => date('Y-m-d'), 'date_expiration_limit' => '', 'change_categ' => ''));
		break;
	case "categ_change" :
		$list_readers_edition_ui = new list_readers_edition_ui(array('date_expiration_start' => '', 'date_expiration_end' => '', 'date_expiration_limit' => '', 'change_categ' => '((((age_min<> 0) || (age_max <> 0)) && (age_max >= age_min)) && (((DATE_FORMAT( curdate() , "%Y" )-empr_year) < age_min) || ((DATE_FORMAT( curdate() , "%Y" )-empr_year) > age_max)))'));
		break;
	case "encours" :
	default :
		$list_readers_edition_ui = new list_readers_edition_ui(array('date_expiration_start' => date('Y-m-d'), 'date_expiration_end' => '', 'date_expiration_limit' => '', 'change_categ' => ''));
		break;
}

switch($dest) {
	case "TABLEAU":
		$list_readers_edition_ui->get_display_spreadsheet_list();
		break;
	case "TABLEAUHTML":
		print $list_readers_edition_ui->get_display_html_list();
		break;
	default:
		if (isset($statut_action) && $statut_action=="modify") {
			$list_readers_edition_ui->run_change_status();
		}
		print $list_readers_edition_ui->get_display_list();
		break;
}