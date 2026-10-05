<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_model.class.php,v 1.5 2023/07/06 13:46:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class selector_model
 * Un sélecteur
 */
class selector_model {
	
	protected $id;
	
	protected $name;
	
	protected $label;
	
	protected $parameters_tabs;
	
	protected $display_modes;
	
	protected static $instances;
	
	public function __construct($name='') {
		$this->id = 0;
		$this->name = $name;
		$this->fetch_data();
	}
	
	protected function _init_label() {
		$this->get_label();
	}
	
	protected function _init_parameters_tabs() {
		$this->get_parameters_tabs();
	}
	
	protected function _init_display_modes() {
		$this->get_display_modes();
	}
	
	protected function fetch_data(){
		$this->_init_label();
		$this->_init_parameters_tabs();
		$this->_init_display_modes();
		$query = 'SELECT * FROM selectors WHERE selector_name = "'.addslashes($this->name).'"';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$data = pmb_mysql_fetch_object($result);
			$this->id = 1;
			$selector_parameters_tabs = encoding_normalize::json_decode($data->selector_parameters_tabs, true);
			if(is_array($selector_parameters_tabs)) {
				$this->parameters_tabs = array_merge($this->parameters_tabs, $selector_parameters_tabs);
			}
			$selector_display_modes = encoding_normalize::json_decode($data->selector_display_modes, true);
			if(is_array($selector_display_modes)) {
				$this->display_modes = array_merge($this->display_modes, $selector_display_modes);
			}
		}
	}
	
	protected function get_parameters_tabs_content_form() {
		return list_selectors_tabs_ui::get_instance(array('selector_name' => $this->name))->get_display_list();
	}
	
	protected function get_display_modes_content_form() {
		global $selector_display_modes_content_form;
		
		$content_form = $selector_display_modes_content_form;
		$content_form = str_replace('!!display_mode_record!!', ($this->display_modes['record'] == 'popup' ? "checked='checked'" : ""), $content_form);
		$content_form = str_replace('!!display_mode_serial!!', ($this->display_modes['serial'] == 'popup' ? "checked='checked'" : ""), $content_form);
		$content_form = str_replace('!!display_mode_bulletin!!', ($this->display_modes['bulletin'] == 'popup' ? "checked='checked'" : ""), $content_form);
		$content_form = str_replace('!!display_mode_analysis!!', ($this->display_modes['analysis'] == 'popup' ? "checked='checked'" : ""), $content_form);
		
		return $content_form;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('selector_parameters_opac', 'selector_parameters_tabs')
		->add_html_node($this->get_parameters_tabs_content_form());
		$interface_content_form->add_element('selector_display_modes', 'selector_display_modes')
		->add_html_node($this->get_display_modes_content_form());
		$interface_content_form->add_element('selector_name')
		->add_input_node('hidden', $this->name);
		
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		$interface_form = new interface_form('selector_form');
		$interface_form->set_label($this->label);
		$interface_form->set_object_id($this->id)
		->set_content_form($this->get_content_form())
		->set_table_name('selectors');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $selector_name;
		global $selectors_tabs_ui_parameters_tabs;
		global $selector_display_modes;
		
		$this->name = stripslashes($selector_name);
		
		$this->parameters_tabs = array();
		$parameters_tabs = $this->get_default_parameters_tabs();
		foreach ($parameters_tabs as $name => $parameters_tab) {
			$this->parameters_tabs[$name] = $parameters_tab;
			if(isset($selectors_tabs_ui_parameters_tabs[$name]['visible_gestion'])) {
				$this->parameters_tabs[$name]['visible_gestion'] = $selectors_tabs_ui_parameters_tabs[$name]['visible_gestion'];
			}
			$this->parameters_tabs[$name]['default_selected_gestion'] = 0;
			if($selectors_tabs_ui_parameters_tabs['default_selected_gestion'] == $name) {
				$this->parameters_tabs[$name]['default_selected_gestion'] = 1;
			}
			if(isset($selectors_tabs_ui_parameters_tabs[$name]['visible_opac'])) {
				$this->parameters_tabs[$name]['visible_opac'] = $selectors_tabs_ui_parameters_tabs[$name]['visible_opac'];
			}
			$this->parameters_tabs[$name]['default_selected_opac'] = 0;
			if($selectors_tabs_ui_parameters_tabs['default_selected_opac'] == $name) {
				$this->parameters_tabs[$name]['default_selected_opac'] = 1;
			}
		}
		
		$this->display_modes = array();
		$display_modes = $this->get_default_display_modes();
		foreach ($display_modes as $name => $display_mode) {
			$this->display_modes[$name] = $display_mode;
			if(!empty($selector_display_modes[$name])) {
				$this->display_modes[$name] = $selector_display_modes[$name];
			}
		}
	}
	
	public function save() {
		if($this->is_in_database()) {
			$query = 'update selectors set ';
			$where = 'where selector_name= "'.addslashes($this->name).'"';
		} else {
			$query = 'insert into selectors set ';
			$query .= 'selector_name= "'.addslashes($this->name).'", ';
			$where = '';
		}
		$query .= '
				selector_parameters_tabs = "'.addslashes(encoding_normalize::json_encode($this->parameters_tabs)).'",
				selector_display_modes = "'.addslashes(encoding_normalize::json_encode($this->display_modes)).'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_default_parameters_tab($visible_gestion=1, $default_selected_gestion=0, $visible_opac=1, $default_selected_opac=0) {
		return array(
				'visible_gestion' => $visible_gestion, 
				'default_selected_gestion' => $default_selected_gestion,
				'visible_opac' => $visible_opac,
				'default_selected_opac' => $default_selected_opac
		);
	}
	
	public function get_default_parameters_tabs() {
		switch ($this->name) {
			case 'author':
			case 'collection':
			case 'indexint':
			case 'publisher':
			case 'serie':
			case 'subcollection':
			case 'titre_uniforme':
				return array(
						'simple_search' => $this->get_default_parameters_tab(1, 1, 1, 1),
						'advanced_search' => $this->get_default_parameters_tab(),
						'add' => $this->get_default_parameters_tab(),
				);
			case 'category':
				return array(
						'hierarchical_search' => $this->get_default_parameters_tab(1, 1, 1, 1),
						'terms_search' => $this->get_default_parameters_tab(),
						'indexation_auto' => $this->get_default_parameters_tab(),
						'simple_search' => $this->get_default_parameters_tab(),
						'advanced_search' => $this->get_default_parameters_tab(),
						'add' => $this->get_default_parameters_tab(),
				);
			default:
				return array();
		}
	}
	
	public function get_default_display_modes() {
		return array(
				'record' => 'tab',
				'serial' => 'tab',
				'bulletin' => 'tab',
				'analysis' => 'tab',
		);
	}
	
	public static function delete($name) {
		$query = 'DELETE FROM selectors WHERE selector_name = "'.addslashes($name).'"';
		pmb_mysql_query($query);
		return true;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_label() {
		global $msg;
		if(!isset($this->label)) {
			if(!empty($msg['selector_'.$this->name])) {
				$this->label = $msg['selector_'.$this->name];
			} else {
				$this->label = $this->name;
			}
		}
		return $this->label;
	}
	
	public function get_parameters_tabs() {
		if(!isset($this->parameters_tabs)) {
			$this->parameters_tabs = $this->get_default_parameters_tabs();
		}
		return $this->parameters_tabs;
	}
	
	public function set_parameters_tabs($parameters_tabs) {
		$this->parameters_tabs = $parameters_tabs;
		return $this;
	}
	
	public function get_display_mode($type) {
		return $this->get_display_modes()[$type];
	}
	
	public function get_display_modes() {
		if(!isset($this->display_modes)) {
			$this->display_modes = $this->get_default_display_modes();
		}
		return $this->display_modes;
	}
	
	public function set_display_modes($display_modes) {
		$this->display_modes = $display_modes;
		return $this;
	}
	
	public function is_in_database() {
		$query = 'SELECT * FROM selectors
			WHERE selector_name = "'.addslashes($this->name).'"';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}
	
	public function is_substituted() {
		if($this->id) {
			return true;
		}
		return false;
	}
	
	public static function get_instance($name) {
		if(!isset(static::$instances[$name])) {
			static::$instances[$name] = new selector_model($name);
		}
		return static::$instances[$name];
	}
	
} // end of selector