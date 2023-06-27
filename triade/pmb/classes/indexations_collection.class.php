<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexations_collection.class.php,v 1.2 2017-12-04 14:00:31 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/indexation.class.php");
require_once($class_path."/indexation_authority.class.php");

class indexations_collection {
	
	static private $indexations = array();
	
	static public function get_indexation($object_type) {
		global $include_path;
		
		$object_type = $object_type*1;
		if (!$object_type) {
			return null;
		}
		
		if (isset(self::$indexations[$object_type])) {
			return self::$indexations[$object_type];
		}
		
		if (!isset(self::$indexations[$object_type])) {
			self::$indexations[$object_type] = array();
		}
		
		switch($object_type){
			case AUT_TABLE_AUTHORS :
				self::$indexations[$object_type] = new indexation_authority($include_path."/indexation/authorities/authors/champs_base.xml", "authorities", AUT_TABLE_AUTHORS);
				break;
			case AUT_TABLE_CATEG :
				self::$indexations[$object_type] = new indexation_authority($include_path."/indexation/authorities/categories/champs_base.xml", "authorities", AUT_TABLE_CATEG);
				break;
			case AUT_TABLE_PUBLISHERS :
				self::$indexations[$object_type] = new indexation_authority($include_path."/indexation/authorities/publishers/champs_base.xml", "authorities", AUT_TABLE_PUBLISHERS);
				break;
			case AUT_TABLE_COLLECTIONS :
				self::$indexations[$object_type] = new indexation_authority($include_path."/indexation/authorities/collections/champs_base.xml", "authorities", AUT_TABLE_COLLECTIONS);
				break;
			case AUT_TABLE_SUB_COLLECTIONS :
				self::$indexations[$object_type] = new indexation_authority($include_path."/indexation/authorities/subcollections/champs_base.xml", "authorities", AUT_TABLE_SUB_COLLECTIONS);
				break;
			case AUT_TABLE_SERIES :
				self::$indexations[$object_type] = new indexation_authority($include_path."/indexation/authorities/series/champs_base.xml", "authorities", AUT_TABLE_SERIES);
				break;
			case AUT_TABLE_INDEXINT :
				self::$indexations[$object_type] = new indexation_authority($include_path."/indexation/authorities/indexint/champs_base.xml", "authorities", AUT_TABLE_INDEXINT);
				break;
			case AUT_TABLE_TITRES_UNIFORMES :
				self::$indexations[$object_type] = new indexation_authority($include_path."/indexation/authorities/titres_uniformes/champs_base.xml", "authorities", AUT_TABLE_TITRES_UNIFORMES);
				break;
// 			case AUT_TABLE_CONCEPT :
// 				self::$indexations[$object_type] = 
// 				break;
// 			case AUT_TABLE_INDEX_CONCEPT :
// 				self::$indexations[$object_type] = 
// 				break;
// 			case AUT_TABLE_AUTHPERSO :
// 				self::$indexations[$object_type] = 
// 				break;
			default :
				return null;
		}
		return self::$indexations[$object_type];
	}
}