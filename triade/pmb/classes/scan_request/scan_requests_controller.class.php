<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests_controller.class.php,v 1.2 2021/11/29 13:07:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/scan_request/scan_request.class.php");

class scan_requests_controller extends lists_controller {
	
	protected static $model_class_name = 'scan_request';
	
	protected static $list_ui_class_name = 'list_scan_requests_ui';
	
	public static function proceed($id=0) {
		global $action, $sub;
		global $base_path;
		
		$id = intval($id);
		switch ($action) {
			case 'save':
				$model_instance = static::get_model_instance($id);
				$model_instance->get_values_from_form();
				$model_instance->save();
				$action = "";
				print '<META HTTP-EQUIV="Refresh" Content="0; URL='.$base_path.'/circ.php?categ=scan_request&sub=list">';
				exit;
				break;
			case 'delete':
				$model_instance = static::get_model_instance($id);
				$model_instance->delete();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				if($sub == 'request') {
					$model_instance = static::get_model_instance($id);
					print $model_instance->get_form();
				} else {
					parent::proceed($id);
				}
		}
	}
	
}