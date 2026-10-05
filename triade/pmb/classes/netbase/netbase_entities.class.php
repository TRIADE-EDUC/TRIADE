<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase_entities.class.php,v 1.4 2024/04/12 09:46:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path.'/entities.class.php';
require_once $class_path.'/parametres_perso.class.php';

class netbase_entities {
	
	protected static $custom_fields_date_flot = array();
	
	public function __construct() {
		
	}
	
	public static function get_custom_fields_date_flot() {
		if(!isset(static::$custom_fields_date_flot)) {
			$prefixes = entities::get_prefixes();
			foreach ($prefixes as $prefix) {
				$p_perso = new parametres_perso($prefix);
				$fields = $p_perso->get_t_fields();
				foreach ($fields as $id_field => $field) {
					if ('date_flot' == $field['TYPE']) {
						if (!isset(static::$custom_fields_date_flot[$prefix])) {
							static::$custom_fields_date_flot[$prefix] = array();
						}
						static::$custom_fields_date_flot[$prefix][] = $id_field;
					}
				}
			}
		}
		return static::$custom_fields_date_flot;
	}
	
	public static function index_custom_field_date_flot($prefix, $field_id) {
		$requete = "delete from ".$prefix."_custom_dates where ".$prefix."_custom_champ=$field_id";
		$query = 'SELECT '.$prefix.'_custom_small_text, '. $prefix.'_custom_text, '.$prefix.'_custom_origine, '.$prefix.'_custom_order
                  FROM '.$prefix.'_custom_values
                  WHERE '.$prefix.'_custom_champ = '.$field_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_array($result)) {
				$value = $row[0];
				if (empty($row[0])) {
					$value = $row[1];
				}
				
				$interval = explode("|||", $value);
				$date_type = $interval[0];
				
				$date_start_signe = 1;
				$date_end_signe = 1;
				if (substr($interval[1], 0, 1) == '-') {
					// date avant JC
					$date_start_signe = -1;
					$interval[1] = substr($interval[1], 1);
				}
				if (substr($interval[2], 0, 1) == '-') {
					// date avant JC
					$date_end_signe = -1;
					$interval[2] = substr($interval[2], 1);
				}
				// années saisie inférieures à 4 digit
				if (strlen($interval[1]) < 4) {
					$interval[1] = str_pad($interval[1], 4, '0', STR_PAD_LEFT);
				}
				if ($interval[2] && strlen($interval[2]) < 4) {
					$interval[2] = str_pad($interval[2], 4, '0', STR_PAD_LEFT);
				}
				
				$date_start = detectFormatDate($interval[1], 'min');
				$date_end = detectFormatDate($interval[2], 'max');
				
				if ($date_start == '0000-00-00') {
					$date_start = '';
				}
				if ($date_end == '0000-00-00') {
					$date_end = '';
				}
				
				if ($date_start || $date_end) {
					if (!$date_end) {
						$date_end = detectFormatDate($interval[1], 'max');
						$date_end_signe = $date_start_signe;
					}
					// format en integer
					$date_start = str_replace('-', '', $date_start) * $date_start_signe;
					$date_end = str_replace('-', '', $date_end) * $date_end_signe;
					if ($date_end < $date_start) {
						$date = $date_start;
						$date_start = $date_end;
						$date_end = $date;
					}
					$requete = "INSERT INTO ".$prefix."_custom_dates (".$prefix."_custom_champ,".$prefix."_custom_origine,
								".$prefix."_custom_date_type,".$prefix."_custom_date_start,".$prefix."_custom_date_end,".$prefix."_custom_order)
								VALUES($field_id,$row[2],$date_type,'".$date_start."','".$date_end."',$row[3])";
					pmb_mysql_query($requete);
				}
			}
		}
	}
	
	protected static function is_temporary_file($file, $indexation_directory='notices') {
	    if(strtoupper(substr($file, 0, 10))."_".$indexation_directory."_".LOCATION == "INDEXATION_".$indexation_directory."_".LOCATION && substr($file, strlen($file)-4, 4) == ".pmb") {
			return true;
		}
		return false;
	}
	
	public static function clean_files($folder_path, $indexation_directory='notices') {
		if(is_dir($folder_path)) {
			$dh = opendir($folder_path);
			while(($file = readdir($dh)) !== false){
				if(!is_dir($folder_path.'/'.$file) && $file != "." && $file != ".." && $file != "CVS"){
				    if(static::is_temporary_file($file, $indexation_directory)) {
						unlink($folder_path.'/'.$file);
					}
				}
			}
			return true;
		}
		return false;
	}
} // fin de déclaration de la classe netbase_entities
