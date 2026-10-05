<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheets_controller.class.php,v 1.2 2021/02/01 13:29:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/sticks_sheet/sticks_sheet.class.php");

class sticks_sheets_controller extends lists_controller {
	
	protected static $model_class_name = 'sticks_sheet';
	
	protected static $list_ui_class_name = 'list_sticks_sheets_ui';
	
	public static function proceed($id=0) {
		global $action;
		
		$id = intval($id);
		switch ($action) {
			case 'add':
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form();
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
}