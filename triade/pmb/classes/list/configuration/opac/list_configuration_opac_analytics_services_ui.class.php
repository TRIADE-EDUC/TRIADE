<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_opac_analytics_services_ui.class.php,v 1.1 2021/07/21 09:45:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/analytics_services/analytics_services.class.php");

class list_configuration_opac_analytics_services_ui extends list_configuration_opac_ui {
	
	protected function _get_query_base() {
		return "SELECT analytics_service_name as id, analytics_services.* FROM analytics_services";
	}
	
	protected function fetch_data() {
		$this->objects = array();
		
		$services = analytics_services::get_services();
		foreach ($services as $service_name) {
			$analytics_service = new analytics_service(analytics_services::get_id_from_name($service_name));
			$analytics_service->set_name($service_name);
			$this->add_object($analytics_service);
		}
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('active', 'datatype', 'boolean');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'analytics_service_name',
				'active' => 'analytics_service_active'
		);
	}
	
	protected function _get_object_property_name($object) {
		return $object->get_name()." (".$object->get_label().")";
	}

	protected function get_edition_link($object) {
		if($object->get_id()) {
			return static::get_controller_url_base().'&action=edit&id='.$object->get_id().'&name='.$object->get_name();
		} else {
			return static::get_controller_url_base().'&action=add&name='.$object->get_name();
		}
	}
	
	protected function get_display_content_object_list($object, $indice) {
		$this->is_editable_object_list = true;
		return list_ui::get_display_content_object_list($object, $indice);
	}
	
	protected function get_button_add() {
		return '';
	}
}