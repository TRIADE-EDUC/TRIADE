<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visits_statistics_controller.class.php,v 1.1.10.1 2024/10/16 13:35:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class visits_statistics_controller extends lists_controller {
	
	protected static $model_class_name = '';
	
	protected static $list_ui_class_name = 'list_visits_statistics_ui';
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
	    global $visits_statistics_ui_date, $visits_statistics_ui_date_start, $visits_statistics_ui_date_end;

	    $filters = [];
	    if (!empty($visits_statistics_ui_date)) {
    	    if(empty($visits_statistics_ui_date_start)) {
    	        $filters['date_start'] = $visits_statistics_ui_date;
    	    }
    	    if(empty($visits_statistics_ui_date_end)) {
    	        $filters['date_end'] = $visits_statistics_ui_date;
    	    }
	    }
	    
	    return new static::$list_ui_class_name($filters, $pager, $applied_sort);
	}
}