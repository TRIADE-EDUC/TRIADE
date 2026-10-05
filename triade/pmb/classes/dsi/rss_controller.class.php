<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rss_controller.class.php,v 1.2 2020/11/05 09:39:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/rss_flux.class.php");

class rss_controller extends lists_controller {
	
	protected static $model_class_name = 'rss_flux';
	protected static $list_ui_class_name = 'list_rss_ui';
	
	public static function proceed($id=0) {
		global $msg;
		global $suite;
		global $database_window_title;
		
		switch($suite) {
			case 'acces':
				$model_instance = static::get_model_instance($id);
				print $model_instance->show_form();
				break;
			case 'add':
				$model_instance = static::get_model_instance($id);
				print $model_instance->show_form();
				break;
			case 'delete':
				$model_instance = static::get_model_instance($id);
				$model_instance->delete();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'update':
				$model_instance = static::get_model_instance($id);
				$model_instance->set_properties_from_form();
				$model_instance->update();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'search':
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				echo window_title($database_window_title.$msg['dsi_menu_flux']);
				parent::proceed($id);
				break;
		}
	}
}
