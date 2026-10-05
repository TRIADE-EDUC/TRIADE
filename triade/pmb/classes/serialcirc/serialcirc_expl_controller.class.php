<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_expl_controller.class.php,v 1.1 2023/01/09 14:44:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class serialcirc_expl_controller extends lists_controller {
	
	protected static $model_class_name = '';
	
	protected static $list_ui_class_name = 'list_serialcirc_expl_ui';
	
	public static function proceed($id=0) {
		global $action;
		
		switch ($action) {
			case 'list_repair_diffusion':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_class_name::repair_diffusion();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				parent::proceed($id);
		}
	}
}