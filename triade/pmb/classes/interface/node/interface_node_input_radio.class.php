<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_radio.class.php,v 1.5 2023/07/13 11:49:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_radio extends interface_node_input {
	
	protected $type = 'radio';
	
	protected $checked = false;
	
	public function get_display() {
		global $charset;
		
		$display = "
		<input type='".$this->type."'
				id='".$this->id."_".$this->value."'
				name='".$this->name."'
				value='".htmlentities($this->value, ENT_QUOTES, $charset)."'
				class='".$this->class."'
				".(!empty($this->checked) ? "checked='checked'" : "")."
				".$this->get_display_attributes()." />";
		if(!empty($this->label)) {
			$display .= " <label class='etiquette' for='".$this->name."_".$this->value."'>".htmlentities($this->label, ENT_QUOTES, $charset)."</label>";
		}
		return $display;
	}
	
	public function set_checked($checked) {
		$this->checked = $checked;
		return $this;
	}
	
}