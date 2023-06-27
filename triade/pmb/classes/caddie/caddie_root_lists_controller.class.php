<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_root_lists_controller.class.php,v 1.1 2019-05-17 10:59:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/lists_controller.class.php");

class caddie_root_lists_controller extends lists_controller {
	
	public static function proceed_ajax($full_object_type, $directory='caddie') {
		global $class_path;
		global $filters, $pager, $sort_by, $sort_asc_desc;
	
		$object_type=substr($full_object_type,0,strpos($full_object_type, '_ui_')+3);
		$object_type_caddie=substr($full_object_type,strpos($full_object_type, '_ui_')+4);
		
		if(isset($object_type) && $object_type) {
			$class_name = 'list_'.$object_type;
			if($directory) {
				static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
			} else {
				static::load_class('/list/'.$class_name.'.class.php');
			}
			$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
			$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager), true) : array());
			$class_name::set_id_caddie($filters['id_caddie']);
			$class_name::set_object_type($object_type_caddie);
			$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (!empty($sort_asc_desc) ? $sort_asc_desc : '')));
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
		}
	}
	
	public static function proceed_manage_ajax($id=0, $full_object_type, $directory='caddie') {
		global $sub, $action;
		global $class_path;
		global $filters, $pager, $sort_by, $sort_asc_desc;
	
		$id = intval($id);
		$objects_type=substr($full_object_type,0,strpos($full_object_type, '_ui_')+3);
		$object_type=substr($full_object_type,strpos($full_object_type, '_ui_')+4);
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
							$class_name::set_id_caddie($filters['id_caddie']);
							$class_name::set_object_type($object_type);
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
}