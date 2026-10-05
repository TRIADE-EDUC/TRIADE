<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase_cache.class.php,v 1.5.2.1 2024/09/05 13:05:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class netbase_cache {

	public function __construct() {

	}

	protected static function is_temporary_file($file) {
		if (substr($file, 0, 3) == "XML" && substr($file, strlen($file)-4, 4) == ".tmp") {
			return true;
		}
		if (substr($file, 0, 4) == "h2o_") {
			return true;
		}
		if (substr($file, 0, 11) == "ontologies_") {
		    return true;
		}
		if (substr($file, 0, 14) == "search_fields_") {
		    return true;
		}
		if (substr($file, 0, 9) == "opac_lang") {
		    return true;
		}
		if ($file === "artecampus.json") {
			// Permet de supprimer le fichier du connecteur artecampus
		    return true;
		}
		return false;
	}

	public static function clean_files($folder_path) {
		if (is_dir($folder_path)) {
			$dh = opendir($folder_path);
			if ($dh) {
				while (($file = readdir($dh)) !== false) {
					if (
						!is_dir($folder_path.'/'.$file) &&
						!in_array($file, array('.', '..', 'CVS')) &&
						static::is_temporary_file($file)
					) {
						unlink($folder_path.'/'.$file);
					}
				}
				closedir($dh);
				return true;
			}
		}
		return false;
	}

	public static function clean_apcu() {
		//Vidons également le cache APCU s'il est activé
		$cache_php=cache_factory::getCache();
		if(is_object($cache_php) && get_class($cache_php) == 'cache_apcu') {
			return $cache_php->clearCache();
		}
		return false;
	}

	public static function clean_autoload_files() {
		// Suppression des fichiers d'autoload back office
		@unlink(__DIR__ . "/../../temp/classLoader_paths.php");
		@unlink(__DIR__ . "/../../temp/classLoader_duplicates.php");
		@unlink(__DIR__ . "/../../temp/classLoader.lock");

		// Suppression des fichiers d'autoload front office
		@unlink(__DIR__ . "/../../opac_css/temp/classLoader_paths.php");
		@unlink(__DIR__ . "/../../opac_css/temp/classLoader_duplicates.php");
		@unlink(__DIR__ . "/../../opac_css/temp/classLoader.lock");
		return true;
	}
} // fin de déclaration de la classe netbase
