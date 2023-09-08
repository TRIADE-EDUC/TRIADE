<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase_authorities.class.php,v 1.1 2017-07-17 09:55:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/indexations_collection.class.php");

class netbase_authorities {
	
	public function __construct() {
		
	}
	
	public static function index_from_query($query, $object_type) {
		$result = pmb_mysql_query($query);
		$nb_indexed = pmb_mysql_num_rows($result);
		if ($nb_indexed) {
			$indexation_authority = indexations_collection::get_indexation($object_type);
			$indexation_authority->set_deleted_index(true);
			while(($row = pmb_mysql_fetch_object($result))) {
				$indexation_authority->maj($row->id);
			}
			pmb_mysql_free_result($result);
		}
		return $nb_indexed;
	}
} // fin de déclaration de la classe netbase
