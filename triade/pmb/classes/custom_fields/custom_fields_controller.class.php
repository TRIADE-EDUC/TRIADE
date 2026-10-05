<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_controller.class.php,v 1.2 2023/11/29 13:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/parametres_perso.class.php");

class custom_fields_controller extends lists_controller {
	
	protected static $model_class_name = 'parametres_perso';
	
	protected static $list_ui_class_name = 'list_custom_fields_ui';
	
	public static function proceed_ajax($object_type, $directory='custom_fields') {
		global $filters, $pager, $sort_by, $sort_asc_desc;
		global $prefix, $id_authperso, $type_id, $option_visibilite;
		
		if(!empty($object_type)) {
			$class_name = 'list_'.$object_type;
			if($directory) {
				static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
			} else {
				static::load_class('/list/'.$class_name.'.class.php');
			}
			$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
			$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager), true) : array());
			if ($class_name == 'list_custom_fields_custom_ui') {
				$class_name::set_custom_prefixe($prefix);
				$class_name::set_num_type($id_authperso);
			} elseif($class_name == 'list_custom_fields_cms_ui') {
				$class_name::set_num_type($type_id);
			}
			$class_name::set_prefix($prefix);
			$class_name::set_option_visibilite(encoding_normalize::json_decode(urldecode(stripslashes($option_visibilite)), true));
			$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (isset($sort_asc_desc) ? $sort_asc_desc : '')));
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_caption_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
		}
	}
	
}