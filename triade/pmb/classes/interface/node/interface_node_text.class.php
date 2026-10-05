<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_text.class.php,v 1.1.4.2 2025/05/13 15:23:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_text extends interface_node {
	protected $content = '';
	
	public function get_display() {
	    global $charset;
	    
	    return htmlentities($this->value, ENT_QUOTES, $charset);
	}
}