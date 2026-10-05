<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classements_controller.class.php,v 1.5 2023/11/29 13:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/classements.class.php");

class classements_controller extends lists_controller {
	
	protected static $model_class_name = 'classement';
	protected static $list_ui_class_name = 'list_classements_ui';
	
	public static function proceed($id=0) {
		global $suite;
		global $type_classement;
		
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
				print $model_instance->delete();
				break;
			case 'update':
				if(!isset($type_classement)) $type_classement = '';
				$model_instance = static::get_model_instance($id);
				$model_instance->set_properties_from_form();
				print $model_instance->save();
				break;
			case 'up':
				$model_instance = static::get_model_instance($id);
				$model_instance->set_order('up');
				break;
			case 'down':
				$model_instance = static::get_model_instance($id);
				$model_instance->set_order('down');
				break;
		}
		
		$list_ui_class_name = static::$list_ui_class_name;
		$list_ui_class_name::set_type('BAN');
		$list_ui_instance = static::get_list_ui_instance();
		if($list_ui_instance->get_pager()['nb_results']) {
			print $list_ui_instance->get_display_list();
		}
		
		$list_ui_class_name::set_type('EQU');
		$list_ui_instance = static::get_list_ui_instance();
		if($list_ui_instance->get_pager()['nb_results']) {
			print $list_ui_instance->get_display_list();
		}
		
		//Aucune équation, on affiche le bouton "Ajouter"
		if(!$list_ui_instance->get_pager()['nb_results']) {
			print $list_ui_class_name::get_button_add_empty_lists();
		}
	}
	
	public static function proceed_ajax($object_type, $directory='') {
		global $filters, $pager, $sort_by, $sort_asc_desc;
		
		$type=substr($object_type,strpos($object_type, '_ui_')+4);
		$object_type=substr($object_type,0,strpos($object_type, '_ui_')+3);
		if(isset($object_type) && $object_type) {
			$class_name = 'list_'.$object_type;
			if($directory) {
				static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
			} else {
				static::load_class('/list/'.$class_name.'.class.php');
			}
			$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
			$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager), true) : array());
			$class_name::set_type($type);
			$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (!empty($sort_asc_desc) ? $sort_asc_desc : '')));
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_caption_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
		}
	}
}// end class
