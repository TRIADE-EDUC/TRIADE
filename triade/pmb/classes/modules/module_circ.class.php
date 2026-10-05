<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_circ.class.php,v 1.3 2021/04/28 06:52:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/modules/module.class.php");

class module_circ extends module{
	
	public function get_left_menu() {
		global $categ;
		if ((SESSrights & RESTRICTCIRC_AUTH) && ($categ!="pret") && ($categ!="pretrestrict") ) {
			return '';
		} else {
			return $this->get_display_tabs();
		}
	}
	
}