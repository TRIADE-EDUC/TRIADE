<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_acquisition_ui.class.php,v 1.2 2021/04/08 07:29:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_acquisition_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $acquisition_rent_requests_activate;
		
		//Achats
		$this->add_tab('acquisition_menu_ach', 'ach', 'acquisition_menu_ach_devi', 'devi');
		$this->add_tab('acquisition_menu_ach', 'ach', 'acquisition_menu_ach_cmde', 'cmde');
		$this->add_tab('acquisition_menu_ach', 'ach', 'acquisition_menu_ach_recept', 'recept');
		$this->add_tab('acquisition_menu_ach', 'ach', 'acquisition_menu_ach_livr', 'livr');
		$this->add_tab('acquisition_menu_ach', 'ach', 'acquisition_menu_ach_fact', 'fact');
		$this->add_tab('acquisition_menu_ach', 'ach', 'acquisition_menu_ach_fourn', 'fourn');
		$this->add_tab('acquisition_menu_ach', 'ach', 'acquisition_menu_ref_budget', 'bud');
		
		//Suggestions
		$this->add_tab('acquisition_menu_sug', 'sug', 'acquisition_menu_sug_multiple', 'multi');
		$this->add_tab('acquisition_menu_sug', 'sug', 'acquisition_menu_sug_import', 'import');
		$this->add_tab('acquisition_menu_sug', 'sug', 'acquisition_menu_sug_empr', 'empr_sug');
		$this->add_tab('acquisition_menu_sug', 'sug', 'acquisition_menu_sug_todo');
		
		//Locations
		if ($acquisition_rent_requests_activate) {
			$this->add_tab('acquisition_menu_rent', 'rent', 'acquisition_menu_rent_requests', 'requests');
			if (SESSrights & ACQUISITION_ACCOUNT_INVOICE_AUTH) {
				$this->add_tab('acquisition_menu_rent', 'rent', 'acquisition_menu_rent_accounts', 'accounts');
				$this->add_tab('acquisition_menu_rent', 'rent', 'acquisition_menu_rent_accounts_to_invoice', 'accounts', '&rent_accounts_ui_invoiced=1&rent_accounts_ui_request_status=3');
				$this->add_tab('acquisition_menu_rent', 'rent', 'acquisition_menu_rent_invoices', 'invoices');
				$this->add_tab('acquisition_menu_rent', 'rent', 'acquisition_menu_rent_invoices_to_validate', 'invoices', '&rent_invoices_ui_status=1');
			}
		}
	}
	
	protected function is_active_tab($label_code, $categ, $sub='') {
		global $rent_accounts_ui_invoiced;
		global $rent_invoices_ui_status;
		
		$active = false;
		switch ($label_code) {
			case 'acquisition_menu_rent_accounts':
				if(($this->is_equal_var_get('categ', 'rent') && $this->is_equal_var_get('sub', 'accounts') && $rent_accounts_ui_invoiced == "")) {
					$active = true;
				}
				break;
			case 'acquisition_menu_rent_accounts_to_invoice':
				if(($this->is_equal_var_get('categ', 'rent') && $this->is_equal_var_get('sub', 'accounts') && $rent_accounts_ui_invoiced == "1")) {
					$active = true;
				}
				break;
			case 'acquisition_menu_rent_invoices':
				if(($this->is_equal_var_get('categ', 'rent') && $this->is_equal_var_get('sub', 'invoices') && $rent_invoices_ui_status == "")) {
					$active = true;
				}
				break;
			case 'acquisition_menu_rent_invoices_to_validate':
				if(($this->is_equal_var_get('categ', 'rent') && $this->is_equal_var_get('sub', 'invoices') && $rent_invoices_ui_status == "1")) {
					$active = true;
				}
				break;
			default:
				$active = parent::is_active_tab($label_code, $categ, $sub);
				break;
		}
		return $active;
	}
}