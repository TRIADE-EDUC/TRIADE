<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: configuration_tpl_controller.class.php,v 1.1 2021/02/01 08:45:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class configuration_tpl_controller extends configuration_controller {
	
	public static function proceed($id=0) {
		global $action;
		
		$id = intval($id);
		switch ($action) {
			case "add_field" :
			case "del_field" :
				if ($id) {
					$model_instance = static::get_model_instance($id);
					if(pmb_error::get_instance(static::$model_class_name)->has_error()) {
						pmb_error::get_instance(static::$model_class_name)->display(1, static::get_url_base());
					} else {
						$model_instance->set_properties_from_form();
						print $model_instance->get_form();
					}
				} else {
					static::redirect_display_list();
				}
				break;
			case "eval":
				$model_instance = static::get_model_instance($id);
				print $model_instance->show_eval();
				break;
			case 'import':
				$model_instance = static::get_model_instance($id);
				print $model_instance->show_import_form();
				break;
			case 'import_suite':
				$model_instance = static::get_model_instance($id);
				print $model_instance->do_import();
				break;
			default :
				parent::proceed($id);
				break;
		}
		
	}	
}