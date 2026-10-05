<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: configuration_harvest_controller.class.php,v 1.3 2024/01/03 09:46:40 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class configuration_harvest_controller extends configuration_controller
{
	
    public static function proceed($id = 0)
    {
		global $action;
		
		$id = intval($id);
		switch ($action) {
			case 'del':
				if(static::$model_class_name == 'harvest') {
					$model_instance = static::get_model_instance($id);
					$deleted = $model_instance->delete();
					if($deleted) {
						static::redirect_display_list();
					} else {
						pmb_error::get_instance(static::$model_class_name)->display(1, static::get_url_base());
					}
				} else {
					parent::proceed($id);
				}
				break;
			default:
				parent::proceed($id);
				break;
		}
	}	
}