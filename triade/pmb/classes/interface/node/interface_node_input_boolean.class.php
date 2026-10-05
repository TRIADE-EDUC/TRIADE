<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_boolean.class.php,v 1.4 2023/09/08 06:06:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_boolean extends interface_node_input {
	
	protected $type = 'checkbox';
	
	protected $value = '1';
	
	protected $class = 'checkbox';
	
	protected $checked = false;
	
	protected $click = '';
	
	public function get_display() {
		global $charset;
		
		$display = "
		<input type='".$this->type."'
				id='".$this->id."'
				name='".$this->name."'
				value='".htmlentities($this->value, ENT_QUOTES, $charset)."'
				class='".$this->class."'
				".(!empty($this->checked) ? "checked='checked'" : "")."
                ".(!empty($this->click) ? "onclick=\"".$this->click."\"" : "")."
				".$this->get_display_attributes()." />";
		if(empty($this->label) && $this->class == 'switch') {
			$display .= " <label class='etiquette' for='".$this->name."'>&nbsp;</label>";
		} elseif(!empty($this->label)) {
			$display .= " <label class='etiquette' for='".$this->name."'>".htmlentities($this->label, ENT_QUOTES, $charset)."</label>";
		}
		return $display;
	}
	
	public function set_checked($checked) {
		$this->checked = $checked;
		return $this;
	}
	
	public function set_click($function) {
	    $this->click = $function;
	    return $this;
	}
}