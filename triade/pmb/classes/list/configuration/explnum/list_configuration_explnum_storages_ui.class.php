<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_explnum_storages_ui.class.php,v 1.2 2021/04/19 07:10:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/storages/storages.class.php");

class list_configuration_explnum_storages_ui extends list_configuration_explnum_ui {
	
	protected $storages_instance;
	
	protected function _get_query_base() {
		return 'SELECT * FROM storages';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('storage_name');
	}
	
	protected function get_main_fields_from_sub() {
		$main_fields = array(
				'storage_name' => 'storage_name',
				'storage_type' => 'storage_type',
				'storage_resume' => 'storage_resume'
		); 
		return $main_fields;
	}
	
	protected function _get_object_property_storage_type($object) {
		return $this->get_storages_instance()->get_type($object->storage_class);
	}
	
	protected function _get_object_property_storage_resume($object) {
		$obj = storages::get_storage_class($object->id_storage);
		if($obj){
			return $obj->get_infos();
		}
		return '';
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id_storage;
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['storage_add'];
	}
	
	protected function get_storages_instance() {
		if(!isset($this->storages_instance)) {
			$this->storages_instance = new storages();
		}
		return $this->storages_instance;
	}
}