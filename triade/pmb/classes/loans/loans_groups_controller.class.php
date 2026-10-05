<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: loans_groups_controller.class.php,v 1.1 2021/04/13 07:49:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/loans/loans_controller.class.php");

class loans_groups_controller extends loans_controller {
	
	protected static $list_ui_class_name = 'list_loans_groups_ui';
	
	
}