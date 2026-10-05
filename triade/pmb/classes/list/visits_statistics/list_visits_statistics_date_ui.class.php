<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_visits_statistics_date_ui.class.php,v 1.1.2.3.2.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/visit_statistics.class.php');

class list_visits_statistics_date_ui extends list_visits_statistics_ui {
	
	protected function _get_query_base() {
		$query = 'select DATE(visits_statistics_date) as date, visits_statistics_location as location, visits_statistics_type as type, count(*) as visits_number from visits_statistics';
		return $query;
	}
	
	protected function _get_query_order() {
	    return ' GROUP BY date, location, type '.parent::_get_query_order();
	}
	
	protected function init_default_columns() {
		$this->add_column('type');
		$this->add_column('location');
		$this->add_column('date');
		$this->add_column('visits_number');
		$this->add_column('actions');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('date', 'datatype', 'date');
	}
	
	protected function get_cell_content($object, $property) {
	    global $msg;
	    
	    $content = '';
	    switch($property) {
	        case 'actions':
	            //Voir le détails
	            $content .= $this->get_interface_button_small($msg['see'], ['location' => static::get_controller_url_base()."&visits_statistics_ui_date=".$object->date."&visits_statistics_ui_locations[]=".$object->location."&visits_statistics_ui_types[]=".$object->type]);
	            if (SESSrights & ADMINISTRATION_AUTH) {
	                $button_event = [
	                    'location' => static::get_controller_url_base()."&action=delete&date=".$object->date."&location=".$object->location."&type=".$object->type."&visits_number=".$object->visits_number,
	                    'confirm_msg' => $msg['confirm_suppr']
	                ];
	                $content .= $this->get_interface_button_small($msg['63'], $button_event);
	            }
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
}