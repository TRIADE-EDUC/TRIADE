<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cache_apcu.class.php,v 1.7 2023/08/03 15:34:09 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cache_apcu extends cache_factory {

	public function setInCache($key, $value, $cache_time = 0) {
		global $CACHE_MAXTIME;
		
		$cache_time = intval($cache_time);
	    if(!$cache_time) {
	        $cache_time = $CACHE_MAXTIME;
	    }
		
	    return apcu_store($key, $value, $cache_time);
	}

	public function getFromCache($key) {
		return apcu_fetch($key);
	}
	
	public function deleteFromCache($key) {
	    return apcu_delete($key);

	}

	public function clearCache() {
	    return apcu_clear_cache();
	}
	
	public function deleteCache() {
		global $KEY_CACHE_FILE_XML;
		if(class_exists('APCuIterator', false) && ('cli' !== \PHP_SAPI || filter_var(ini_get('apc.enable_cli'), \FILTER_VALIDATE_BOOLEAN))) {
			return apcu_delete(new APCUIterator('#^'.$KEY_CACHE_FILE_XML.'#'));
		} else {
			return false;
		}
	}
}