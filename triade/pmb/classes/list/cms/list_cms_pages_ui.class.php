<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_cms_pages_ui.class.php,v 1.5.4.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/cms/cms_pages.class.php");

class list_cms_pages_ui extends list_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM cms_pages';
	}
	
	protected function get_object_instance($row) {
		return new cms_page($row->id_page);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function init_available_columns() {
		$this->available_columns = array (
				'main_fields' => array (
						'name' => 'infopage_title_infopage'
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('name');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function get_button_add() {
		global $msg;
		
		return $this->get_interface_button($msg["cms_new_page_button"], ['location' => static::get_controller_url_base()."&sub=edit"], ['name' => 'cms_page_add']);
	}
	
	protected function get_display_left_actions() {
		return $this->get_button_add();
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&sub=edit&id=".$object->get_id()."\"";
		return $attributes;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		return $base_path.'/cms.php?categ=pages';
	}
}