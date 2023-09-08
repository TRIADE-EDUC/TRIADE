<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_custom_fields_custom_ui.class.php,v 1.2 2018-04-25 09:12:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/custom_fields/list_custom_fields_ui.class.php");

class list_custom_fields_custom_ui extends list_custom_fields_ui {
	
	protected static $custom_prefixe;
	
	protected static $num_type;
	
	public static function set_custom_prefixe($custom_prefixe) {
		static::$custom_prefixe = $custom_prefixe;
	}
	
	public static function set_num_type($num_type) {
		static::$num_type = $num_type;
	}
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = parent::_get_query_base();
		$query .= " where custom_prefixe = '".static::$custom_prefixe."' and num_type = ".static::$num_type;
		return $query;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $categ, $sub, $auth_action, $id_authperso;
		
		return $base_path.'/admin.php?categ='.$categ.'&sub='.$sub.'&auth_action='.$auth_action.'&id_authperso='.$id_authperso;
	}
}