<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_button.class.php,v 1.1.2.3 2025/02/20 15:18:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_button extends interface_node {
	
    protected $class = 'bouton';
    
    protected $title = '';
    
    protected $onclick = '';
    
    protected $aria_label = '';
    
	public function get_display() {
		global $charset;
		global $opac_rgaa_active;
		
		$standard_attributes = " 
            id='".$this->id."' 
            name='".$this->name."' 
            class='".$this->class."'
        ";
		if (!empty($this->title)) {
		    $standard_attributes .= " title='".htmlentities($this->title, ENT_QUOTES, $charset)."'";
		}
		if (!empty($this->onclick)) {
		    $standard_attributes .= " onClick=\"".$this->onclick."\"";
		}
		if (!empty($this->aria_label)) {
		    $standard_attributes .= " aria-label='".htmlentities($this->aria_label, ENT_QUOTES, $charset)."'";
		}
		if ($opac_rgaa_active) {
		    $display = "
            <button type='button' ".$standard_attributes." ".$this->get_display_attributes().">
                ".htmlentities($this->value, ENT_QUOTES, $charset)."
            </button>";
		} else {
		    $display = "<input type='button' ".$standard_attributes." value='".htmlentities($this->value, ENT_QUOTES, $charset)."' ".$this->get_display_attributes()." />";
		}
		return $display;
	}
	
	public function get_title() {
	    return $this->title;
	}
	
	public function set_title($title) {
	    $this->title = $title;
	    return $this;
	}
	
	public function get_onclick() {
	    return $this->onclick;
	}
	
	public function set_onclick($onclick) {
	    $this->onclick = $onclick;
	    return $this;
	}
	
	public function set_aria_label($aria_label) {
	    $this->aria_label = $aria_label;
	    return $this;
	}
}