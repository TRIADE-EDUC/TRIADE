<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node.class.php,v 1.1 2023/12/20 08:52:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node {
	
	protected $id;
	
	protected $name;
	
	protected $label;
	
	protected $label_code;
	
	protected $value = '';
	
	protected $class = '';
	
	protected $attributes;
	
	public function __construct($name = ''){
		$this->name = $name;
		$this->id = $name;
	}
	
	public function get_display_attributes() {
		global $charset;
		
		$display = '';
		if(!empty($this->attributes)) {
			foreach($this->attributes as $attr=>$value) {
				if($display) {
					$display .= " ";
				}
				$display .= $attr."='".htmlentities($value, ENT_QUOTES, $charset)."'";
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
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_label_code() {
		return $this->label_code;
	}
	
	public function get_value() {
		return $this->value;
	}
	
	public function get_class() {
		return $this->class;
	}
	
	public function set_id($id) {
		$this->id = $id;
		return $this;
	}
	
	public function set_name($name) {
		$this->name = $name;
		return $this;
	}
	
	public function set_label($label) {
		$this->label = $label;
		return $this;
	}
	
	public function set_label_code($label_code) {
		global $msg;
		
		$this->label_code = $label_code;
		$this->label = $msg[$label_code] ?? '';
		return $this;
	}
	
	public function set_value($value) {
		$this->value = $value;
		return $this;
	}
	
	public function set_class($class) {
		$this->class = $class;
		return $this;
	}
	
	public function set_attributes($attributes) {
		$this->attributes = $attributes;
		return $this;
	}
}