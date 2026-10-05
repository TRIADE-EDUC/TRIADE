<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannettes_diffusions_controller.class.php,v 1.1 2023/03/07 14:51:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/dsi/bannettes_controller.class.php");

class bannettes_diffusions_controller extends lists_controller {
	
	protected static $model_class_name = 'bannette_diffusion';
	protected static $list_ui_class_name = 'list_bannettes_diffusions_ui';
	
	public static function proceed($id=0) {
		global $suite;
		
		switch($suite) {
			case 'view':
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_display_view();
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
	
}// end class
