<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_quotas_ui.class.php,v 1.2 2022/11/03 15:29:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_quotas_ui extends list_configuration_ui {
	
	protected static $type_id;
	
	protected static $descriptor;
	
	protected static $quota_instance;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		global $categ, $sub;
		
		static::$module = 'admin';
		static::$categ = $categ;
		static::$sub = $sub;
		static::_init_quota_data();
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected static function _init_quota_data() {
		static::$quota_instance = quota::get_instance(static::get_type_id(), static::get_descriptor());
	}
	
	protected function get_button_add() {
		return '';
	}
	
	public static function get_type_id() {
		global $quota;
		
		if(empty(static::$type_id)) {
			// on valorise par rapport au contexte
			switch (static::$categ) {
				case 'quotas':
					static::$type_id = static::$sub;
					break;
				case 'opac':
					static::$type_id = 1;
					break;
				case 'finance':
					static::$type_id = $quota;
					break;
			}
		}
		return static::$type_id;
	}
	
	public static function set_type_id($type_id) {
		static::$type_id = $type_id;
	}
	
	public static function get_descriptor() {
		global $include_path, $lang;
		
		if(!isset(static::$descriptor)) {
			// on valorise par rapport au contexte
			switch (static::$categ) {
				case 'opac':
					static::$descriptor = $include_path."/quotas/own/".$lang."/opac_views.xml";
					break;
				case 'finance':
					static::$descriptor = $include_path."/quotas/own/".$lang."/finances.xml";
					break;
			}
		}
		return static::$descriptor;
	}
	
	public static function set_descriptor($descriptor) {
		static::$descriptor = $descriptor;
	}
}