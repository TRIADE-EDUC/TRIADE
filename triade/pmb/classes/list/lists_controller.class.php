<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lists_controller.class.php,v 1.9 2019-05-17 10:59:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_model.class.php");

class lists_controller {
	
	/**
	 * Nom de la classe modèle à dériver
	 * @var string
	 */
	protected static $model_class_name = '';
	
	/**
	 * Nom de la classe list_ui à dériver
	 * @var string
	 */
	protected static $list_ui_class_name = '';
	
	protected static function get_model_instance($id) {
		return new static::$model_class_name($id);
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		return new static::$list_ui_class_name($filters, $pager, $applied_sort);
	}
	
	public static function proceed($id=0) {
		global $msg;
		global $action;
		global $dest;
		
		switch ($action) {
			case 'edit':
				$id += 0;
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form();
				break;
			case 'save':
				$id += 0;
				$model_instance = static::get_model_instance($id);
				$model_instance->set_properties_from_form();
				$model_instance->save();
				break;
			case 'delete':
				$id += 0;
				$model_class_name = static::model_class_name;
				$model_class_name::delete($id);
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'list_delete':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_class_name::delete();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'dataset_edit':
				$id += 0;
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_dataset_form($id);
				break;
			case 'dataset_save':
				$id += 0;
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_instance = static::get_list_ui_instance();
				$list_model = new list_model($id);
				$list_model->set_objects_type($list_ui_instance->get_objects_type());
				$list_model->set_list_ui($list_ui_instance);
				$list_model->set_properties_from_form();
				$list_model->save();
				if(!$id) { //Création
					$list_ui_instance->add_dataset($list_model->get_id());
				}
				$list_ui_instance->apply_dataset($list_model->get_id());
				print $list_ui_instance->get_display_list();
				break;
			case 'dataset_apply':
				$id += 0;
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_instance = static::get_list_ui_instance();
				$list_ui_instance->apply_dataset($id);
				print $list_ui_instance->get_display_list();
				break;
			case 'dataset_delete':
				list_model::delete($id);
				break;
			default:
				$list_ui_instance = static::get_list_ui_instance();
				switch($dest) {
					case "TABLEAU":
						$list_ui_instance->get_display_spreadsheet_list();
						break;
					case "TABLEAUHTML":
						print $list_ui_instance->get_display_html_list();
						break;
					default:
						print $list_ui_instance->get_display_list();
						break;
				}
		}
	}
	
	public static function proceed_ajax($object_type, $directory='') {
		global $class_path;
		global $filters, $pager, $sort_by, $sort_asc_desc;
		
		if(isset($object_type) && $object_type) {
			$class_name = 'list_'.$object_type;
			if($directory) {
				static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
			} else {
				static::load_class('/list/'.$class_name.'.class.php');
			}
			$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
			$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager), true) : array());
			$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (!empty($sort_asc_desc) ? $sort_asc_desc : '')));
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
		}
	}
	
	public static function proceed_manage_ajax($id=0, $objects_type, $directory='') {
		global $sub, $action;
		global $class_path;
		global $filters, $pager, $sort_by, $sort_asc_desc;
	
		$id = intval($id);
		if(isset($objects_type) && $objects_type) {
			switch($sub) {
				case 'options':
					switch ($action) {
						case 'get_applied_group_selector':
							$class_name = 'list_'.$objects_type;
							if($directory) {
								static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
							} else {
								static::load_class('/list/'.$class_name.'.class.php');
							}
							$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
							$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager), true) : array());
							$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (!empty($sort_asc_desc) ? $sort_asc_desc : '')));
							print encoding_normalize::utf8_normalize($instance_class_name->get_display_add_applied_group($id));
							break;
					}
					break;
				default:
					switch($action) {
						case 'save':
							$list_model = new list_model($id);
							$list_model->set_objects_type($objects_type);
							$list_model->set_properties_from_form();
							$list_model->save();
							break;
						case 'edit':
								
							break;
						case 'delete':
							list_model::delete($id);
							break;
					}
					break;
			}
		}
	}
	
	protected static function load_class($file){
		global $base_path;
		global $class_path;
		global $include_path;
		global $javascript_path;
		global $styles_path;
		global $msg,$charset;
		global $current_module;
		 
		if(file_exists($class_path.$file)){
			require_once($class_path.$file);
		}else{
			return false;
		}
		return true;
	}
	
	public static function set_list_ui_class_name($list_ui_class_name) {
		static::$list_ui_class_name = $list_ui_class_name;
	}
	
	public static function set_model_class_name($model_class_name) {
		static::$model_class_name = $model_class_name;
	}
}