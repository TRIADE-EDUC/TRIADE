<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_nomenclatures_musicstands_ui.class.php,v 1.4 2022/10/06 11:57:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/nomenclature/nomenclature_family.class.php");
require_once($class_path."/nomenclature/nomenclature_musicstand.class.php");

class list_configuration_nomenclatures_musicstands_ui extends list_configuration_nomenclatures_ui {
	
	protected static $object_type = 'family_musicstand';
	
	protected static $table_name = 'nomenclature_musicstands';
	protected static $field_id = 'id_musicstand';
	protected static $field_order = 'musicstand_order';
	
	protected function get_title() {
		global $msg, $charset;
		$nomenclature_family = new nomenclature_family($this->filters['num_family']);
		return "<h1>".str_replace('!!famille_name!!',$nomenclature_family->get_name(), htmlentities($msg["admin_nomenclature_family_musicstand"], ENT_QUOTES, $charset))."</h1>";
	}
	
	protected function _get_query_base() {
		return 'SELECT * FROM nomenclature_musicstands';
	}
	
	protected function get_object_instance($row) {
		return new nomenclature_musicstand($row->id_musicstand);
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'num_family' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('order');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('num_family', 'musicstand_famille_num');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'admin_nomenclature_family_musicstand_form_name',
				'instruments' => 'admin_nomenclature_family_musicstand_form_instruments',
				'divisable' => 'admin_nomenclature_family_musicstand_form_division',
				'used_by_workshops' => 'admin_nomenclature_family_musicstand_form_workshop'
		);
	}
		
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('divisable', 'align', 'center');
		$this->set_setting_column('divisable', 'datatype', 'boolean');
		$this->set_setting_column('used_by_workshops', 'align', 'center');
		$this->set_setting_column('used_by_workshops', 'datatype', 'boolean');
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'order', 'name', 'instruments', 'divisable', 'used_by_workshops'
		);
	}
	
	protected function init_default_columns() {
		$this->add_column_dnd();
		parent::init_default_columns();
	}
	
	protected function _get_object_property_instruments($object) {
		return $object->get_instruments_display();
	}
	
	public static function get_query_line_order($order) {
		global $num_family;
		$num_family = intval($num_family);
		return "select ".static::$field_id." from ".static::$table_name." where ".static::$field_order."=".$order." and musicstand_famille_num=".$num_family." limit 1";
	}
	
	public static function get_query_max_order($id, $order) {
		global $num_family;
		$num_family = intval($num_family);
		return "select max(".static::$field_order.") as ordre from ".static::$table_name." where ".static::$field_order."<".$order." and musicstand_famille_num=".$num_family;
	}
	
	public static function get_query_min_order($id, $order) {
		global $num_family;
		$num_family = intval($num_family);
		return "select min(".static::$field_order.") as ordre from ".static::$table_name." where ".static::$field_order.">".$order." and musicstand_famille_num=".$num_family;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $num_family;
		
		$num_family = intval($num_family);
		return $base_path.'/'.static::$module.'.php?categ=family&sub='.static::$object_type.'&num_family='.$num_family;
	}
}