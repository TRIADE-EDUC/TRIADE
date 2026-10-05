<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_datalist.class.php,v 1.1 2023/09/13 08:13:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_datalist extends interface_node {
	
	protected $options = [];
	
	public function get_display() {
		global $charset;
		
		$display = "<datalist id=\"".$this->name."\"";
		$attr = $this->get_display_attributes();
		if ($attr) {
		    $display .= "$attr ";
		}
		$display.=">\n";
		if(count($this->options)) {
		    foreach ($this->options as $label) {
		        $display .= "<option>".htmlentities($label,ENT_QUOTES, $charset)."</option>\n";
		        
		    }
		}
		$display .= "</datalist>\n";
		return $display;
	}
	
	public function get_options() {
	    return $this->options;
	}
	
	public function set_options($options) {
	    $this->options = $options;
	    return $this;
	}
}