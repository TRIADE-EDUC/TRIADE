<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: loans_controller.class.php,v 1.1 2021/04/13 07:49:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// require_once($class_path."/relance.class.php");
// require_once($class_path."/emprunteur.class.php");

class loans_controller extends lists_controller {
	
	protected static $model_class_name = 'exemplaire';
	
	protected static $list_ui_class_name = 'list_loans_ui';
	
	protected static $id_expl;
	
	public static function set_id_expl($id_expl) {
		static::$id_expl = intval($id_expl);
	}
	
}