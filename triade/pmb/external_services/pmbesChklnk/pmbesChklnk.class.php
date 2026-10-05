<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesChklnk.class.php,v 1.3 2023/03/16 11:01:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/chklnk/chklnk.class.php");

class pmbesChklnk extends external_services_api_class {
	
	protected $initialized_chklnk;
	
	protected function initialize_chklnk() {
		chklnk::init_queries();
	}
	
	protected function check_parameter($class_name, $caddie_id=0, $caddie_type='NOTI') {
		if(!isset($this->initialized_chklnk)) {
			$this->initialize_chklnk();
		}
		$class_name_instance = new $class_name();
		$caddie_id = intval($caddie_id);
		if ($caddie_id) {
			$caddie_instance = caddie_root::get_instance_from_object_type($caddie_type, $caddie_id);
			$class_name_instance->set_caddie_instance($caddie_instance);
			$class_name_instance->set_caddie_type($caddie_type);
		}
		return $class_name_instance->process_scheduler();
	}
	
	protected function check_custom_field_parameter($sub_type, $caddie_id=0, $caddie_type='NOTI') {
		if(!isset($this->initialized_chklnk)) {
			$this->initialize_chklnk();
		}
		$chklnk_custom_fields = new chklnk_custom_fields();
		$chklnk_custom_fields->set_sub_type($sub_type);
		$caddie_id = intval($caddie_id);
		if ($caddie_id) {
			$caddie_instance = caddie_root::get_instance_from_object_type($caddie_type, $caddie_id);
			$chklnk_custom_fields->set_caddie_instance($caddie_instance);
			$chklnk_custom_fields->set_caddie_type($caddie_type);
		}
		return $chklnk_custom_fields->process_scheduler();
	}
	
	public function check_records($caddie_id=0) {
		return $this->check_parameter('chklnk_records', $caddie_id);
	}
	
	public function check_records_thumbnail($caddie_id=0) {
		return $this->check_parameter('chklnk_vign', $caddie_id);
	}
	
	public function check_records_custom_fields($caddie_id=0) {
		return $this->check_custom_field_parameter('notices', $caddie_id);
	}
	
	public function check_records_enum($caddie_id=0) {
		return $this->check_parameter('chklnk_enum', $caddie_id);
	}
	
	public function check_bulletins($caddie_id=0) {
		return $this->check_parameter('chklnk_bull', $caddie_id, 'BULL');
	}
	
	public function check_custom_fields_etatcoll($caddie_id=0) {
		return $this->check_custom_field_parameter('collstate', $caddie_id);
	}
	
	public function check_authors($caddie_id=0) {
		return $this->check_parameter('chklnk_authors', $caddie_id, 'AUTHORS');
	}
	
	public function check_publishers($caddie_id=0) {
		return $this->check_parameter('chklnk_publishers', $caddie_id, 'PUBLISHERS');
	}
	
	public function check_collections($caddie_id=0) {
		return $this->check_parameter('chklnk_collections', $caddie_id, 'COLLECTIONS');
	}
	
	public function check_subcollections($caddie_id=0) {
		return $this->check_parameter('chklnk_subcollections', $caddie_id, 'SUBCOLLECTIONS');
	}
	
	public function check_authorities_thumbnail($caddie_id=0) {
		return $this->check_parameter('chklnk_authorities_thumbnail', $caddie_id, 'MIXED');
	}
	
	public function check_editorial_custom_fields() {
		global $cms_active;
		
		if($cms_active) {
			$chklnk_custom_fields = new chklnk_custom_fields();
			$chklnk_custom_fields->set_sub_type('cms_editorial');
		}
		return $this->check_custom_field_parameter('cms_editorial');
	}
}


?>