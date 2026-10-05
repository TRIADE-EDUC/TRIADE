<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_explnum_licence_profiles_ui.class.php,v 1.2.8.1 2025/02/21 13:24:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_explnum_licence_profiles_ui extends list_configuration_explnum_ui {
	
	protected function _get_query_base() {
		return 'SELECT id_explnum_licence_profile FROM explnum_licence_profiles';
	}
	
	protected function get_object_instance($row) {
		return new explnum_licence_profile($row->id_explnum_licence_profile);
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'licence' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('label');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'label' => 'docnum_statut_libelle',
				'uri' => 'explnum_licence_uri',
		); 
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'label', 'uri'
		);
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('licence', 'explnum_licence_profile_explnum_licence_num', 'integer');
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&profileaction=edit&profileid='.$object->get_id();
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["explnum_licence_no_profile_defined"], ENT_QUOTES, $charset);
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['explnum_licence_profile_new'];
	}
	
	protected function get_button_add() {
		return $this->get_interface_button($this->get_label_button_add(), ['location' => static::get_controller_url_base()."&profileaction=edit"]);
	}
	
	public static function get_controller_url_base() {
		global $base_path, $id;
		$id = intval($id);
		return $base_path.'/'.static::$module.'.php?categ='.static::$categ.'&sub=licence&action=settings&id='.$id.'&what=profiles';
	}
}