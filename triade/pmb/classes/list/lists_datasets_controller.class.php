<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lists_datasets_controller.class.php,v 1.2 2024/01/26 09:14:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class lists_datasets_controller extends lists_controller {
	
	protected static $model_class_name = 'list_model';
	protected static $list_ui_class_name = 'list_lists_datasets_ui';
	
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
		global $objects_type;
		
		$list_ui_class_name = static::get_list_ui_class_name();
		$list_ui_instance = new $list_ui_class_name($filters, $pager, $applied_sort);
		if(!empty($objects_type)) {
			$settings = $list_ui_instance->get_settings();
			$settings['display']['search_form']['unfolded_filters'] = true;
			$settings['display']['search_form']['add_filters'] = true;
			$settings['display']['search_form']['unfolded_options'] = true;
			$settings['display']['search_form']['datasets'] = true;
			$settings['display']['search_form']['operators_filters'] = true;
			$list_ui_instance->set_settings($settings);
		}
		return $list_ui_instance;
	}
	
	public static function proceed($id=0) {
		global $action;
		global $objects_type;
		
		$id = intval($id);
		switch($action){
			case 'edit':
				$model_instance = static::get_model_instance($id);
				if(empty($id) && !empty($objects_type)) {
					$model_instance->set_objects_type($objects_type);
				} elseif(empty($objects_type)) {
					$objects_type = $model_instance->get_objects_type();
				}
				$list_ui_instance = static::get_list_ui_instance();
				print "<h2>".$list_ui_instance->get_dataset_title()."</h2>";
				$model_instance->set_list_ui($list_ui_instance);
				print $model_instance->get_form();
				break;
			case 'save':
			    $list_ui_instance = static::get_list_ui_instance();
			    $model_instance = static::get_model_instance($id);
			    $model_instance->set_objects_type($list_ui_instance->get_objects_type());
			    $model_instance->set_list_ui($list_ui_instance);
			    $model_instance->set_properties_from_form();
			    $model_instance->save();
// 			    if(!$id) { //Création
// 			        $list_ui_instance->add_dataset($model_instance->get_id());
// 			    }
			    $list_ui_instance = static::get_list_ui_instance();
			    print $list_ui_instance->get_display_list();
			    break;
			case 'play':
			    $model_instance = static::get_model_instance($id);
			    print "<h2>".$model_instance->get_label()."</h2>";
			    $list_ui_class_name = 'list_'.$model_instance->get_objects_type();
				static::$list_ui_class_name = $list_ui_class_name;
				$list_ui_instance = static::get_list_ui_instance();
				$list_ui_instance->apply_dataset($id);
				print $list_ui_instance->get_display_list();
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
}
