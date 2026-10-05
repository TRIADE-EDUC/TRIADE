<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_img.class.php,v 1.1 2023/06/27 13:44:41 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_img extends interface_node {
	
	protected $src = '';
	
	protected $alt = '';
	
	public function get_display() {
		global $charset;
		
		$display = "<img class='" . $this->class . "' src='" . htmlentities($this->src, ENT_QUOTES, $charset) . "'
			alt='" . htmlentities($this->alt, ENT_QUOTES, $charset) . "'>";
		return $display;
	}
	
	public function get_src() {
		return $this->src;
	}
	
	public function get_alt() {
		return $this->alt;
	}
	
	public function set_src($src) {
		$this->src = $src;
		return $this;
	}
	
	public function set_alt($alt) {
		$this->alt = $alt;
		return $this;
	}
}