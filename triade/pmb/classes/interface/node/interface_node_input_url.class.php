<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_url.class.php,v 1.1.2.2 2025/02/27 10:04:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_url extends interface_node_input_text {
	
	public function get_display() {
		global $msg, $charset;
		
		$display = "
		<input type='".$this->type."'
				id='".$this->id."'
				name='".$this->name."'
				value='".htmlentities($this->value, ENT_QUOTES, $charset)."'
				class='".$this->class."'
                ".(!empty($this->maxlength) ? "maxlength='".$this->maxlength."'" : "")."
                ".(!empty($this->disabled) ? "disabled='disabled'" : "")."
				".$this->get_display_attributes()." />";
		$display .= "<input class='bouton' type='button' onClick=\"check_link('".$this->name."')\" title='".htmlentities($msg["CheckLink"], ENT_QUOTES, $charset)."' value='".htmlentities($msg["CheckButton"], ENT_QUOTES, $charset)."' />";
		if(!empty($this->label)) {
			$display .= " <label class='etiquette' for='".$this->name."'>".htmlentities($this->label, ENT_QUOTES, $charset)."</label>";
		}
		return $display;
	}
}