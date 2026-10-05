<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_cashdesk_transactions_compte_ui.class.php,v 1.1.2.2 2024/09/11 14:19:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_cashdesk_transactions_compte_ui extends list_cashdesk_transactions_ui {
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('search_form', 'visible', false);
	    $this->set_setting_display('query', 'human', false);
	    $this->set_setting_display('pager', 'visible', false);
	}
	
	protected function init_default_columns() {
		$this->add_column('empr');
		$this->add_column('date_enrgt');
		$this->add_column('sens');
		$this->add_column('montant');
		$this->add_column('commentaire');
		$this->add_column('payment_method');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date_enrgt', 'desc');
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['all_on_page'] = true;
	}
	
	protected function _cell_is_sortable($name) {
	    return false;
	}
}