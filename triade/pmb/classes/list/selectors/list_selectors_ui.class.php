<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_selectors_ui.class.php,v 1.6 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/list_ui.class.php");
require_once($class_path."/selectors/selector_model.class.php");

class list_selectors_ui extends list_ui {
	
	protected function _init_selectors() {
		$this->add_selector('author');
		$this->add_selector('authperso');
		$this->add_selector('category');
		$this->add_selector('collection');
		$this->add_selector('indexint');
		$this->add_selector('publisher');
		$this->add_selector('serie');
		$this->add_selector('subcollection');
		$this->add_selector('titre_uniforme');
		$this->add_selector('func');
		$this->add_selector('lang');
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->_init_selectors();
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	public function add_selector($name) {
		$selector = new selector_model($name);
		$this->add_object($selector);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters['main_fields'] = array();
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'label' => '103',
						'initialization' => 'initialization'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'label', 'initialization'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('label');
		$this->add_column('initialization');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('label', 'align', 'left');
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'initialization':
				if($object->is_substituted()) {
					$link = static::get_controller_url_base()."&action=delete&name=".$object->get_name();
					$content .= $this->get_img_cell_content('initialization.png', 'initialize', $link, 'initialization_confirm');
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		switch ($property) {
			case 'initialization':
				break;
			default:
				$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&name=".$object->get_name()."\"";
				break;
		}
		return $attributes;
	}
	
	protected function _get_object_property_initialization($object) {
		if($object->is_substituted()) {
			return 1;
		}
		return 0;
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$initialize_link = array(
				'href' => static::get_controller_url_base()."&action=list_delete",
				'confirm' => $msg['initialization_confirm']
		);
		$this->add_selection_action('delete', $msg['initialize'], '', $initialize_link);
	}
	
	public static function delete_object($name) {
		selector_model::delete($name);
	}
}