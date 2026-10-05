<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authentication_controller.class.php,v 1.3 2023/06/28 14:40:56 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/authentication/authentication.class.php");

class authentication_controller extends lists_controller {
	
	protected static $model_class_name = 'authentication';

	public static function proceed($id=0) {
		global $action;
		
		$id = intval($id);
		switch ($action) {
			default: 
			case 'edit':
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form();
				break;
			case 'save':
				$model_instance = static::get_model_instance($id);
				$model_instance->set_properties_from_form();
				$model_instance->save();

				print $model_instance->get_form();
				break;
			case 'validate_mfa':
				$model_instance = static::get_model_instance($id);
				$model_instance->validate_mfa();

				print $model_instance->get_form();
				break;
			case 'save_mfa':
				$model_instance = static::get_model_instance($id);
				$model_instance->save_mfa();

				print $model_instance->get_form();
				break;
			case 'reset_mfa':
				$model_instance = static::get_model_instance($id);
				$model_instance->reset_mfa();

				print $model_instance->get_form();
				break;
		}
	}
}