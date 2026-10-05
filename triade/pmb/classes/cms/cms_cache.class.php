<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_cache.class.php,v 1.12.6.1 2024/07/26 09:13:37 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

final class cms_cache {

	/**
	 * @var array()
	 */
	private static $cms_cache_arrayObject;

	/**
	 * @param object $cms_object an storable cms object
	 * @return number the object's index
	 */
	private static function get_index($cms_object){
		$index=0;

		switch(get_class($cms_object)){
			case 'cms_articles':
				$index=$cms_object->num_section;
				break;
			case 'cms_editorial_parametres_perso':
				$index=$cms_object->get_num_type();
				break;
			case 'cms_editorial_publications_states':
				$index=0;
				break;
			case 'cms_logo':
				$index=$cms_object->get_type().'_'.$cms_object->get_id();
				break;
			default:
				$index=$cms_object->get_id();
				break;
		}
		return $index;
	}

	/**
	 * @param object $cms_object an storable cms object
	 * @return bool true if exists in the array, false otherwise
	 */
	public static function get_at_cms_cache($cms_object){
		if(!isset(self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)])) {
			self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)] = null;
		}
		if(is_null(self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)])){
			return false;
		}else{
			return self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)];
		}
	}

	/**
	 * @param object $cms_object an storable cms object
	 */
	public static function set_at_cms_cache($cms_object){
		self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)]=$cms_object;
	}

	/**
	 * Private contructor
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of instance
	 *
	 * @throws Exception
	 * @return void
	 */
	private function __clone() {
		throw new Exception('Clone is not allowed !');
	}

	/**
	 * Set the instance to null
	 */
	private function __destruct() {
		self::$cms_cache_arrayObject=null;
	}

	public static function clean_cache(){
		pmb_mysql_query("TRUNCATE TABLE cms_cache_cadres");
	}

	/**
	 * Clean the cache
	 *
	 * @return void
	 */
	public static function clean_cache_img() {
		global $base_path, $database;

		$cache_dirs = [
			$base_path.'/temp/cms_vign/'.$database,
			$base_path.'/opac_css/temp/cms_vign/'.$database,
			$base_path.'/opac_css/temp/cms_document/'.$database,
			$base_path.'/temp/animations_vign/'.$database,
			$base_path.'/opac_css/temp/animations_vign/'.$database
		];

		foreach($cache_dirs as $dir) {
			if (is_dir($dir)) {
				self::rmdir_files($dir);
			}
		}

		// Il faut également vider le cache des cadres
		static::clean_cache();
	}

	/**
	 * Delete all files in a directory
	 *
	 * @param string $dir
	 * @return void
	 */
	private static function rmdir_files($dir) {
		foreach(glob($dir . '/*') as $file) {
			if (is_dir($file)) {
				self::rmdir_files($file);
			} else {
				@unlink($file);
			}
		}
		@rmdir($dir);
	}

	public static function get_cache_formatted_last_date(){
		global $msg, $charset;

		$query = "SELECT cache_cadre_create_date FROM cms_cache_cadres order by cache_cadre_create_date limit 1";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			return htmlentities($msg['cms_cache_date'], ENT_QUOTES, $charset)." : ".formatdate(pmb_mysql_result($result, 0, 'cache_cadre_create_date'), 1);
		}
		return '';
	}
}
