<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selectors_controller.class.php,v 1.2 2021/10/26 09:27:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/selectors/selector_model.class.php");

class selectors_controller extends lists_controller {
	
	protected static $model_class_name = 'selector_model';
	
	protected static $list_ui_class_name = 'list_selectors_ui';
	
	public static function proceed($id=0) {
		global $action;
		global $name;
		
		$id = intval($id);
		switch($action){
			case 'edit':
				if(isset($name) && $name) {
					$model_instance = new selector_model($name);
					print $model_instance->get_form();
				}
				break;
			case 'save':
				$model_instance = new selector_model($name);
				$model_instance->set_properties_from_form();
				$model_instance->save();
				
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'delete':
				$model_class_name = static::$model_class_name;
				$model_class_name::delete($name);
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default :
				parent::proceed($id);
				break;
		}
	}
}