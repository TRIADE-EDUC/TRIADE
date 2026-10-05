<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_html.class.php,v 1.1 2023/06/23 14:03:27 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_html extends interface_node {
	protected $content = '';
	
	public function get_display() {
		return $this->content;
	}
	
	public function get_content() {
		return $this->content;
	}
	
	public function set_content($content) {
		$this->content = $content;
		return $this;
	}
}