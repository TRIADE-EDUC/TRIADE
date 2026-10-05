<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_finance_cashdesk_ui.class.php,v 1.1 2021/01/15 10:14:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_finance_cashdesk_ui extends list_configuration_finance_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM cashdesk';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('cashdesk_name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'cashdesk_name' => 'cashdesk_list_libelle',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->cashdesk_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['cashdesk_list_add'];
	}
}