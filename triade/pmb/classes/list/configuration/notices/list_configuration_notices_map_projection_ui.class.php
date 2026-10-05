<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_notices_map_projection_ui.class.php,v 1.2 2021/01/12 07:23:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_notices_map_projection_ui extends list_configuration_notices_ui {
	
	protected function _get_query_base() {
		return 'SELECT map_projection_id, map_projection_name FROM map_projections';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('map_projection_name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'map_projection_name' => 'admin_nomap_projection_name',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->map_projection_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_noti_map_projection_ajout'];
	}
}