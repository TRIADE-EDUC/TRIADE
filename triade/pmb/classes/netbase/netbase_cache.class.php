<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase_cache.class.php,v 1.1 2019-04-29 11:04:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class netbase_cache {
	
	public function __construct() {
		
	}
	
	protected static function is_temporary_file($file) {
		if(substr($file, 0, 3) == "XML" && substr($file, strlen($file)-4, 4) == ".tmp") {
			return true;
		}
		if(substr($file, 0, 4) == "h2o_") {
			return true;
		}
		return false;
	}
	
	public static function clean_files($folder_path) {
		if(is_dir($folder_path)) {
			$dh = opendir($folder_path);
			while(($file = readdir($dh)) !== false){
				if(!is_dir($folder_path.'/'.$file) && $file != "." && $file != ".." && $file != "CVS"){
					if(static::is_temporary_file($file)) {
						unlink($folder_path.'/'.$file);
					}
				}
			}
			return true;
		}
		return false;
	}
} // fin de déclaration de la classe netbase
