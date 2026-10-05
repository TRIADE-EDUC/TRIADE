<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_date.class.php,v 1.1 2018/05/18 12:24:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_date {
	
	protected $dom_node_id;
	
	protected $dom_node_name;
	
	protected $value;
	
	protected $required;
	
	public function __construct($dom_node_id = '', $dom_node_name = ''){
		$this->dom_node_id = $dom_node_id;
		$this->dom_node_name = ($dom_node_name ? $dom_node_name : $dom_node_id);
		$this->value = today();
		$this->required = true;
	}
	
	public function get_display() {
		global $msg, $charset;
		
		$display = "
		<input type='text' style='width: 10em;' name='".$this->dom_node_name."' id='".$this->dom_node_id."' value='".$this->value."' data-form-name='' 
			data-dojo-type='dijit/form/DateTextBox' required='".(($this->required == 1) ? 'true' : 'false')."' constraints=\"{datePattern:'".getDojoPattern($msg['format_date'])."'}\" />	
		<input class='bouton' type='button' value='X' onClick='empty_dojo_calendar_by_id(\"".$this->dom_node_id."\"); '/>";
		return $display;
	}
	
	public function get_dom_node_id() {
		return $this->dom_node_id;
	}
	
	public function get_dom_node_name() {
		return $this->dom_node_name;
	}
	
	public function get_value() {
		return $this->value;
	}
	
	public function is_required() {
		return $this->required;
	}
	
	public function set_dom_node_id($dom_node_id) {
		$this->dom_node_id = $dom_node_id;
		return $this;
	}
	
	public function set_dom_node_name($dom_node_name) {
		$this->dom_node_name = $dom_node_name;
		return $this;
	}
	
	public function set_value($value) {
		$this->value = $value;
		return $this;
	}
	
	public function set_required($required) {
		$this->required = $required;
		return $this;
	}
}