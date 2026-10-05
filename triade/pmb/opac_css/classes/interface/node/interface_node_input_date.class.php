<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_node_input_date.class.php,v 1.1.4.2 2025/05/13 15:37:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_node_input_date extends interface_node_input_text {
	
	protected $type = 'date';
	
	protected $class = 'saisie-15em';
	
}