<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_harvest_profil_import_ui.class.php,v 1.2 2021/04/16 14:47:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/harvest_profil_import.class.php");

class list_configuration_harvest_profil_import_ui extends list_configuration_harvest_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM harvest_profil_import';
	}
	
	protected function get_object_instance($row) {
		return new harvest_profil_import($row->id_harvest_profil_import);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'admin_harvest_profil_name',
		);
	}
	
	protected function _get_object_property_name($object) {
		return $object->info['name'];
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_harvest_profil_add'];
	}
}