<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lists_ui_controller.class.php,v 1.2 2022/10/06 11:48:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/list_model.class.php");

class lists_ui_controller extends lists_controller {
	
	protected static $model_class_name = 'list_model';
	protected static $list_ui_class_name = 'list_lists_ui';
	
	public static function get_list_ui_class_name() {
		global $objects_type;
		
		if(!empty($objects_type)) {
			if(strpos($objects_type, 'authorities_caddie_content_ui_') === 0) {
				$object_type = str_replace('authorities_caddie_content_ui_', '', $objects_type);
				$list_ui_class_name = 'list_authorities_caddie_content_ui';
				$list_ui_class_name::set_object_type($object_type);
			} elseif(strpos($objects_type, 'empr_caddie_content_ui_') === 0) {
				$object_type = str_replace('empr_caddie_content_ui_', '', $objects_type);
				$list_ui_class_name = 'list_empr_caddie_content_ui';
				$list_ui_class_name::set_object_type($object_type);
			} elseif(strpos($objects_type, 'caddie_content_ui_') === 0) {
				$object_type = str_replace('caddie_content_ui_', '', $objects_type);
				$list_ui_class_name = 'list_caddie_content_ui';
				$list_ui_class_name::set_object_type($object_type);
			} else {
				$list_ui_class_name = 'list_'.$objects_type;
			}
			static::$list_ui_class_name = $list_ui_class_name;
		}
		return static::$list_ui_class_name;
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		global $empr_sort_rows, $empr_show_rows, $empr_filter_rows;
		
		$list_ui_class_name = static::get_list_ui_class_name();
		switch ($list_ui_class_name) {
			case 'list_readers_circ_ui':
			case 'list_readers_relances_ui':
				if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
					$filter = emprunteur::get_instance_filter_list();
					$list_ui_class_name::set_used_filter_list_mode(true);
					$list_ui_class_name::set_filter_list($filter);
				}
				return new static::$list_ui_class_name($filters, $pager, $applied_sort);
			default :
				return new static::$list_ui_class_name($filters, $pager, $applied_sort);
				
		}
	}
	
	public static function proceed($id=0) {
		global $action;
		global $objects_type;
		
		$id = intval($id);
		switch($action){
			case 'edit':
				if(isset($objects_type) && $objects_type) {
					$list_ui_instance = static::get_list_ui_instance();
					print "<h2>".$list_ui_instance->get_dataset_title()."</h2>";
					print $list_ui_instance->get_default_dataset_form($id);
				}
				break;
			case 'save':
				if(isset($objects_type) && $objects_type) {
					$list_ui_instance = static::get_list_ui_instance();
					$model_instance = static::get_model_instance($id);
					$model_instance->set_num_user(0);
					$model_instance->set_objects_type($list_ui_instance->get_objects_type());
					$model_instance->set_list_ui($list_ui_instance);
					$model_instance->set_properties_from_form();
					$has_doublon = false;
					if(method_exists($model_instance, 'get_query_if_exists')) {
						$query = $model_instance->get_query_if_exists();
						$result = pmb_mysql_query($query);
						$has_doublon = pmb_mysql_result($result, 0, 0);
					}
					if(!$has_doublon) {
						$model_instance->save();
					}
				}
				static::redirect_display_list();
				break;
			case 'delete':
				$model_class_name = static::$model_class_name;
				$model_class_name::delete_common_list($id, $objects_type);
				static::redirect_display_list();
				break;
			case 'list_delete':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_class_name::delete();
				static::redirect_display_list();
				break;
			default:
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
		}
	}
}
