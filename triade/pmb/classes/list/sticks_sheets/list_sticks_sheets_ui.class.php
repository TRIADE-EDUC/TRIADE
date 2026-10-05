<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_sticks_sheets_ui.class.php,v 1.9 2023/09/29 07:22:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/sticks_sheet/sticks_sheet.class.php");

class list_sticks_sheets_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT id_sticks_sheet FROM sticks_sheets';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new sticks_sheet($row->id_sticks_sheet);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters = array('main_fields' => array());
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'label' => 'sticks_sheet_label',
					'page_format' => 'sticks_sheet_page_format',
					'page_orientation_label' => 'sticks_sheet_page_orientation',
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('label');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'id_sticks_sheet';
	        case 'label' :
	            return 'sticks_sheet_'.$sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
		
	protected function get_button_add() {
		global $msg;
		
		return $this->get_button('add', $msg['ajouter']);
	}
	
	protected function get_display_left_actions() {
		return $this->get_button_add();
	}
	
	protected function init_default_columns() {
		$this->add_column('label');
		$this->add_column('page_format');
		$this->add_column('page_orientation_label');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('label', 'align', 'left');
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&id=".$object->get_id()."\"";
		return $attributes;
	}
}