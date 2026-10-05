<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: configuration_nomenclature_controller.class.php,v 1.2 2021/01/28 08:54:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class configuration_nomenclature_controller extends configuration_controller {
	
	public static function proceed($id=0) {
		global $action;
		
		$id = intval($id);
		switch ($action) {
			case 'form':
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form();
				break;
			case 'up':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_class_name::order_up($id);
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'down':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_class_name::order_down($id);
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
	
	protected static function get_model_instance($id) {
		global $num_family, $num_formation;
		
		$model_instance = new static::$model_class_name($id);
		if(empty($id)) {
			if($num_family && method_exists($model_instance, 'set_family_num')) {
				$model_instance->set_family_num($num_family);
			}
			if($num_formation && method_exists($model_instance, 'set_formation_num')) {
				$model_instance->set_formation_num($num_formation);
			}
		}
		return $model_instance;
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		global $num_family, $num_formation;
		
		switch (static::$model_class_name) {
			case 'nomenclature_musicstand':
				return new static::$list_ui_class_name(array('num_family' => $num_family));
			case 'nomenclature_type':
				return new static::$list_ui_class_name(array('num_formation' => $num_formation));
			default:
				return new static::$list_ui_class_name();
		}
	}
	
	public static function get_url_base() {
		global $num_family, $num_formation;
		
		$url_base = parent::get_url_base();
		switch (static::$model_class_name) {
			case 'nomenclature_musicstand':
				return $url_base."&num_family=".$num_family;
			case 'nomenclature_type':
				return $url_base."&num_formation=".$num_formation;
			default:
				return $url_base;
		}
	}
	
}