<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: logs_controller.class.php,v 1.3 2023/07/13 06:41:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/log.class.php");

class logs_controller extends lists_controller {
	
	protected static $model_class_name = 'log';
	
	protected static $list_ui_class_name = 'list_logs_ui';
	
	public static function proceed($id=0) {
		global $action;
		
		$model_class_name = static::$model_class_name;
		switch ($action) {
			case 'clean':
				$model_class_name::clean();
				static::redirect_display_list();
				break;
			default:
				$model_class_name::purge();
				parent::proceed($id);
				break;
		}
		
	}
	
}