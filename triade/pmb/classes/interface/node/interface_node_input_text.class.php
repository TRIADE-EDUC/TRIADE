<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_text.class.php,v 1.4.4.1 2025/04/25 09:37:44 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_text extends interface_node_input {

	protected $type = 'text';

	protected $class = 'saisie-50em';

	protected $maxlength = 0;

	protected $disabled = false;

	public function get_display() {
		global $charset;

		$display = "
		<input type='".$this->type."'
				id='".$this->id."'
				name='".$this->name."'
				value='".htmlentities($this->value ?? '', ENT_QUOTES, $charset)."'
				class='".$this->class."'
				".(!empty($this->maxlength) ? "maxlength='".$this->maxlength."'" : "")."
                ".(!empty($this->disabled) ? "disabled='disabled'" : "")."
				".$this->get_display_attributes()." />";
		if(!empty($this->label)) {
			$display .= " <label class='etiquette' for='".$this->name."'>".htmlentities($this->label, ENT_QUOTES, $charset)."</label>";
		}
		return $display;
	}

	public function get_maxlength() {
		return $this->maxlength;
	}

	public function set_maxlength($maxlength) {
		$this->maxlength = intval($maxlength);
		return $this;
	}

	public function set_disabled($disabled) {
	    $this->disabled = $disabled;
	    return $this;
	}
}