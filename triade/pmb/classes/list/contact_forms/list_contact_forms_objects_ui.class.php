<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_contact_forms_objects_ui.class.php,v 1.6.4.2 2025/02/20 15:31:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/contact_forms/contact_form_object.class.php");

class list_contact_forms_objects_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT distinct id_object
				FROM contact_form_objects
				LEFT JOIN contact_forms ON contact_form_objects.num_contact_form=contact_forms.id_contact_form
				';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new contact_form_object($row->id_object);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
			array('main_fields' => array()
			);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'num_contact_form' => 0
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('label');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'id' => 'admin_opac_contact_form_object_id',
						'label' => 'admin_opac_contact_form_object_label',
				)
		);
		
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('label');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('num_contact_form', 'num_contact_form', 'integer');
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".static::get_controller_url_base()."&num_contact_form=".$this->filters['num_contact_form']."&action=edit&id=".$object->get_id()."\""
		);
	}
	
	protected function get_button_add() {
		global $msg;
		
		return $this->get_interface_button($msg['admin_opac_contact_form_object_add'], ['location' => static::get_controller_url_base()."&num_contact_form=".$this->filters['num_contact_form']."&action=edit&id=0"], ['id' => 'contact_form_object_add', 'name' => 'contact_form_object_add']);
	}
	
	protected function get_display_left_actions() {
		global $base_path, $msg;
		
		$display = $this->get_interface_button($msg['76'], ['location' => $base_path."/admin.php?categ=contact_forms"]);
		$display .= $this->get_button_add();
		return $display;
	}
}