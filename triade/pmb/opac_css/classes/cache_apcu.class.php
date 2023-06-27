<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cache_apcu.class.php,v 1.1 2017-06-15 12:25:23 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cache_apcu extends cache_factory {

	public function setInCache($key, $value) {
		global $CACHE_MAXTIME;
		
		return apcu_store($key, $value, $CACHE_MAXTIME);
	}

	public function getFromCache($key) {
		return apcu_fetch($key);
	}

}