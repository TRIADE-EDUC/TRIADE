<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_connecteurs_out_sets_ui.class.php,v 1.7 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/connecteurs_out_sets.class.php");

class list_configuration_connecteurs_out_sets_ui extends list_configuration_connecteurs_ui {
	
	protected function _get_query_base() {
		return 'SELECT connector_out_set_id, connector_out_set_type FROM connectors_out_sets';
	}
	
	protected function new_connector_out_set_typed($id, $type=0) {
		$id = intval($id);
		if (!$type) {
			$sql = "SELECT connector_out_set_type FROM connectors_out_sets WHERE connector_out_set_id = ".$id;
			$type = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		}
		if (!$type)
			$type=1;
		return new $this->connector_out_set_types_classes[$type]($id);
	}
	
	protected function get_object_instance($row) {
		
		return $this->new_connector_out_set_typed($row->connector_out_set_id, $row->connector_out_set_type);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('type');
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'type');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'caption' => 'admin_connecteurs_sets_setcaption',
				'type' => 'admin_connecteurs_sets_settype',
				'additionalinfo' => 'admin_connecteurs_sets_setadditionalinfo',
				'latestcacheupdate' => 'admin_connecteurs_setcateg_latestcacheupdate',
		);
	}
	
	protected function init_default_columns() {
		parent::init_default_columns();
		$this->add_column_manualupdate();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'manualupdate',
		);
	}
	
	protected function add_column_manualupdate() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['admin_connecteurs_setcateg_updatemanually'],
				'link' => static::get_controller_url_base().'&action=manual_update&id=!!id!!'
		);
		$this->add_column_simple_action('manualupdate', $msg['admin_connecteurs_setcateg_manualupdate'], $html_properties);
	}
	
	protected function get_display_content_object_list($object, $indice) {
		$this->is_editable_object_list = false;
		return parent::get_display_content_object_list($object, $indice);
	}
	
	protected function _get_object_property_type($object) {
		global $msg;
		return $msg[$this->connector_out_set_types_msgs[$object->type]];
	}
	
	protected function _get_object_property_additionalinfo($object) {
		return $object->get_third_column_info();
	}
	
	protected function _get_object_property_latestcacheupdate($object) {
		global $msg;
		return strtotime($object->cache->last_updated_date) ? formatdate($object->cache->last_updated_date, 1) : $msg["admin_connecteurs_setcateg_latestcacheupdate_never"];
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
		);
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["admin_connecteurs_sets_nosets"], ENT_QUOTES, $charset);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_connecteurs_set_add'];
	}
}