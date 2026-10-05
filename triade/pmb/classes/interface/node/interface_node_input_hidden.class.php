<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_hidden.class.php,v 1.2 2023/06/28 07:53:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_hidden extends interface_node_input_text {
	
	protected $type = 'hidden';
	
	protected $class = '';
}