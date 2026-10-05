<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_authorities_origins_ui.class.php,v 1.2 2021/04/19 10:09:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_authorities_origins_ui extends list_configuration_authorities_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM origin_authorities';
	}
	
	protected function get_object_instance($row) {
		return new origin($row->id_origin_authorities);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'origin_name',
				'country' => 'origin_country',
				'diffusible' => 'origin_diffusible'
		);
	}
	
	protected function _get_object_property_diffusible($object) {
		global $msg;
		
		if($object->diffusible) {
			return $msg['orinot_diffusable_oui'];
		} else {
			return $msg['orinot_diffusable_non'];
		}
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['authorities_origin_add'];
	}
}