<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_collection.class.php,v 1.10 2023/07/26 12:49:31 dbellamy Exp $

/**
 * Classe de collection d'autorités pour éviter d'instancier plusieurs fois les mêmes autorités dans une même page
 * @author apetithomme
 *
 */
class authorities_collection {
	static private $authorities = array();

	static public function get_authority($authority_type, $authority_id, $params = array()) {
		$authority_id = intval($authority_id);
		if (!$authority_type) {
			return null;
		}
		if (isset($params['num_object']) && isset($params['type_object']) && isset(self::$authorities[$authority_type][$params['num_object'].'_'.$params['type_object']])) {
		    return self::$authorities[$authority_type][$params['num_object'].'_'.$params['type_object']];
		}
		if ($authority_id && isset(self::$authorities[$authority_type][$authority_id])) {
			return self::$authorities[$authority_type][$authority_id];
		}

		if (!isset(self::$authorities[$authority_type])) {
			self::$authorities[$authority_type] = array();
		}

		switch($authority_type){
			case "author" :
			case (string) AUT_TABLE_AUTHORS :
				self::load_class("author");
				self::$authorities[$authority_type][$authority_id] = new auteur($authority_id);
				break;
			case "publisher" :
			case (string) AUT_TABLE_PUBLISHERS :
				self::load_class("publisher");
				self::$authorities[$authority_type][$authority_id] = new publisher($authority_id);
				break;
			case "collection" :
			case (string) AUT_TABLE_COLLECTIONS :
				self::load_class("collection");
				self::$authorities[$authority_type][$authority_id] = new collection($authority_id);
				break;
			case "subcollection" :
			case (string) AUT_TABLE_SUB_COLLECTIONS :
				self::load_class("subcollection");
				self::$authorities[$authority_type][$authority_id] = new subcollection($authority_id);
				break;
			case "serie" :
			case (string) AUT_TABLE_SERIES :
				self::load_class("serie");
				self::$authorities[$authority_type][$authority_id] = new serie($authority_id);
				break;
			case "indexint" :
			case (string) AUT_TABLE_INDEXINT :
				self::load_class("indexint");
				self::$authorities[$authority_type][$authority_id] = new indexint($authority_id);
				break;
			case "titre_uniforme" :
			case (string) AUT_TABLE_TITRES_UNIFORMES :
				self::load_class("titre_uniforme");
				self::$authorities[$authority_type][$authority_id] = new titre_uniforme($authority_id);
				break;
			case "category" :
			case (string) AUT_TABLE_CATEG :
				global $lang;
				self::load_class("categorie");
				self::$authorities[$authority_type][$authority_id] = new categorie($authority_id,$lang);
				break;
			case "concept" :
			case (string) AUT_TABLE_CONCEPT :
				self::load_class("skos/skos_concept");
				if(!is_numeric($authority_id)) {
				    $authority_id = onto_common_uri::get_id($authority_id);
				}
                self::$authorities[$authority_type][$authority_id] = new skos_concept($authority_id);
                break;
			case (string) AUT_TABLE_INDEX_CONCEPT :
			    self::$authorities[$authority_type][$authority_id] = new concept($authority_id);
			    break;
			case "authperso" :
			case (string) AUT_TABLE_AUTHPERSO :
				self::load_class("authperso_data");
				self::$authorities[$authority_type][$authority_id] = new authperso_data($authority_id);
				break;
			case 'authority' :
			case (string) AUT_TABLE_AUTHORITY :
			    if($authority_id > 0){
			        $aut = new authority($authority_id);
			    }else{
			        if(!is_numeric($params['num_object'])) {
			            $params['num_object'] = onto_common_uri::get_id($params['num_object']);
			        }
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

	static private function load_class($classname) {
		global $base_path,$include_path,$class_path,$javascript_path,$style_path;
		require_once($class_path."/".$classname.".class.php");
	}
}