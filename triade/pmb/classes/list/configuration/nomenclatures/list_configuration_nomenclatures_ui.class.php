<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_nomenclatures_ui.class.php,v 1.2 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_nomenclatures_ui extends list_configuration_ui {
		
	protected static $object_type;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		static::$module = 'admin';
		static::$categ = 'nomenclatures';
		static::$sub = str_replace(array('list_configuration_nomenclatures_', '_ui'), '', static::class);
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		return 'SELECT * FROM nomenclature_'.static::$sub;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=form&id='.$object->get_id();
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
		);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_nomenclature_'.static::$object_type.'_add'];
	}
	
	protected function add_column_dnd() {
		$this->columns[] = array(
				'property' => 'order',
				'label' => '',
				'html' => "<input class='bouton_small' type='button' onclick=\"document.location='".static::get_controller_url_base()."&action=up&id=!!id!!'\" value='-'>
						<input class='bouton_small' type='button' onclick=\"document.location='".static::get_controller_url_base()."&action=down&id=!!id!!'\" value='+'>",
				'exportable' => false
		);
	}
	
	protected function get_display_content_object_list($object, $indice) {
		return list_ui::get_display_content_object_list($object, $indice);
	}
	
	public static function get_query_line_order($order) {
		return "select ".static::$field_id." from ".static::$table_name." where ".static::$field_order."=$order limit 1";
	}
	
	public static function get_query_max_order($id, $order) {
		return "select max(".static::$field_order.") as ordre from ".static::$table_name." where ".static::$field_order."<$order";
	}
	
	public static function order_up($id){
		
		$query="select ".static::$field_order." from ".static::$table_name." where ".static::$field_id."=$id";
		$result=pmb_mysql_query($query);
		$order=pmb_mysql_result($result,0,0);
		$query=static::get_query_max_order($id, $order);
		$result=pmb_mysql_query($query);
		$order_max=@pmb_mysql_result($result,0,0);
		if ($order_max != '') {
			$query=static::get_query_line_order($order_max);
			$result=pmb_mysql_query($query);
			$id_max=pmb_mysql_result($result,0,0);
			$query="update ".static::$table_name." set ".static::$field_order."='".$order_max."' where ".static::$field_id."=$id";
			pmb_mysql_query($query);
			$query="update ".static::$table_name." set ".static::$field_order."='".$order."' where ".static::$field_id."=".$id_max;
			pmb_mysql_query($query);
		}
	}
	
	public static function get_query_min_order($id, $order) {
		return "select min(".static::$field_order.") as ordre from ".static::$table_name." where ".static::$field_order.">$order";
	}
	
	public static function order_down($id){
		$query="select ".static::$field_order." from ".static::$table_name." where ".static::$field_id."=$id";
		$result=pmb_mysql_query($query);
		$order=pmb_mysql_result($result,0,0);
		$query=static::get_query_min_order($id, $order);
		$result=pmb_mysql_query($query);
		$order_min=@pmb_mysql_result($result,0,0);
		if ($order_min) {
			$query=static::get_query_line_order($order_min);
			$result=pmb_mysql_query($query);
			$id_min=pmb_mysql_result($result,0,0);
			$query="update ".static::$table_name." set ".static::$field_order."='".$order_min."' where ".static::$field_id."=$id";
			pmb_mysql_query($query);
			$query="update ".static::$table_name." set ".static::$field_order."='".$order."' where ".static::$field_id."=".$id_min;
			pmb_mysql_query($query);
		}
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/'.static::$module.'.php?categ='.static::$object_type.'&sub='.static::$object_type;
	}
}