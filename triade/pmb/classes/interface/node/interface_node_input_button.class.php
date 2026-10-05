<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_button.class.php,v 1.2 2023/06/27 13:44:41 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_button extends interface_node_input {
	
	protected $type = 'button';
	protected $click = '';
	protected $class = 'bouton';
	
	public function get_display() {
		global $charset;
		
		$display = "
		<input type='".$this->type."'
				id='".$this->id."'
				name='".$this->name."'
				value='".htmlentities($this->value, ENT_QUOTES, $charset)."'
				class='".$this->class."'
				onclick=\"".$this->click."\"
				".$this->get_display_attributes()." />";
		if(!empty($this->label)) {
			$display .= " ".htmlentities($this->label, ENT_QUOTES, $charset);
		}
		return $display;
	}
	
	public function set_click($function) {
		$this->click = $function;
		return $this;
	}
	
}