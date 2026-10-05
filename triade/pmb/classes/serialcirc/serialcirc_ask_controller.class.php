<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask_controller.class.php,v 1.1 2023/01/09 14:44:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/serialcirc_ask.class.php");

class serialcirc_ask_controller extends lists_controller {
	
	protected static $model_class_name = 'serialcirc_ask';
	
	protected static $list_ui_class_name = 'list_serialcirc_ask_ui';
		
	public static function proceed($id=0) {
		global $action;
		global $asklist_id;
		
		$id = intval($id);
		switch ($action) {
			case 'accept':
				foreach($asklist_id as $ask_id){
					$model_instance = static::get_model_instance($ask_id);
					$model_instance->accept();
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'refus':
				foreach($asklist_id as $ask_id){
					$model_instance = static::get_model_instance($ask_id);
					$model_instance->refus();
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'delete':
				foreach($asklist_id as $ask_id){
					$model_instance = static::get_model_instance($ask_id);
					$model_instance->delete();
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
}