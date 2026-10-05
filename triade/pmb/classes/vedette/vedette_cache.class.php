<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_cache.class.php,v 1.4 2018/12/04 10:26:44 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

final class vedette_cache{
	
	/**
	 * @var array() 
	 */
	private static $vedette_cache_arrayObject;
	
	/**
	 * @param object $vedette_object an storable cms object
	 * @return number the object's index
	 */
	private static function get_index($vedette_object){
		$index=0;
		
		if(get_class($vedette_object)=='vedette_element' || get_parent_class($vedette_object)=='vedette_element'){
			$index=$vedette_object->get_num_available_field().'_'.$vedette_object->get_id();
		}else{
			$index=$vedette_object->get_id();
		}
		return $index;
	}
	
	/**
	 * @param object $vedette_object an storable cms object
	 * @return bool true if exists in the array, false otherwise
	 */
	public static function get_at_vedette_cache($vedette_object){
		if(!isset(self::$vedette_cache_arrayObject[get_class($vedette_object)][self::get_index($vedette_object)])) {
			self::$vedette_cache_arrayObject[get_class($vedette_object)][self::get_index($vedette_object)] = null;
		}
		if(is_null(self::$vedette_cache_arrayObject[get_class($vedette_object)][self::get_index($vedette_object)])){
			return false;
		}else{
			return self::$vedette_cache_arrayObject[get_class($vedette_object)][self::get_index($vedette_object)];
		}
	}
	
	/**
	 * @param object $vedette_object an storable cms object
	 */
	public static function set_at_vedette_cache($vedette_object){
		self::$vedette_cache_arrayObject[get_class($vedette_object)][self::get_index($vedette_object)]=$vedette_object;
	}
	
	/*
	 * Private contructor
	 */
	private function __construct() {}
	
	/*
	 * Prevent cloning of instance
	 */
	private function __clone() {
		throw new Exception('Clone is not allowed !');
	}
	
	/*
	 * Set the instance to null
	 */
	private function __destruct() {
		self::$vedette_cache_arrayObject=null;
	}
}
