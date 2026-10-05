<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_model.class.php,v 1.6 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/modules/module_model.tpl.php");

/**
 * class module_model
 * Un module
 */
class module_model {
	
	protected $id;
	
	protected $name;
	
	protected $label;
	
	protected $title;
	
	protected $accesskey;
	
	protected $display_mode;
	
	protected $destination_link;
		
	public function __construct($name='') {
		$this->id = 0;
		$this->name = $name;
		$this->fetch_data();
	}
	
	protected function _init_destination_link() {
		if(empty($this->destination_link)) {
			$this->destination_link = '';
			$list_modules_ui = list_modules_ui::get_instance();
			$objects = $list_modules_ui->get_objects();
			foreach ($objects as $object) {
				if($object->get_name() == $this->name) {
					$this->destination_link = $object->get_destination_link();
				}
			}
		}
	}
	
	protected function fetch_data(){
		$this->destination_link = '';
		if(!$this->table_exists()) {
			return false;
		}
		$query = 'SELECT * FROM modules WHERE module_name = "'.addslashes($this->name).'"';
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->id = 1;
		$this->destination_link = $data->module_destination_link;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('module_destination_link', 'module_destination_link')
		->add_input_node('text', $this->destination_link);
		$interface_content_form->add_element('module_name')
		->add_input_node('hidden', $this->name);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$this->_init_destination_link();
		
		$interface_form = new interface_form('module_form');
		$interface_form->set_label($msg['module_form_edit']." : ".$this->name);
		$interface_form->set_object_id($this->id)
		->set_content_form($this->get_content_form())
		->set_table_name('modules');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $module_name;
		global $module_destination_link;
		
		$this->name = stripslashes($module_name);
		$this->destination_link = stripslashes($module_destination_link);
	}
	
	public function save() {
		if($this->is_in_database()) {
			$query = 'update modules set ';
			$where = 'where module_name= "'.addslashes($this->name).'"';
		} else {
			$query = 'insert into modules set ';
			$query .= 'module_name= "'.addslashes($this->name).'", ';
			$where = '';
		}
		$query .= '
				module_destination_link = "'.addslashes($this->destination_link).'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function delete($name) {
		$query = 'DELETE FROM modules WHERE module_name = "'.addslashes($name).'"';
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
		return $this->label;
	}
	
	public function get_title() {
		return $this->title;
	}
	
	public function get_accesskey() {
		return $this->accesskey;
	}
	
	public function get_display_mode() {
		return $this->display_mode;
	}
	
	public function get_destination_link() {
		return $this->destination_link;
	}
	
	public function set_label($label) {
		$this->label = $label;
		return $this;
	}
	
	public function set_title($title) {
		$this->title = $title;
		return $this;
	}
	
	public function set_accesskey($accesskey) {
		$this->accesskey = $accesskey;
		return $this;
	}
	
	public function set_display_mode($display_mode) {
		$this->display_mode = $display_mode;
		return $this;
	}
	
	public function set_destination_link($destination_link) {
		$this->destination_link = $destination_link;
		return $this;
	}
	
	public function is_in_database() {
		$query = 'SELECT * FROM modules
			WHERE module_name = "'.addslashes($this->name).'"';
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
	
	protected function table_exists() {
		$query = "SHOW TABLES LIKE 'modules'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}
} // end of module