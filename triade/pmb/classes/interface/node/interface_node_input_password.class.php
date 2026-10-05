<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_password.class.php,v 1.2 2023/09/02 09:30:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_password extends interface_node_input {
	
	protected $type = 'password';
	
	protected $class = 'saisie-20em';
	
	protected $maxlength = 0;
	
	protected $placeholder = '';
	
	protected $autocomplete = 'off';
	
	public function get_display() {
		global $charset;
		
		$display = "
		<input type='".$this->type."'
				id='".$this->id."'
				name='".$this->name."'
				value='".htmlentities($this->value, ENT_QUOTES, $charset)."'
				class='".$this->class."'
				".(!empty($this->maxlength) ? "maxlength='".$this->maxlength."'" : "")."
				".(!empty($this->placeholder) ? "placeholder='".htmlentities($this->placeholder, ENT_QUOTES, $charset)."'" : "")."
				".$this->get_display_attributes()." 
				autocomplete='".$this->autocomplete."' />";
		$display .= "<span class='fa fa-eye' onclick='toggle_password(this, \"".$this->name."\");'></span>";
		return $display;
	}
	
	public function get_maxlength() {
		return $this->maxlength;
	}
	
	public function set_maxlength($maxlength) {
		$this->maxlength = intval($maxlength);
		return $this;
	}
	
	public function set_placeholder($placeholder) {
		$this->placeholder = $placeholder;
		return $this;
	}
	
	public function set_autocomplete($autocomplete) {
		$this->autocomplete = $autocomplete;
		return $this;
	}
}