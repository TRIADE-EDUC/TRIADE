<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input.class.php,v 1.1 2023/12/20 08:52:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input extends interface_node {
	
	protected $type;
	
	protected $required = false;
	
	public function get_type() {
		return $this->type;
	}
	
	public function is_required() {
		return $this->required;
	}
	
	public function set_type($type) {
		$this->type = $type;
		return $this;
	}
	
	public function set_required($required) {
		$this->required = $required;
		return $this;
	}
}