<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_procs_classements_ui.class.php,v 1.9 2023/09/29 06:46:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_procs_classements_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT idproc_classement,libproc_classement FROM procs_classements';
		return $query;
	}
	
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'label':
	            return 'libproc_classement';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
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
						'label' => 'proc_clas_lib',
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
		$this->set_setting_column('label', 'align', 'left');
		$this->set_setting_column('label', 'text', array('strong' => true));
	}
	
	protected function get_button_add() {
		global $msg;
	
		return $this->get_button('add', $msg['proc_clas_bt_add']);
	}
	
	protected function _get_object_property_label($object) {
		return $object->libproc_classement;
	}

	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".static::get_controller_url_base()."&action=modif&id=".$object->idproc_classement."\""
		);
	}
	
	protected function get_display_left_actions() {
		return $this->get_button_add();
	}
}