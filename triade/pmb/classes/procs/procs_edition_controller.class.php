<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: procs_edition_controller.class.php,v 1.1 2021/05/11 07:39:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/procs/procs_controller.class.php");

class procs_edition_controller extends procs_controller {
	
	protected static $list_ui_class_name = 'list_procs_edition_ui';
	
}