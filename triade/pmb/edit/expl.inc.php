<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl.inc.php,v 1.71 2018-12-27 10:32:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($class_path."/list/loans/list_loans_edition_ui.class.php");

switch($sub) {
	case 'retard' :
		$list_loans_edition_ui = new list_loans_edition_ui(array('pret_retour_end' => date('Y-m-d'), 'short_loan_flag' => 0, 'pret_date_end' => '', 'pret_retour_start' => ''), array(), array('by' => 'empr'));
		break;
	case 'retard_par_date' :
		$list_loans_edition_ui = new list_loans_edition_ui(array('pret_retour_end' => date('Y-m-d'), 'short_loan_flag' => 0, 'pret_date_end' => '', 'pret_retour_start' => ''), array(), array('by' => 'pret_retour_empr'));
		break;
	case 'short_loans':
		$list_loans_edition_ui = new list_loans_edition_ui(array('pret_retour_end' => '', 'short_loan_flag' => 1, 'pret_date_end' => '', 'pret_retour_start' => ''), array(), array('by' => 'pret_retour'));
		break;
	case 'unreturned_short_loans' :
		$list_loans_edition_ui = new list_loans_edition_ui(array('pret_retour_end' => '', 'short_loan_flag' => 1, 'pret_date_end' => date('Y-m-d'), 'pret_retour_start' => date('Y-m-d')), array(), array('by' => 'pret_retour'));
		break;
	case 'overdue_short_loans' :
		$list_loans_edition_ui = new list_loans_edition_ui(array('pret_retour_end' => date('Y-m-d'), 'short_loan_flag' => 1, 'pret_date_end' => '', 'pret_retour_start' => ''), array(), array('by' => 'pret_retour'));
		break;
	default:
		$list_loans_edition_ui = new list_loans_edition_ui(array('pret_retour_end' => '', 'short_loan_flag' => 0, 'pret_date_end' => '', 'pret_retour_start' => ''), array(), array('by' => 'pret_retour'));
		break;
}
switch($dest) {
	case "TABLEAU":
		$list_loans_edition_ui->get_display_spreadsheet_list();
		break;
	case "TABLEAUHTML":
		print $list_loans_edition_ui->get_display_html_list();
		break;
	default:
		print $list_loans_edition_ui->get_display_list();
		//impression/emails (on est dans le cas retards/retards par date)
		if ($action == "print") {
			$list_loans_edition_ui->print_relances();
		}
		break;
}
