<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_notices_orinot_ui.class.php,v 1.4 2023/03/24 07:44:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_notices_orinot_ui extends list_configuration_notices_ui {
	
	protected function _get_query_base() {
		return 'SELECT orinot_id, orinot_nom, orinot_pays, orinot_diffusion FROM origine_notice';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('orinot_nom');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'orinot_nom' => 'orinot_nom',
				'orinot_pays' => 'orinot_pays',
				'orinot_diffusion' => 'orinot_diffusable',
		);
	}
	
	protected function _get_object_property_orinot_diffusion($object) {
		global $msg;
		
		if ($object->orinot_diffusion) {
			return $msg['orinot_diffusable_oui'];
		} else {
			return $msg['orinot_diffusable_non'];
		}
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->orinot_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['orinot_ajout'];
	}
}