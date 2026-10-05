<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: forms_controller.class.php,v 1.1 2022/07/22 15:29:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/forms/form.class.php");

class forms_controller extends lists_controller {
	
	protected static $model_class_name = 'form';
	
	protected static $list_ui_class_name = 'list_forms_ui';
	
	public static function proceed($id=0) {
		global $action;
		global $form_model_name, $form_module;
		
		$id = intval($id);
		switch($action){
			case 'edit':
				$list_ui_instance = static::get_list_ui_instance();
				$objects = $list_ui_instance->get_objects();
				if($id) {
					$model_instance = static::get_model_instance($id);
					print $model_instance->get_form();
				} else {
					foreach ($objects as $object) {
						if($object->get_model_name() == $form_model_name && $object->get_module() == $form_module) {
							print $object->get_form();
						}
					}
				}
				break;
			default :
				parent::proceed($id);
				break;
		}
	}
}