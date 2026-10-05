<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_rent_invoices_selector_ui.class.php,v 1.2 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_rent_invoices_selector_ui extends list_rent_invoices_ui {
			
	protected $num_account;
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('id');
		$this->add_column('num_user');
		$this->add_column('date');
		$this->add_column('num_publisher');
		$this->add_column('num_supplier');
		$this->add_column('destination_name');
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "account_add_account_in_invoice(".$this->num_account.", ".$object->get_id().");";
		return $attributes;
	}
	
	protected function get_selection_actions() {
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
		}
		return $this->selection_actions;
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["acquisition_account_invoices_not_found"], ENT_QUOTES, $charset);
	}
	
	public function set_num_account($num_account) {
		$this->num_account = intval($num_account);
	}
}