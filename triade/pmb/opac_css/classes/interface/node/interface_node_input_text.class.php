<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_text.class.php,v 1.2 2023/12/20 10:57:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_text extends interface_node_input {
	
	protected $type = 'text';
	
	protected $class = 'saisie-50em';
	
	protected $size = 0;
	
	protected $maxlength = 0;
	
	public function get_display() {
		global $charset;
		
		$display = "
		<input type='".$this->type."'
				id='".$this->id."'
				name='".$this->name."'
				value='".htmlentities($this->value, ENT_QUOTES, $charset)."'
				class='".$this->class."'
                ".(!empty($this->size) ? "size='".$this->size."'" : "")."
				".(!empty($this->maxlength) ? "maxlength='".$this->maxlength."'" : "")."
				".$this->get_display_attributes()." />";
		if(!empty($this->label)) {
			$display .= " <label class='etiquette' for='".$this->name."'>".htmlentities($this->label, ENT_QUOTES, $charset)."</label>";
		}
		return $display;
	}
	
	public function get_size() {
	    return $this->size;
	}
	
	public function set_size($size) {
	    $this->size = intval($size);
	    return $this;
	}
	
	public function get_maxlength() {
		return $this->maxlength;
	}
	
	public function set_maxlength($maxlength) {
		$this->maxlength = intval($maxlength);
		return $this;
	}
}