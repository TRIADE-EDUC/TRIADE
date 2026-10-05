<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_edit_ui.class.php,v 1.5 2023/12/15 14:56:53 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_edit_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $pmb_short_loan_management, $pmb_pnb_param_login;
		global $pmb_resa_planning, $pmb_gestion_financiere_caisses;
		global $pmb_transferts_actif, $transferts_validation_actif;
		global $pmb_logs_activate, $empr_visits_statistics_active;
		
		//Etats
		$this->add_tab('1130', 'procs', '1131');
		$this->add_tab('1130', 'state', 'editions_state');
		
		//Prêts
		$this->add_tab('1110', 'expl', '1111', 'encours');
		$this->add_tab('1110', 'expl', '1112', 'retard');
		$this->add_tab('1110', 'expl', 'edit_expl_retard_par_date', 'retard_par_date');
		$this->add_tab('1110', 'expl', '1114', 'ppargroupe');
		$this->add_tab('1110', 'expl', 'menu_retards_groupe', 'rpargroupe');
		
		//Prêts courts
		if ($pmb_short_loan_management==1) {
			$this->add_tab('short_loans', 'expl', 'short_loans', 'short_loans');
			$this->add_tab('short_loans', 'expl', 'unreturned_short_loans', 'unreturned_short_loans');
			$this->add_tab('short_loans', 'expl', 'overdue_short_loans', 'overdue_short_loans');
		}
		
		//PNB
		if($pmb_pnb_param_login) {
			$this->add_tab('edit_menu_pnb', 'pnb', 'edit_menu_pnb_orders', 'orders');
		}
		
		//Reservations
		$this->add_tab('350', 'notices', 'edit_resa_menu', 'resa');
		$this->add_tab('350', 'notices', 'edit_resa_menu_a_traiter', 'resa_a_traiter');
		if($pmb_resa_planning) {
			$this->add_tab('350', 'notices', 'edit_resa_planning_menu', 'resa_planning');
		}
		
		//Lecteurs
		$this->add_tab('1120', 'empr', '1121', 'encours');
		$this->add_tab('1120', 'empr', 'edit_menu_empr_abo_limite', 'limite');
		$this->add_tab('1120', 'empr', 'edit_menu_empr_abo_depasse', 'depasse');
		$this->add_tab('1120', 'empr', 'edit_menu_empr_categ_change', 'categ_change');
		if($pmb_gestion_financiere_caisses) {
			$this->add_tab('1120', 'empr', 'cashdesk_edition', 'cashdesk');
		}
		
		//Périodique
		$this->add_tab('1150', 'serials', '1151', 'collect');
		$this->add_tab('1150', 'serials', 'serial_circ_state_edit', 'circ_state');
		$this->add_tab('1150', 'serials', 'serial_simple_circ_edit', 'simple_circ');
		
		//Code-barres
		$this->add_tab('1140', 'cbgen', '1141', 'libre');
		$this->add_tab('1140', 'barcodes_sheets', 'barcodes_sheet_models', 'models');
		
		//Etiquettes
		$this->add_tab('sticks_sheet', 'sticks_sheet', 'sticks_sheet_models', 'models');
		
		//Templates
		$this->add_tab('edit_tpl_menu', 'tpl', 'edit_notice_tpl_menu', 'notice');
		$this->add_tab('edit_tpl_menu', 'tpl', 'edit_serialcirc_tpl_menu', 'serialcirc');
		$this->add_tab('edit_tpl_menu', 'tpl', 'edit_bannette_tpl_menu', 'bannette');
		$this->add_tab('edit_tpl_menu', 'tpl', 'admin_print_cart_tpl_menu', 'print_cart_tpl');
		
		//Transferts
		if ($pmb_transferts_actif=="1") {
			if ($transferts_validation_actif=="1") {
				$this->add_tab('transferts_edition_titre', 'transferts', 'transferts_edition_validation', 'validation');
			}
			$this->add_tab('transferts_edition_titre', 'transferts', 'transferts_edition_envoi', 'envoi');
			$this->add_tab('transferts_edition_titre', 'transferts', 'transferts_edition_reception', 'reception');
			$this->add_tab('transferts_edition_titre', 'transferts', 'transferts_edition_retours', 'retours');
			$this->add_tab('transferts_edition_titre', 'transferts', 'transferts_circ_menu_refuse', 'refus');
		}
		
		//OPAC
		if($pmb_logs_activate) {
			$this->add_tab('opac_admin_menu', 'stat_opac', 'stat_opac_menu');
		}
		$this->add_tab('opac_admin_menu', 'opac', 'campaigns', 'campaigns');
		if($empr_visits_statistics_active) {
			$this->add_tab('opac_admin_menu', 'opac', 'dashboard_visits_statistics', 'visits_statistics');
		}
	}
}