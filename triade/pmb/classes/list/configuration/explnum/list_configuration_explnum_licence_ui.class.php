<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_explnum_licence_ui.class.php,v 1.4 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_explnum_licence_ui extends list_configuration_explnum_ui {
	
	protected function _get_query_base() {
		return 'SELECT id_explnum_licence as id, explnum_licence.* FROM explnum_licence';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('explnum_licence_label');
	}
	
	protected function get_main_fields_from_sub() {
		$main_fields = array(
				'explnum_licence_label' => 'docnum_statut_libelle',
				'explnum_licence_uri' => 'explnum_licence_uri',
		); 
		return $main_fields;
	}

	protected function init_default_columns() {
		parent::init_default_columns();
		$this->add_column_configure_profiles_rights();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'configure_profiles_rights',
		);
	}
	
	protected function add_column_configure_profiles_rights() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['explnum_licence_settings'],
				'link' => static::get_controller_url_base().'&action=settings&id=!!id!!'
		);
		$this->add_column_simple_action('configure_profiles_rights', '', $html_properties);
	}
	
	protected function get_display_content_object_list($object, $indice) {
		$this->is_editable_object_list = false;
		return parent::get_display_content_object_list($object, $indice);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id_explnum_licence;
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["explnum_licence_no_licence_defined"], ENT_QUOTES, $charset);
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
		);
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['explnum_licence_new'];
	}
}