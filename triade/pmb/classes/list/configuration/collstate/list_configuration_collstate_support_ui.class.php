<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_collstate_support_ui.class.php,v 1.3 2021/01/12 07:23:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_collstate_support_ui extends list_configuration_collstate_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM arch_type';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('archtype_libelle');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'archtype_libelle' => 'admin_collstate_support_nom',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->archtype_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_collstate_add_support'];
	}
}