<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_contact_forms_ui.class.php,v 1.12.4.2 2025/02/20 15:31:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/contact_forms/contact_form.class.php");

class list_contact_forms_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT distinct id_contact_form
				FROM contact_forms
				LEFT JOIN contact_form_objects ON contact_form_objects.num_contact_form=contact_forms.id_contact_form
				';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new contact_form($row->id_contact_form);
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
		$this->filters = array();
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
						'id' => 'admin_opac_contact_form_id',
						'label' => 'admin_opac_contact_form_label',
						'desc' => 'admin_opac_contact_form_desc',
						'parameters' => 'admin_opac_contact_form_parameters',
						'objects' => 'admin_opac_contact_form_objects',
						'recipients' => 'admin_opac_contact_form_recipients',
						'permalink' => 'admin_opac_contact_form_parameter_permalink',
						'recipients_mode' => 'admin_opac_contact_form_parameter_recipients_mode',
				)
		);
		
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('id');
		$this->add_column('label');
		$this->add_column('desc');
		$this->add_column('parameters');
		$this->add_column('objects');
		$this->add_column('recipients');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'parameters':
			case 'recipients':
			    $content .= $this->get_interface_button($msg['62'], ['location' => "./admin.php?categ=contact_forms&sub=".$property."&id=".$object->get_id()]);
				break;
			case 'objects':
			    $content .= $this->get_interface_button($msg['62'], ['location' => "./admin.php?categ=contact_forms&sub=".$property."&num_contact_form=".$object->get_id()]);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch ($property) {
			case 'parameters':
			case 'recipients':
			case 'objects':
				return array();
			default:
				return array(
						'onclick' => "document.location=\"".static::get_controller_url_base()."&action=edit&id=".$object->get_id()."\""
				);
		}
	}
	
	protected function get_button_add() {
		global $msg;
		
		return $this->get_interface_button($msg['admin_opac_contact_form_add'], ['location' => static::get_controller_url_base().'&action=edit&id=0'], ['id' => 'contact_form_add', 'name' => 'contact_form_add']);
	}
	
	protected function get_display_left_actions() {
		return $this->get_button_add();
	}
}