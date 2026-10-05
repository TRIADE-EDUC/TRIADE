<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_a.class.php,v 1.1 2023/06/20 06:55:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_a extends interface_node {
	
	protected $href;
	
	protected $target = '';
	
	public function get_display() {
		global $charset;
		
		$display = "
		<a href='".htmlentities($this->value, ENT_QUOTES, $charset)."' target='_blank'>".htmlentities($this->value, ENT_QUOTES, $charset)."</a>";
		return $display;
	}
	
	public function get_href() {
		return $this->href;
	}
	
	public function get_target() {
		return $this->target;
	}
	
	public function set_href($href) {
		$this->href = $href;
		return $this;
	}
	
	public function set_target($target) {
		$this->target = $target;
		return $this;
	}
}