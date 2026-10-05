<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: readers_controller.class.php,v 1.6 2023/11/29 13:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/relance.class.php");
require_once($class_path."/emprunteur.class.php");

class readers_controller extends lists_controller {
	
	protected static $model_class_name = 'emprunteur';
	
	protected static $list_ui_class_name = 'list_readers_ui';
	
	protected static $id_empr;

	public static function proceed_ajax($object_type, $directory='') {
	    global $empr_sort_rows, $empr_show_rows, $empr_filter_rows;
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
	        if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
	        	$list_ui_class_name = static::$list_ui_class_name;
	        	switch ($list_ui_class_name) {
	        	    case 'list_readers_relances_ui':
	        	    	$filter = relance::get_instance_filter_list();
	        	    	break;
	        	    case 'list_readers_circ_ui':
	        	    	$filter = emprunteur::get_instance_filter_list();
	        	    	break;
	        	}
	            $list_ui_class_name::set_used_filter_list_mode(true);
	            $list_ui_class_name::set_filter_list($filter, $filters);
	        }
	        $instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (!empty($sort_asc_desc) ? $sort_asc_desc : '')));
	        $instance_class_name->set_ancre($ancre);
	        print encoding_normalize::utf8_normalize($instance_class_name->get_display_caption_list());
	        print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
	        print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
	        
	    }
	}
	
	public static function set_id_empr($id_empr) {
		static::$id_empr = intval($id_empr);
	}
	
}