<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: form.class.php,v 1.3.4.1 2025/03/28 07:51:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/users.class.php");
require_once($include_path."/templates/forms/form.tpl.php");

/**
 * class module
 * Un module
 */
class form {
	
	protected $id;
	
	protected $label;
	
	protected $name;
	
	protected $model_name;
	
	protected $module;
	
	protected $autorisations;
	
	protected $autorisations_all;
	
	protected $duplicable;
	
	protected $deletable_on_auth;
	
	protected $categ;
	
	protected $sub;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data(){
		$this->autorisations = '';
		$this->autorisations_all = 1;
		$this->duplicable = false;
		$this->deletable_on_auth = false;
		if(!$this->id) return;
		
		$query = 'SELECT * FROM forms WHERE id_form = '.$this->id;
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->model_name = $data->form_model_name;
		$this->module = $data->form_module;
		$this->autorisations = $data->form_autorisations;
		$this->autorisations_all = $data->form_autorisations_all;
		$this->duplicable = $data->form_duplicable;
		$this->deletable_on_auth = $data->form_deletable_on_auth;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		
		$interface_content_form->add_element('form_autorisations_all', 'form_autorisations_all', 'flat')
		->add_input_node('boolean', $this->autorisations_all);
		
		$interface_content_form->add_inherited_element('permissions_users', 'tab_autorisations', 'tab_autorisations')
		->set_autorisations($this->autorisations)
		->set_on_create(($this->id ? 0 : 1));
		
		$interface_content_form->add_element('form_duplicable', 'form_duplicable', 'flat')
		->add_input_node('boolean', $this->duplicable);
		
// 		$interface_content_form->add_element('form_deletable_on_auth', 'form_deletable_on_auth', 'flat')
// 		->add_input_node('boolean', $this->deletable_on_auth);
		
		$interface_content_form->add_element('form_model_name')
		->add_input_node('hidden', $this->model_name);
		$interface_content_form->add_element('form_module')
		->add_input_node('hidden', $this->module);
		
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_form('form_form');
		$interface_form->set_label($msg['form_form_edit']." : ".$this->get_label());
		$interface_form->set_object_id($this->id)
		->set_content_form($this->get_content_form())
		->set_table_name('forms');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_model_name, $form_module;
		global $autorisations, $form_autorisations_all, $form_duplicable, $form_deletable_on_auth;
		
		$this->model_name = stripslashes($form_model_name);
		$this->module = stripslashes($form_module);
		if (is_array($autorisations)) {
			$this->autorisations=implode(" ",$autorisations);
		} else {
			$this->autorisations="";
		}
		$this->autorisations_all = intval($form_autorisations_all);
		$this->duplicable = intval($form_duplicable);
		$this->deletable_on_auth = intval($form_deletable_on_auth);
	}
	
	public function save() {
		if($this->id) {
			$query = 'update forms set ';
			$where = 'where id_form= '.$this->id;
		} else {
			$query = 'insert into forms set ';
			$where = '';
		}
		$query .= '
				form_model_name = "'.addslashes($this->model_name).'",
				form_module = "'.addslashes($this->module).'",
				form_autorisations = "'.addslashes($this->autorisations).'",
				form_autorisations_all = "'.$this->autorisations_all.'",
				form_duplicable = "'.$this->duplicable.'",
				form_deletable_on_auth = "'.$this->deletable_on_auth.'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			return true;
		} else {
			return false;
		}
	}
	
	public static function delete($id) {
		$id = intval($id);
		$query = 'DELETE FROM forms WHERE id_form = '.$id;
		pmb_mysql_query($query);
		return true;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		if(empty($this->label) && !empty($this->model_name) && !empty($this->module)) {
			$this->label = list_forms_ui::get_instance()->get_label_from_object($this);
		} else {
			$this->label = '';
		}
		return $this->label;
	}
	
	public function get_name() {
	    return $this->name;
	}
	
	public function set_name($name) {
	    $this->name = $name;
	    return $this;
	}
	
	public function get_model_name() {
		return $this->model_name;
	}
	
	public function set_model_name($model_name) {
		$this->model_name = $model_name;
		return $this;
	}
	
	public function get_module() {
		return $this->module;
	}
	
	public function set_module($module) {
		$this->module = $module;
		return $this;
	}
	
	public function get_autorisations() {
		return $this->autorisations;
	}
	
	public function set_autorisations($autorisations) {
		$this->autorisations = $autorisations;
		return $this;
	}
	
	public function get_autorisations_all() {
		return $this->autorisations_all;
	}
	
	public function set_autorisations_all($autorisations_all) {
		$this->autorisations_all = $autorisations_all;
		return $this;
	}
	
	public function get_duplicable() {
		return $this->duplicable;
	}
	
	public function set_duplicable($duplicable) {
		$this->duplicable = $duplicable;
		return $this;
	}
	
	public function get_deletable_on_auth() {
		return $this->deletable_on_auth;
	}
	
	public function set_deletable_on_auth($deletable_on_auth) {
		$this->deletable_on_auth = $deletable_on_auth;
		return $this;
	}
	
	public function get_categ() {
		return $this->categ;
	}
	
	public function set_categ($categ) {
		$this->categ = $categ;
		return $this;
	}
	
	public function get_sub() {
		return $this->sub;
	}
	
	public function set_sub($sub) {
		$this->sub = $sub;
		return $this;
	}
	
	public function is_in_database() {
		if($this->id) {
			return true;
		}
		$query = 'SELECT * FROM forms
			WHERE form_model_name = "'.addslashes($this->model_name).'"
			AND form_module = "'.addslashes($this->module).'"';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$data = pmb_mysql_fetch_object($result);
			$this->id = $data->id_form;
			$this->fetch_data();
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
} // end of form