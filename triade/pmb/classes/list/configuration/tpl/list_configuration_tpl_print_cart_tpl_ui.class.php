<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_tpl_print_cart_tpl_ui.class.php,v 1.1 2021/02/02 07:43:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_tpl_print_cart_tpl_ui extends list_configuration_tpl_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM print_cart_tpl';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('print_cart_tpl_name');
	}
		
	protected function get_main_fields_from_sub() {
		return array(
				'id_print_cart_tpl' => 'admin_print_cart_tpl_id',
				'print_cart_tpl_name' => 'admin_print_cart_tpl_name'
		);
	}
		
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_print_cart_tpl;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_print_cart_tpl_add'];
	}
}