<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_cadres_controller.class.php,v 1.1.2.2 2025/01/31 15:46:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_cadres_controller extends lists_controller {
	
	protected static $model_class_name = 'cms_cadre';
	protected static $list_ui_class_name = 'list_cms_cadres_ui';
	
	public static function proceed($id=0) {
	    global $action;

		switch ($action) {
			
			default:
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
		}
	}
}
