<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visits_statistics_date_controller.class.php,v 1.1.2.3 2024/11/07 07:57:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class visits_statistics_date_controller extends visits_statistics_controller {
	
	protected static $model_class_name = 'visits_statistics';
	
	protected static $list_ui_class_name = 'list_visits_statistics_date_ui';
	
	public static function proceed($id=0) {
	    global $action;
	    global $date, $location, $type, $visits_number;
	    
	    $id = intval($id);
	    switch ($action) {
	        case 'delete':
	            if (SESSrights & ADMINISTRATION_AUTH) {
    	            $matches = [];
    	            if(pmb_preg_match("#(\d{4})[-/\.](\d{2})[-/\.](\d{2})#",$date, $matches)) {
    	                $location = intval($location);
    	                $visits_number = intval($visits_number);
    	                $model_instance = new static::$model_class_name($location, $date);
    	                $model_instance->remove_visits($type, $visits_number);
    	            }
	            }
	            $list_ui_instance = static::get_list_ui_instance();
	            print $list_ui_instance->get_display_list();
	            break;
	        default:
	            parent::proceed($id);
	            break;
	    }
	}
}