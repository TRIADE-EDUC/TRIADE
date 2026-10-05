<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_select.class.php,v 1.1 2019/11/14 11:16:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_select {
	
	protected $name;
	
	protected $options;
	
	protected $selected;
	
	protected $multiple;

	protected $onchange;
	
	public function __construct($name = ''){
		$this->name = $name;
		$this->_init_properties();
	}
	
	protected function _init_properties() {
	    $this->options = array();
	    $this->selected = '';
	    $this->multiple = 0;
	    $this->onchange = '';
	}
	
	public function get_display($liste_vide_code, $liste_vide_info,$option_premier_code,$option_premier_info,$attr='') {
		global $charset;
		
		$display = "<select name=\"".$this->name."\" id=\"".$this->name."\" onChange=\"".$this->onchange."\" ";
		if ($this->multiple) {
		    $display .= "multiple ";
		}
		if ($attr) {
		    $display .= "$attr ";
		}
		$display.=">\n";
		if(count($this->options)) {
		    if ($option_premier_info!="") {
		        $display .= "<option value=\"$option_premier_code\" ";
		        if ($this->selected==$option_premier_code) {
		            $display .= "selected=\"selected\"";
		        }
		        $display .= ">".htmlentities($option_premier_info, ENT_QUOTES, $charset)."</option>\n";
		    }
		    foreach ($this->options as $value => $label) {
		        $display .= "<option value=\"".$value."\" ";
		        if ($this->selected == $value) $display.="selected=\"selected\"";
		        $display.=">".htmlentities($label,ENT_QUOTES, $charset)."</option>\n";
		        
		    }
		} else {
		    $display .= "<option value=\"$liste_vide_code\">".htmlentities($liste_vide_info, ENT_QUOTES, $charset)."</option>\n";
		}
		$display .= "</select>\n";
		return $display;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_options() {
	    return $this->options;
	}
	
	public function get_selected() {
		return $this->selected;
	}
	
	public function set_name($name) {
		$this->name = $name;
		return $this;
	}
	
	public function set_options($options) {
	    $this->options = $options;
	    return $this;
	}
	
	public function set_selected($selected) {
	    $this->selected = $selected;
		return $this;
	}
	
	public function set_multiple($multiple) {
	    $this->multiple = $multiple;
	    return $this;
	}
	
	public function set_onchange($onchange) {
	    $this->onchange = $onchange;
	    return $this;
	}
}