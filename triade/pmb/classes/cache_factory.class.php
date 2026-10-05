<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cache_factory.class.php,v 1.4 2022/10/24 07:55:50 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/cache_apcu.class.php");

class cache_factory {

	protected static $cacheInstance;

	public function setInCache($key, $value) {
		return false;
	}

	public function getFromCache($key) {
		return false;
	}
	
	public function deleteFromCache($key) {
	    return false;
	}
	

	/**
	 * 
	 * @return cache_factory
	 */
	public static function getCache() {
		global $CACHE_ENGINE;
		if(!isset(self::$cacheInstance)){
			if(($CACHE_ENGINE == 'apcu') && extension_loaded('apcu') && ini_get('apc.enabled')){
				$class_name="cache_apcu";
				self::$cacheInstance = new $class_name();
			}else{
				self::$cacheInstance = false;
			}
		}
		return self::$cacheInstance;
	}
}