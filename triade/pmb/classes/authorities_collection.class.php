<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_collection.class.php,v 1.11 2019-06-06 13:05:45 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/author.class.php");
require_once($class_path."/category.class.php");
require_once($class_path."/editor.class.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/serie.class.php");
require_once($class_path."/indexint.class.php");
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path."/skos/skos_concept.class.php");
require_once($class_path."/concept.class.php");
require_once($class_path."/authperso_authority.class.php");

class authorities_collection {
	
	static private $authorities = array();
	
	static public function get_authority($authority_type, $authority_id, $params = array()) {
		$authority_type = $authority_type*1;
		$authority_id = $authority_id*1;
		if (!$authority_type) {
			return null;
		}
		if (isset($params['num_object']) && isset($params['type_object']) && isset(self::$authorities[$authority_type][$params['num_object'].'_'.$params['type_object']])) {
		    return self::$authorities[$authority_type][$params['num_object'].'_'.$params['type_object']];
		}
		if ($authority_id > 0 && isset(self::$authorities[$authority_type][$authority_id])) {
		    return self::$authorities[$authority_type][$authority_id];
		}
		
		if (!isset(self::$authorities[$authority_type]) || count(self::$authorities[$authority_type]) > 10000) {
			self::$authorities[$authority_type] = array();
		}
		
		switch($authority_type){
			case AUT_TABLE_AUTHORS :
				if(!isset($params['recursif'])) $params['recursif'] = 0;
				self::$authorities[$authority_type][$authority_id] = new auteur($authority_id, $params['recursif']);
				break;
			case AUT_TABLE_CATEG :
				global $lang;
				self::$authorities[$authority_type][$authority_id] = new category($authority_id,$lang);
				break;
			case AUT_TABLE_PUBLISHERS :
				self::$authorities[$authority_type][$authority_id] = new editeur($authority_id);
				break;
			case AUT_TABLE_COLLECTIONS :
				self::$authorities[$authority_type][$authority_id] = new collection($authority_id);
				break;
			case AUT_TABLE_SUB_COLLECTIONS :
				self::$authorities[$authority_type][$authority_id] = new subcollection($authority_id);
				break;
			case AUT_TABLE_SERIES :
				self::$authorities[$authority_type][$authority_id] = new serie($authority_id);
				break;
			case AUT_TABLE_INDEXINT :
				self::$authorities[$authority_type][$authority_id] = new indexint($authority_id);
				break;
			case AUT_TABLE_TITRES_UNIFORMES :
				self::$authorities[$authority_type][$authority_id] = new titre_uniforme($authority_id);
				break;
			case AUT_TABLE_CONCEPT :
				self::$authorities[$authority_type][$authority_id] = new skos_concept($authority_id);
				break;
			case AUT_TABLE_INDEX_CONCEPT :
				self::$authorities[$authority_type][$authority_id] = new concept($authority_id);
				break;
			case AUT_TABLE_AUTHPERSO :
				self::$authorities[$authority_type][$authority_id] = new authperso_authority($authority_id);
				break;
			case AUT_TABLE_CATEGORIES :
			    self::$authorities[$authority_type][$authority_id] = new categories($authority_id,$params['lang'],$params['for_indexation']);
			    break;
			case AUT_TABLE_AUTHORITY :
			    if (!isset($params['num_object'])) $params['num_object'] = '';
			    if($authority_id > 0){
			        $aut = new authority($authority_id);
			    }else{
                    $aut = new authority($authority_id,$params['num_object'],$params['type_object']);
                    $authority_id = $aut->get_id();
			    }
			    self::$authorities[$authority_type][$authority_id] = $aut;  
			    self::$authorities[$authority_type][$aut->get_num_object().'_'.$aut->get_type_object()] = $aut; 
			    break;
			default :
				return null;
		}
		return self::$authorities[$authority_type][$authority_id];
	}
}