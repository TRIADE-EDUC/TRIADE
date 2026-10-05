<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_zone.class.php,v 1.1 2023/07/13 14:11:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/translation.class.php');

class interface_zone {
	
	protected $name;
	
	protected $label;
	
	protected $label_code;
	
	protected $class;
	
	protected $elements;
	
	public function __construct($name = ''){
		$this->name = $name;
		$this->class = '';
		$this->elements = array();
	}
	
	public function add_element($element) {
		$this->elements[$element->get_name()] = $element;
	}
	
	public function get_display() {
		$display = '';
		if(!empty($this->class)) {
			$display .= "<div class='".$this->class."'>";
		}
		if(!empty($this->label)) {
			$display .= "
			<div class='row interface-zone-display-label'>
				<label class='etiquette' for='".$this->name."'><strong>".$this->label."</strong></label>
			</div>";
		}
		foreach ($this->elements as $element) {
			$display .= $element->get_display();
		}
		if(!empty($this->class)) {
			$display .= "</div>";
		}
		return $display;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_label() {
		return $this->label;
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
	
	public function set_class($class) {
		$this->class = $class;
		return $this;
	}
}