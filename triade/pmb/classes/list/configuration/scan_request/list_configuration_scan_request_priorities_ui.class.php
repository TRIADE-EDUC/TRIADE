<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_scan_request_priorities_ui.class.php,v 1.1 2021/01/20 07:27:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/configuration/scan_request/list_configuration_scan_request_ui.class.php");

class list_configuration_scan_request_priorities_ui extends list_configuration_scan_request_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM scan_request_priorities';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('scan_request_priority_weight');
	    $this->add_applied_sort('scan_request_priority_label');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'scan_request_priority_label' => 'scan_request_priorities_label',
				'scan_request_priority_weight' => 'scan_request_priority_weight',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id_scan_request_priority;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['925'];
	}
}