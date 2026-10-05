<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_index.class.php,v 1.18 2017/12/14 18:11:21 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class onto_index
*/
class onto_index {
	
	static protected $instances = array();
	
	/**
	 * 
	 */
	public function __construct(){		
	}	
	
	/**
	 * Methode qui retourne l'instance de la classe d'indexation correspondant à l'ontologie
	 * @param string $onto_name
	 * @return onto_common_index
	 */
	public static function get_instance($onto_name = "common"){
		$prefix="onto_";
		$suffixe = "_index";
		$instance_name = $prefix."common".$suffixe;
		if($onto_name && class_exists($prefix.$onto_name.$suffixe)){			
			$instance_name = $prefix.$onto_name.$suffixe;
		}
		if (!isset(self::$instances[$instance_name])) {
			self::$instances[$instance_name] = new $instance_name(); 
		}
		return self::$instances[$instance_name];
	}
}