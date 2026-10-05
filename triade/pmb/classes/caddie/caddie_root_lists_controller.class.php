<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_root_lists_controller.class.php,v 1.9 2023/11/29 13:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class caddie_root_lists_controller extends lists_controller {
	
	protected static $id_caddie;
	
	protected static $object_type;
	
	public static function set_id_caddie($id_caddie) {
		static::$id_caddie = $id_caddie;
	}
	
	public static function set_object_type($object_type) {
		static::$object_type = $object_type;
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
	    global $show_list, $elt_flag_not_sended;
		
		$list_ui_class_name = static::$list_ui_class_name;
		$list_ui_class_name::set_id_caddie(static::$id_caddie);
		$list_ui_class_name::set_object_type(static::$object_type);
		if($show_list) {
			$list_ui_class_name::set_show_list(true);
		}
		switch (static::$object_type) {
			case 'BULL':
				return new $list_ui_class_name($filters, $pager, array('by' => 'bulletin_titre', 'asc_desc' => 'asc'));
			case 'EMPR':
			    if($elt_flag_not_sended) {
			        $filters['elt_flag'] = '2';
			        $filters['elt_no_flag'] = '0';
			    }
			    return new $list_ui_class_name($filters, $pager, $applied_sort);
			default:
				return new $list_ui_class_name($filters, $pager, $applied_sort);
		}
	}
	
	public static function proceed_ajax($object_type, $directory='caddie') {
		global $filters, $pager, $sort_by, $sort_asc_desc;
	
		$objects_type=substr($object_type,0,strpos($object_type, '_ui_')+3);
		$caddie_object_type=substr($object_type,strpos($object_type, '_ui_')+4);
		
		if(isset($objects_type) && $objects_type) {
			$class_name = 'list_'.$objects_type;
			if($directory) {
				static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
			} else {
				static::load_class('/list/'.$class_name.'.class.php');
			}
			$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
			$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager), true) : array());
			$class_name::set_id_caddie($filters['id_caddie']);
			$class_name::set_object_type($caddie_object_type);
			$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (!empty($sort_asc_desc) ? $sort_asc_desc : '')));
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_caption_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
		}
	}
	
	public static function proceed_manage_ajax($id=0, $object_type='', $directory='caddie_content') {
		global $sub, $action;
		global $filters, $pager, $sort_by, $sort_asc_desc;
	
		$id = intval($id);
		$objects_type=substr($object_type,0,strpos($object_type, '_ui_')+3);
		$caddie_object_type=substr($object_type,strpos($object_type, '_ui_')+4);
		if(isset($objects_type) && $objects_type) {
			$class_name = 'list_'.$objects_type;
			if($directory) {
				static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
			} else {
				static::load_class('/list/'.$class_name.'.class.php');
			}
			$class_name::set_id_caddie($filters['id_caddie']);
			$class_name::set_object_type($caddie_object_type);
			switch($sub) {
				case 'options':
					switch ($action) {
						case 'get_applied_group_selector':
							$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
							$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager), true) : array());
							$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (!empty($sort_asc_desc) ? $sort_asc_desc : '')));
							print encoding_normalize::utf8_normalize($instance_class_name->get_display_add_applied_group($id));
							break;
						default:
							parent::proceed_manage_ajax($id, $objects_type, $directory);
							break;
					}
					break;
				default:
					parent::proceed_manage_ajax($id, $objects_type, $directory);
					break;
			}
		}
	}
}