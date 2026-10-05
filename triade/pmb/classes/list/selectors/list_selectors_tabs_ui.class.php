<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_selectors_tabs_ui.class.php,v 1.7.4.1 2025/04/15 07:51:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/list_ui.class.php");
require_once($class_path."/selectors/selector_model.class.php");

class list_selectors_tabs_ui extends list_ui {
	
	protected function _init_selectors_tabs() {
		global $msg;
		
		if(!empty($this->filters['selector_name'])) {
			$selector = selector_model::get_instance($this->filters['selector_name']);
			$tabs = $selector->get_parameters_tabs();
			foreach ($tabs as $name=>$tab) {
				$this->add_selector_tab($name, $msg['selector_tab_'.$name], $tab['visible_gestion'], $tab['default_selected_gestion'], $tab['visible_opac'], $tab['default_selected_opac']);
			}
		}
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->_init_selectors_tabs();
		$this->messages = "";
	}
	
	public function add_selector_tab($name, $label, $visible_gestion, $default_selected_gestion, $visible_opac, $default_selected_opac) {
		$selector_tab = array(
				'id' => $name,
				'name' => $name,
				'label' => $label,
				'visible_gestion' => $visible_gestion,
				'default_selected_gestion' => $default_selected_gestion,
				'visible_opac' => $visible_opac,
				'default_selected_opac' => $default_selected_opac
		);
		$this->add_object((object) $selector_tab);
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'selector_name' => '',
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
						'label' => '103',
						'visible_gestion' => 'selector_tab_visible',
						'default_selected_gestion' => 'selector_tab_default_selected',
						'visible_opac' => 'selector_tab_visible',
						'default_selected_opac' => 'selector_tab_default_selected'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'label', 'visible_gestion', 'default_selected_gestion', 'visible_opac', 'default_selected_opac'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function init_default_columns() {
		$this->add_column('label');
		$this->add_column('visible_gestion');
		$this->add_column('default_selected_gestion');
		$this->add_column('visible_opac');
		$this->add_column('default_selected_opac');
	}
	
	public function get_display_search_form() {
		return '';
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		
		$this->set_setting_column('label', 'align', 'left');
		$this->set_setting_column('visible_gestion', 'display_mode', 'edition');
		$this->set_setting_column('visible_gestion', 'datatype', 'boolean');
		$this->set_setting_column('visible_gestion', 'edition_type', 'radio');
		$this->set_setting_column('default_selected_gestion', 'display_mode', 'edition');
		$this->set_setting_column('visible_opac', 'display_mode', 'edition');
		$this->set_setting_column('visible_opac', 'datatype', 'boolean');
		$this->set_setting_column('visible_opac', 'edition_type', 'radio');
		$this->set_setting_column('default_selected_opac', 'display_mode', 'edition');
	}
	
	protected function get_cell_edition_content($object, $property) {
		global $charset;
		
		if(in_array($property, array('visible_opac', 'default_selected_opac')) && in_array($object->name, array('hierarchical_search', 'terms_search', 'add'))) {
			return '';
		}
		$content = '';
		switch($property) {
			case 'visible_gestion':
			case 'visible_opac':
				$options = $this->get_options_editable_column($object, $property);
				foreach($options as $option) {
					$content .= "<input type='radio' id='".$this->objects_type."_parameters_tabs_".$object->name."_".$property."_".$option['value']."' name='".$this->objects_type."_parameters_tabs[".$object->name."][".$property."]' value='".$option['value']."' ".($object->{$property} == $option['value'] ? "checked='checked'" : "")." />";
					$content .= "<label for='".$this->objects_type."_parameters_tabs_".$object->name."_".$property."_".$option['value']."'>".htmlentities($option['label'], ENT_QUOTES, $charset)."</label>";
				}
				break;
			case 'default_selected_gestion':
			case 'default_selected_opac':
				$content .= "<input type='radio' id='".$this->objects_type."_parameters_tabs_".$object->name."_".$property."' name='".$this->objects_type."_parameters_tabs[".$property."]' value='".$object->name."' ".(!empty($object->{$property}) ? "checked='checked'" : "")." />";
				break;
			default :
				$content .= parent::get_cell_edition_content($object, $property);
				break;
		}
		return $content;
	}
	
	public function get_display_header_list() {
		global $msg;
		
		$display = "
		<tr>
			<th scope='colgroup'></th>
			<th colspan='2' scope='colgroup'>".$msg["selector_parameters_gestion"]."</th>
			<th colspan='2' scope='colgroup'>".$msg["selector_parameters_opac"]."</th>
		</tr>";
		$display .= parent::get_display_header_list();
		return $display;
	}
}