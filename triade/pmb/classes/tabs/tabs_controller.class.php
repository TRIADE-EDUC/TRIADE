<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tabs_controller.class.php,v 1.4 2023/09/02 09:26:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/tabs/tab.class.php");

class tabs_controller extends lists_controller {
	
	protected static $model_class_name = 'tab';
	
	protected static $list_ui_class_name = 'list_tabs_ui';
	
	public static function proceed($id=0) {
		global $action;
		global $tab_module, $tab_categ, $tab_sub;
		
		if(empty($tab_module)) $tab_module ='admin';
		static::$list_ui_class_name = 'list_tabs_'.$tab_module.'_ui';
		static::$list_ui_class_name::set_module_name($tab_module);
		static::$list_ui_class_name::set_no_check_rights(1);
		$id = intval($id);
		switch($action){
			case 'edit':
				$list_ui_instance = static::get_list_ui_instance();
				$objects = $list_ui_instance->get_objects();
				if($id) {
					$model_instance = static::get_model_instance($id);
					foreach ($objects as $object) {
						if($object->get_categ() == $model_instance->get_categ() && $object->get_sub() == $model_instance->get_sub()) {
							$model_instance->set_label($object->get_label());
						}
					}
					print $model_instance->get_form();
				} else {
					foreach ($objects as $object) {
						if($object->get_categ() == $tab_categ && $object->get_sub() == $tab_sub) {
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
	
	public static function get_url_base() {
	    global $tab_module;
	    
	    if(empty($tab_module)) $tab_module ='admin';
	    return parent::get_url_base()."&tab_module=".$tab_module;
	}
}