<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_ui.class.php,v 1.24.2.3 2024/11/28 09:03:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Helper\Helper;
use Pmb\Users\Controller\RolesController;

global $class_path;
require_once($class_path."/tabs/tabs.class.php");
require_once($class_path."/tabs/tab.class.php");

class list_tabs_ui extends list_ui {
	
	protected static $module_name;
	
	protected static $no_check_rights;
	
	protected $rolesController;
	
	protected function get_object_instance($row) {
		return $row;
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->rolesController = new RolesController();
		$this->_init_tabs();
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	protected function has_tab_rights($categ, $sub, $url_extra='') {
		global $PMBuserid;
		
		if(!empty(static::$no_check_rights) || ($PMBuserid == 1)) {
		    return true;
		}
		
		$data = [
		    'module' => static::$module_name,
		    'categ' => $categ,
		    'sub' => $sub,
		    'urlExtra' => $url_extra,
		    'userId' => $PMBuserid
		];
		$userRights = $this->rolesController->getUserRights('tabs', $data);
		if(isset($userRights['visible']) && $userRights['visible'] == 0) {
		    return false;
		}
		$tabs_module = tabs::get_tabs_module(static::$module_name);
		$tab_key = $categ.($sub ? "_".$sub : '');
		if(isset($tabs_module[$tab_key]['visible']) && $tabs_module[$tab_key]['visible'] == 0) {
			return false;
		}
		if(isset($tabs_module[$tab_key]['autorisations_all']) && $tabs_module[$tab_key]['autorisations_all'] == 1) {
			return true;
		}
		if(isset($tabs_module[$tab_key]['autorisations']) && !in_array($PMBuserid, $tabs_module[$tab_key]['autorisations'])) {
			return false;
		}
		return true;
	}
	
	public function add_tab($section, $categ, $label_code, $sub='', $url_extra='', $number=0) {
		global $msg;
		global $base_path;
		
		if($this->has_tab_rights($categ, $sub, $url_extra)) {
			$tab = new tab();
			$tab->set_module(static::$module_name)
				->set_section($section)
				->set_label_code($label_code)
				->set_categ($categ)
				->set_label(isset($msg[$label_code]) ? $msg[$label_code] : $label_code)
				->set_sub($sub)
				->set_url_extra($url_extra)
				->set_number($number)
				->set_destination_link($base_path."/".static::$module_name.".php".($categ ? "?categ=".$categ : "").($sub ? "&sub=".$sub : '').$url_extra);
			$tab->is_in_database();
			$this->add_object($tab);
		}
	}
	
	protected function is_equal_var_get($variable, $value="") {
		if(!empty($value) && is_array($value)) {
			if(isset($_GET[$variable])) {
				if(in_array($_GET[$variable], $value)) {
					return true;
				}
			}
		} else {
			if(!empty($value) && isset($_GET[$variable]) && $_GET[$variable] == $value) {
				return true;
			}
			if(empty($value) && empty($_GET[$variable])) {
				return true;
			}
		}
		return false;
	}
	
	protected function is_active_tab($label_code, $categ, $sub='') {
		if((isset($_GET['categ']) && $categ == $_GET['categ']) && (empty($sub) || (isset($_GET['sub']) && $sub == $_GET['sub']))) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_display_tab($object) {
		$nodeId = static::$module_name . "_menu_" . Helper::snakelize($object->get_label_code());
		return "<li id='{$nodeId}' ".($this->is_active_tab($object->get_label_code(), $object->get_categ(), $object->get_sub()) ? "class='active'" : "" ).">
			<a href='".$object->get_destination_link()."'>
				".$object->get_label()."
			</a>
		</li>";
	}
	
	public function get_display() {
		$display = '';
		$grouped_objects = $this->get_grouped_objects();
		foreach($grouped_objects as $group_label=>$objects) {
		    if (!empty($group_label)) {
    			$display .= "<h3 onclick='menuHide(this,event)'>".$group_label."</h3>";
		    }
		    $display .= "<ul>";
			foreach ($objects as $object) {
				$display .= $this->get_display_tab($object);
			}
			$display .= "</ul>";
		}
		return $display;
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
						'visible' => 'tab_visible',
						'autorisations' => '25',
						'autorisations_all' => 'tab_autorisations_all',
						'shortcut' => '95',
						'initialization' => 'initialization'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des colonnes éditables disponibles
	 */
	protected function init_available_editable_columns() {
		$this->available_editable_columns = array(
				'visible',
				'autorisations',
				'autorisations_all'
		);
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'section');
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'label', 'visible', 'autorisations', 'autorisations_all', 'shortcut', 'initialization'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('label');
		$this->add_column('visible');
		$this->add_column('autorisations_all');
		$this->add_column('shortcut');
		$this->add_column('initialization');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('grouped_objects', 'sort', false);
		$this->set_setting_column('label', 'align', 'left');
		$this->set_setting_column('visible', 'datatype', 'boolean');
		$this->set_setting_column('visible', 'edition_type', 'radio');
		$this->set_setting_column('autorisations', 'edition_type', 'checkbox');
		$this->set_setting_column('autorisations_all', 'datatype', 'boolean');
		$this->set_setting_column('autorisations_all', 'edition_type', 'radio');
	}
	
	protected function get_selection_query_fields($type) {
		switch ($type) {
			case 'users':
				return array('id' => 'userid', 'label' => 'username');
		}
	}
	
	protected function _get_object_property_section($object) {
		global $msg;
		
		$section = $object->get_section();
		if(isset($msg[$section])) {
			return $msg[$section];
		} else {
			return $section;
		}
	}
	
	protected function _get_object_property_shortcut($object) {
		$shortcut = $object->get_shortcut();
		if($shortcut) {
			return "<kbd>Esc</kbd>+<kbd>".$shortcut."</kbd>";
		}
		return '';
	}
	
	protected function _get_object_property_initialization($object) {
		if($object->is_substituted()) {
			return 1;
		}
		return 0;
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
		    case 'shortcut':
		        $content .= $this->_get_object_property_shortcut($object); // Interprétation du HTML
		        break;
			case 'initialization':
				if($object->is_substituted()) {
					$link = static::get_controller_url_base()."&action=delete&id=".$object->get_id();
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
				if($object->is_in_database()) {
					$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&id=".$object->get_id()."\"";
				} else {
					$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&tab_module=".$object->get_module()."&tab_categ=".$object->get_categ()."&tab_sub=".$object->get_sub()."\"";
				}
				break;
		}
		return $attributes;
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$edit_link = array(
				'showConfiguration' => static::get_controller_url_base()."&action=list_save"
		);
		$this->add_selection_action('edit', $msg['62'], 'b_edit.png', $edit_link);
		$initialize_link = array(
				'href' => static::get_controller_url_base()."&action=list_delete",
				'confirm' => $msg['initialization_confirm']
		);
		$this->add_selection_action('delete', $msg['initialize'], '', $initialize_link);
	}
	
	protected function get_options_editable_column($object, $property) {
		switch ($property) {
			case 'autorisations':
				return $this->get_options_from_query_selection($this->get_selection_query('users'));
			default:
				return parent::get_options_editable_column($object, $property);
		}
	}
	
	protected function get_display_cell_html_value($object, $value) {
	    if(empty($object->get_id())) {
	        $value = str_replace('!!id!!', $object->get_module()."_".$object->get_categ().(!empty($object->get_sub()) ? "_".$object->get_sub() : ""), $value);
	    }
	    return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function save_object($object, $property, $value) {
		switch ($property) {
			case 'autorisations':
				parent::save_object($object, $property, implode(" ",$value));
				break;
			default:
				parent::save_object($object, $property, $value);
				break;
		}
	}
	
	public function get_objects_data() {
	    if (empty($this->selected_columns['label_code'])) {
	        $this->add_column('label_code');
	    }
	    if (empty($this->selected_columns['categ'])) {
	        $this->add_column('categ');
	    }
	    if (empty($this->selected_columns['sub'])) {
	        $this->add_column('sub');
	    }
	    if (empty($this->selected_columns['url_extra'])) {
	        $this->add_column('url_extra');
	    }
	    return parent::get_objects_data();
	}
	
	public static function delete_object($id) {
		tab::delete($id);
	}
	
	public static function get_controller_url_base() {
	    return parent::get_controller_url_base().(!empty(static::$module_name) ? '&tab_module='.static::$module_name : '');
	}
	
	public static function set_module_name($module_name) {
		static::$module_name = $module_name;
	}
	
	public static function set_no_check_rights($no_check_rights) {
		static::$no_check_rights = intval($no_check_rights);
	}
}