<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_connecteurs_categout_sets_ui.class.php,v 1.4 2021/04/19 07:10:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/connecteurs_out_sets.class.php");

class list_configuration_connecteurs_categout_sets_ui extends list_configuration_connecteurs_ui {
	
	protected function _get_query_base() {
		return 'SELECT connectors_out_setcateg_id FROM connectors_out_setcategs';
	}
	
	protected function get_object_instance($row) {
		return new connector_out_setcateg($row->connectors_out_setcateg_id);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'admin_connecteurs_setcateg_name',
				'sets' => 'admin_connecteurs_setcateg_setcount',
		);
	}
	
	protected function _get_object_property_sets($object) {
		return count($object->sets);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id;
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["admin_connecteurs_sets_nosetcateg"], ENT_QUOTES, $charset);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_connecteurs_setcateg_add'];
	}
}