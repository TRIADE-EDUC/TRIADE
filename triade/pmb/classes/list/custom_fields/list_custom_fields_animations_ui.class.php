<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_custom_fields_animations_ui.class.php,v 1.3 2020/11/05 12:26:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_custom_fields_animations_ui extends list_custom_fields_ui {
		
	protected static $num_type;
	
	public static function set_num_type($num_type) {
		static::$num_type = $num_type;
	}
	
	protected function _get_query_base() {
		$query = parent::_get_query_base();
		$query .= " where num_type = ".static::$num_type;
		return $query;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return './admin.php?categ=animations&sub=priceTypesPerso&type_field=anim_price_type&numPriceType='.static::$num_type;
	}
}