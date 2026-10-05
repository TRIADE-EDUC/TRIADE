<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analytics_service.class.php,v 1.3 2024/02/21 13:42:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once $include_path.'/templates/analytics_services/analytics_service.tpl.php';

class analytics_service{
	
	protected $id = 0;
	
	protected $name = "";
	
	protected $active = 0;
	
	protected $parameters = array();
	
	protected $template = "";
	
	protected $consent_template = "";
	
	public function __construct($id=0){
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $name;
		
		$query = "select * from analytics_services where id_analytics_service = '".$this->id."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$this->name = $row->analytics_service_name;
			$this->active = $row->analytics_service_active;
			$this->parameters = json_decode($row->analytics_service_parameters, true);
			$this->template = $row->analytics_service_template;
			$this->consent_template = $row->analytics_service_consent_template;
		} else {
			if(!empty($name)) {
				$this->name = $name;
			}
		}
	}
	
	public function get_label() {
		global $class_path;
		
		$class_name = 'analytics_service_'.$this->name;
		require_once $class_path.'/analytics_services/services/'.$this->name.'/'.$class_name.'.class.php';
		return $class_name::get_label();
	}
	
	protected function get_parameters_content_form() {
		global $class_path;
		
		$class_name = 'analytics_service_'.$this->name;
		require_once $class_path.'/analytics_services/services/'.$this->name.'/'.$class_name.'.class.php';
		return $class_name::get_parameters_content_form($this->parameters);
	}
	
	public function get_content_form() {
		global $msg, $charset;
		global $analytics_service_calculate_button;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('analytics_service_name', 'analytics_service_name')
		->add_input_node('hidden', $this->name);
		$interface_content_form->add_element('display_label')
		->add_html_node($this->name." (".$this->get_label().")");
		$interface_content_form->add_element('analytics_service_active', 'analytics_service_active')
		->add_input_node('boolean', $this->active)
		->set_class('switch');
		$interface_content_form->add_element('calculate_button')
		->add_html_node($analytics_service_calculate_button);
		$interface_content_form->add_element('parameters')
		->add_html_node($this->get_parameters_content_form());
		$interface_content_form->add_element('template_description')
		->add_html_node('<hr />'.htmlentities($msg['analytics_service_template_description'], ENT_QUOTES, $charset));
		$interface_content_form->add_element('analytics_service_template', 'analytics_service_template')
		->add_textarea_node($this->template)
		->set_cols(120)
		->set_rows(40);
		$interface_content_form->add_element('analytics_service_consent_template', 'analytics_service_consent_template')
		->add_textarea_node($this->consent_template)
		->set_cols(120)
		->set_rows(10);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		$interface_form = new interface_form('analytics_service_form');
		$interface_form->set_content_form($this->get_content_form());
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $analytics_service_name;
		global $analytics_service_active;
		global $analytics_service_parameters;
		global $analytics_service_template;
		global $analytics_service_consent_template;
		
		if(!empty($analytics_service_name)) {
			$this->name = stripslashes($analytics_service_name);
		}
		$this->active = intval($analytics_service_active);
		$this->parameters = stripslashes_array($analytics_service_parameters);
		$this->template = stripslashes($analytics_service_template);
		$this->consent_template = stripslashes($analytics_service_consent_template);
	}
	
	public function save() {
		global $class_path;
	
		if($this->name && file_exists($class_path.'/analytics_services/services/'.$this->name)) {
			$query = "select analytics_service_name from analytics_services where analytics_service_name = '".addslashes($this->name)."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$query = "update analytics_services set
					analytics_service_active = '".$this->active."',
					analytics_service_parameters = '".encoding_normalize::json_encode($this->parameters)."',
					analytics_service_template = '".addslashes($this->template)."',
					analytics_service_consent_template = '".addslashes($this->consent_template)."'
					where analytics_service_name = '".addslashes($this->name)."'";
			} else {
				$query = "insert into analytics_services set
					analytics_service_name = '".addslashes($this->name)."',
					analytics_service_active = '".$this->active."',
					analytics_service_parameters = '".json_encode($this->parameters)."',
					analytics_service_template = '".addslashes($this->template)."',
					analytics_service_consent_template = '".addslashes($this->consent_template)."'";
			}
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$query = "DELETE FROM analytics_services WHERE id_analytics_service = ".$id;
			pmb_mysql_query($query);
		}
		return true;
	}
	
	public function get_display_service() {
		$display = $this->template;
		$display .= $this->consent_template;
		
		if (!empty($this->parameters)) {
		    foreach ($this->parameters as $property=>$value) {
		        $display = str_replace(array('{{ '.$property.' }}', '{{'.$property.'}}'), $value, $display);
		    }
		}
		return $display; 
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_active() {
		return $this->active;
	}
	
	public function get_template() {
		return $this->template;
	}
	
	public function get_consent_template() {
		return $this->consent_template;
	}
	
	public function set_name($name) {
		$this->name = $name;
	}
	
	public function set_active($active) {
	    $this->active = intval($active);
	}
	
	public function set_template($template) {
		$this->template = $template;
	}
}