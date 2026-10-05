<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_patterns_ui.class.php,v 1.1 2023/06/27 13:35:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_patterns_ui extends list_ui {
	
	
	protected function get_object_instance($row) {
		return $row;
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->_init_patterns();
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	protected function _init_patterns() {
		
		$available_patterns = $this->get_available_patterns();
		foreach ($available_patterns as $group_code=>$group_patterns) {
			if(empty($this->filters['groups']) || in_array($group_code, $this->filters['groups'])) {
				foreach ($group_patterns as $code) {
					$this->add_pattern($group_code, $code);
				}
			}
		}
	}
	
	public function add_pattern($group_code, $code) {
		global $msg;
		
		$object = new stdClass();
		$object->group_code = $group_code;
		$object->group_label = (isset($msg["selvars_" . $group_code]) ? $msg["selvars_" . $group_code] : $group_code);
		$object->code = $code;
		$object->label = (isset($msg["selvars_" . $code]) ? $msg["selvars_" . $code] : $code);
		$this->add_object($object);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters['main_fields'] = array();
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'groups' => array(),
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'code' => '',
						'label' => '103',
						'group_label' => '',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'group_label');
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function init_default_columns() {
		$this->add_column('code');
		$this->add_column('label');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('label', 'align', 'left');
	}
	
	protected function _get_object_property_label($object) {
		global $msg;
		
		if(isset($msg["selvars_" . $object->code])) {
			return $msg["selvars_" . $object->code];
		} else {
			return $object->code;
		}
	}
	
	protected function _get_object_property_group_label($object) {
		global $msg;
		
		if(isset($msg["selvars_" . $object->group_code])) {
			return $msg["selvars_" . $object->group_code];
		} else {
			return $object->group_code;
		}
	}

}