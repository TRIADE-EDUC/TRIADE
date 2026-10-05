<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: readers_recouvr_controller.class.php,v 1.4 2023/11/29 13:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/readers/readers_controller.class.php");

class readers_recouvr_controller extends readers_controller {
	
	protected static $list_ui_class_name = 'list_readers_recouvr_ui';
	
	public static function proceed_ajax($object_type, $directory='') {
		global $filters, $pager, $sort_by, $sort_asc_desc, $ancre;
		
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
			$instance_class_name->set_ancre($ancre);
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_caption_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
			
		}
	}
}