<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acces_controller.class.php,v 1.1 2022/12/26 13:19:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/acces.class.php");

class acces_controller extends lists_controller {
	
	protected static $model_class_name = 'acces';
	
	protected static $dom;
	
	public static function set_dom($dom) {
		static::$dom = $dom;
	}
}
