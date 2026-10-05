<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexations_collection.class.php,v 1.5 2023/05/05 13:38:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/indexation.class.php");
require_once($class_path."/indexation_authority.class.php");

class indexations_collection {
	
	static private $indexations = array();
	
	static public function get_xml_file_path($object_type) {
		global $include_path;
		
		switch($object_type){
			case AUT_TABLE_AUTHORS :
				return $include_path."/indexation/authorities/authors/champs_base.xml";
			case AUT_TABLE_CATEG :
				return $include_path."/indexation/authorities/categories/champs_base.xml";
			case AUT_TABLE_PUBLISHERS :
				return $include_path."/indexation/authorities/publishers/champs_base.xml";
			case AUT_TABLE_COLLECTIONS :
				return $include_path."/indexation/authorities/collections/champs_base.xml";
			case AUT_TABLE_SUB_COLLECTIONS :
				return $include_path."/indexation/authorities/subcollections/champs_base.xml";
			case AUT_TABLE_SERIES :
				return $include_path."/indexation/authorities/series/champs_base.xml";
			case AUT_TABLE_INDEXINT :
				return $include_path."/indexation/authorities/indexint/champs_base.xml";
			case AUT_TABLE_TITRES_UNIFORMES :
				return $include_path."/indexation/authorities/titres_uniformes/champs_base.xml";
			case AUT_TABLE_FAQ :
				return $include_path."/indexation/faq/question.xml";
			case AUT_TABLE_INDEX_CONCEPT :
				return '';
			case AUT_TABLE_AUTHPERSO :
				return '';
			default :
				return null;
		}
	}
	
	static public function get_indexation($object_type) {
		$object_type = intval($object_type);
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
			case AUT_TABLE_CATEG :
			case AUT_TABLE_PUBLISHERS :
			case AUT_TABLE_COLLECTIONS :
			case AUT_TABLE_SUB_COLLECTIONS :
			case AUT_TABLE_SERIES :
			case AUT_TABLE_INDEXINT :
			case AUT_TABLE_TITRES_UNIFORMES :
				self::$indexations[$object_type] = new indexation_authority(static::get_xml_file_path($object_type), "authorities", $object_type);
				break;
			case AUT_TABLE_FAQ :
				self::$indexations[$object_type] =  new indexation(static::get_xml_file_path($object_type), "faq_questions", AUT_TABLE_FAQ);
				break;
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