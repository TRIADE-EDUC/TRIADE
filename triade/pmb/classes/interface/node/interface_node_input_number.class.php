<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_number.class.php,v 1.1 2023/07/04 09:58:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_number extends interface_node_input_text {
	
	protected $type = 'number';
	
	protected $class = 'saisie-5em';
	
}