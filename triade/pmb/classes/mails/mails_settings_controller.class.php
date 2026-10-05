<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mails_settings_controller.class.php,v 1.3 2023/09/13 08:13:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/mails/mail_setting.class.php");

class mails_settings_controller extends lists_controller {
	
	protected static $model_class_name = 'mail_setting';
	
	protected static $list_ui_class_name = 'list_mails_settings_ui';
	
	public static function proceed($id=0) {
		global $action;
		global $classname;
		
		$id = intval($id);
		switch ($action) {
			case 'edit':
			    if(empty($id) && !empty($classname)) {
			        $id = mail_setting::get_id_from_classname($classname);
			    }
				$model_instance = static::get_model_instance($id);
				if(empty($id) && !empty($classname)) {
					$model_instance->set_classname($classname);
					$objects = list_mails_settings_ui::get_instance()->get_objects();
					foreach ($objects as $object) {
						if($object->get_classname() == $classname) {
							print $model_instance->set_properties_from_folder($object->get_folder_path());
						}
					}
				}
				print $model_instance->get_form();
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
}