<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_nomenclatures_types_ui.class.php,v 1.4 2023/05/05 13:38:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/nomenclature/nomenclature_formation.class.php");
require_once($class_path."/nomenclature/nomenclature_type.class.php");

class list_configuration_nomenclatures_types_ui extends list_configuration_nomenclatures_ui {
	
	protected static $object_type = 'formation_type';
	
	protected static $table_name = 'nomenclature_types';
	protected static $field_id = 'id_type';
	protected static $field_order = 'type_order';
	
	protected function get_title() {
		global $msg, $charset;
		$nomenclature_formation = new nomenclature_formation($this->filters['num_formation']);
		return "<h1>".str_replace('!!formation_name!!',$nomenclature_formation->get_name(), htmlentities($msg["admin_nomenclature_formation_type"], ENT_QUOTES, $charset))."</h1>";
	}
	
	protected function _get_query_base() {
		return 'SELECT * FROM nomenclature_types';
	}
	
	protected function get_object_instance($row) {
		return nomenclature_type::get_instance($row->id_type);
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'num_formation' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('order');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('num_formation', 'type_formation_num');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'admin_nomenclature_formation_type_form_name',
		);
	}
		
	protected function init_default_columns() {
		$this->add_column_dnd();
		parent::init_default_columns();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'order', 'name'
		);
	}
	
	public static function get_query_line_order($order) {
		global $num_formation;
		$num_formation = intval($num_formation);
		return "select ".static::$field_id." from ".static::$table_name." where ".static::$field_order."=".$order." and type_formation_num=".$num_formation." limit 1";
	}
	
	public static function get_query_max_order($id, $order) {
		global $num_formation;
		$num_formation = intval($num_formation);
		return "select max(".static::$field_order.") as ordre from ".static::$table_name." where ".static::$field_order."<".$order." and type_formation_num=".$num_formation;
	}
	
	public static function get_query_min_order($id, $order) {
		global $num_formation;
		$num_formation = intval($num_formation);
		return "select min(".static::$field_order.") as ordre from ".static::$table_name." where ".static::$field_order.">".$order." and type_formation_num=".$num_formation;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $num_formation;
		
		$num_formation = intval($num_formation);
		return $base_path.'/'.static::$module.'.php?categ=formation&sub='.static::$object_type.'&num_formation='.$num_formation;
	}
}