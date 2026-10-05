<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_notices_map_ref_ui.class.php,v 1.2 2021/01/12 07:23:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_notices_map_ref_ui extends list_configuration_notices_ui {
	
	protected function _get_query_base() {
		return 'SELECT map_ref_id, map_ref_name FROM map_refs';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('map_ref_name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'map_ref_name' => 'admin_nomap_ref_name',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->map_ref_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_noti_map_ref_ajout'];
	}
}