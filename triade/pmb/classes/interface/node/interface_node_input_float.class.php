<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_float.class.php,v 1.1 2023/06/23 07:21:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_float extends interface_node_input_text {
	
	protected $class = 'saisie-10em';
	
}