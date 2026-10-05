<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_finance_transaction_payment_method_ui.class.php,v 1.1 2021/01/13 11:19:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_finance_transaction_payment_method_ui extends list_configuration_finance_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM transaction_payment_methods';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('transaction_payment_method_name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'transaction_payment_method_name' => 'transaction_payment_method_list_libelle',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->transaction_payment_method_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['transaction_payment_method_list_add'];
	}
}