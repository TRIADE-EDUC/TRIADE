<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cache_apcu.class.php,v 1.3 2023/08/03 15:34:09 jparis Exp $

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

	public function clearCache() {
	    return apcu_clear_cache();
	}
}