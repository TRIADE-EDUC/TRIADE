<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: barcodes_sheets_controller.class.php,v 1.1 2021/07/19 13:17:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/barcodes_sheets/barcodes_sheet.class.php");

class barcodes_sheets_controller extends lists_controller {
	
	protected static $model_class_name = 'barcodes_sheet';
	
	protected static $list_ui_class_name = 'list_barcodes_sheets_ui';
	
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