<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_quotas_list_ui.class.php,v 1.2 2022/11/03 15:29:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/configuration/quotas/list_configuration_quotas_ui.class.php");

class list_configuration_quotas_list_ui extends list_configuration_quotas_ui {
	
	protected function fetch_data() {
		$this->objects = array();
		for($i=0;$i<count(static::$quota_instance->quota_type["QUOTAS"]);$i++) {
			$object = new stdClass();
			$object->names = static::$quota_instance->quota_type["QUOTAS"][$i];
			$this->add_object($object);
		}
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'label' => '',
		);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('label', 'text', array('bold' => true));
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function _get_object_property_label($object) {
		global $msg;
		
		$elts=explode(",", $object->names);
		$index=array();
		for ($j=0; $j<count($elts); $j++) {
			$index[]=$msg["quotas_by"]." ".quota::$_quotas_[static::$quota_instance->descriptor]['_elements_'][static::$quota_instance->get_element_by_name($elts[$j])]["COMMENT"];
		}
		return implode(" ".$msg["quotas_and"]." ",$index);
	}
	
	public function get_display_header_list() {
		return '';
	}
	
	protected function get_edition_link($object) {
		global $query_compl;
		
		return static::get_controller_url_base().'&elements='.static::$quota_instance->get_elements_id_by_names($object->names).$query_compl;
	}
}