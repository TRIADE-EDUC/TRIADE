<?php
// +-------------------------------------------------+
// � 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities.class.php,v 1.22.2.1.2.2 2025/04/25 11:52:14 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class entities{
    public static $entities;

    public static $isbd;

    public static function get_entities() {
    	return array(
    			TYPE_NOTICE,
    			TYPE_AUTHOR,
    			TYPE_CATEGORY,
    			TYPE_PUBLISHER,
    			TYPE_COLLECTION,
    			TYPE_SUBCOLLECTION,
    			TYPE_SERIE,
    			TYPE_TITRE_UNIFORME,
    			TYPE_INDEXINT,
    			TYPE_EXPL,
    			TYPE_EXPLNUM,
    			TYPE_AUTHPERSO,
    			TYPE_CMS_SECTION,
    			TYPE_CMS_ARTICLE,
    			TYPE_CONCEPT,
    			TYPE_ANIMATION
    	);
    }

	public static function get_entities_labels() {
	    global $msg;

	    $entities = array(
	    		TYPE_NOTICE => $msg['288'],
	    		TYPE_AUTHOR => $msg['isbd_author'],
	    		TYPE_CATEGORY => $msg['isbd_categories'],
	    		TYPE_PUBLISHER => $msg['isbd_editeur'],
	    		TYPE_COLLECTION => $msg['isbd_collection'],
	    		TYPE_SUBCOLLECTION => $msg['isbd_subcollection'],
	    		TYPE_SERIE => $msg['isbd_serie'],
	    		TYPE_TITRE_UNIFORME => $msg['isbd_titre_uniforme'],
	    		TYPE_INDEXINT => $msg['isbd_indexint'],
	    		TYPE_EXPL => $msg['376'],
	    		TYPE_EXPLNUM => $msg['search_explnum'],
	    		TYPE_AUTHPERSO => $msg['search_by_authperso_title'],
	    		TYPE_CMS_SECTION => $msg['cms_menu_editorial_section'],
	    		TYPE_CMS_ARTICLE => $msg['cms_menu_editorial_article'],
	    		TYPE_CONCEPT => $msg['search_concept_title'],
	           TYPE_ANIMATION => $msg['selvars_animation_name']
	    );
	    return $entities;
	}

	public static function get_entities_options($selected) {
	    global $charset;
	    $entities = static::get_entities_labels();
	    $html = '';
	    foreach ($entities as $value => $label) {
	        $html .= '<option value="'.$value.'" '.($value == $selected ? 'selected="selected"' : '').'>'.htmlentities($label, ENT_QUOTES, $charset).'</option>';
	    }
	    return $html;
	}

	public static function get_string_from_const_type($type){
	    if(!is_numeric($type)){
	        return $type;
	    }
		switch ($type) {
			case TYPE_NOTICE :
				return 'notices';
			case TYPE_AUTHOR :
				return 'authors';
			case TYPE_CATEGORY :
				return 'categories';
			case TYPE_PUBLISHER :
				return 'publishers';
			case TYPE_COLLECTION :
				return 'collections';
			case TYPE_SUBCOLLECTION :
				return 'subcollections';
			case TYPE_SERIE :
				return 'series';
			case TYPE_TITRE_UNIFORME :
				return 'titres_uniformes';
			case TYPE_INDEXINT :
				return 'indexint';
			case TYPE_CONCEPT_PREFLABEL:
			case TYPE_CONCEPT:
				return 'concepts';
			case TYPE_AUTHPERSO :
				return 'authperso';
			case TYPE_EXTERNAL :
				return 'notices_externes';
			case TYPE_ONTOLOGY:
			    return 'ontologies';
			case TYPE_ANIMATION:
			    return 'animations';
		}
		if ($type > 10000) {
		    return 'ontologies'.($type - 10000);
		}
		if ($type > 1000) {
		    return 'authperso_'.($type - 1000);
		}
	}

	public static  function get_sort_string_from_const_type($type){
	    if ($type == TYPE_EXTERNAL) {
	        return "external";
	    }
	    return static::get_string_from_const_type($type);
	}

	public static function get_query_from_entity_linked($id, $get_type, $from_type) {
		$query = "";
		switch($get_type){
			case 'publisher':
				$query .= "SELECT ed_id FROM publishers";
				switch($from_type){
					case 'collection':
						$query .= " JOIN collections ON ed_id = collection_parent where collection_id = ".$id;
						break;
					case 'sub_collection':
						$query .= " JOIN collections ON ed_id = collection_parent JOIN sub_collections ON sub_coll_parent = collection_id where sub_coll_id = ".$id;
						break;
				}
				break;
			case 'collection':
				$query .= "SELECT collection_id FROM collections";
				switch($from_type){
					case 'publisher':
						 $query .= " JOIN publishers ON ed_id = collection_parent where ed_id = ".$id;
						break;
					case 'sub_collection':
						$query .= " JOIN sub_collections ON sub_coll_parent = collection_id  where sub_coll_id = ".$id;
						break;

				}
				break;
			case 'sub_collection':
				$query = "SELECT sub_coll_id FROM sub_collections";
				switch($from_type){
					case 'publisher':
						$query .= " JOIN collections ON sub_coll_parent = collection_id WHERE collection_parent = ".$id;
						break;
					case 'collection':
						 $query .= " WHERE sub_coll_parent = ".$id;
						break;

				}
				break;
		}
		return $query;
	}

	public static function get_entity_from_lvl($lvl) {
        $entity_type = 0;
        switch ($lvl) {
            case 'categories' :
            case 'categorie' :
            case 'category' :
            case 'categ_see':
                $entity_type = TYPE_CATEGORY;
                break;
            case 'authors':
            case 'author' :
            case 'auteur' :
            case 'auteurs' :
            case 'author_see':
                $entity_type = TYPE_AUTHOR;
                break;
            case 'editeur' :
            case 'editeurs' :
            case 'publisher' :
            case 'publishers':
            case 'publisher_see':
                $entity_type = TYPE_PUBLISHER;
                break;
            case 'work':
            case 'works':
            case 'titre_uniforme':
            case 'titres_uniformes':
            case 'titre_uniforme_see':
                $entity_type = TYPE_TITRE_UNIFORME;
                break;
            case 'collections':
            case 'collection':
            case 'coll_see':
                $entity_type = TYPE_COLLECTION;
                break;
            case 'subcoll_see':
            case 'subcollection':
            case 'subcollections':
                $entity_type = TYPE_SUBCOLLECTION;
                break;
            case 'indexint':
            case 'indexint_see':
                $entity_type = TYPE_INDEXINT;
                break;
            case 'serie':
            case 'series':
            case 'serie_see':
                $entity_type = TYPE_SERIE;
                break;
            case 'concept':
            case 'concepts':
            case 'concept_see' :
                $entity_type = TYPE_CONCEPT;
                break;
            case 'notice':
            case 'notices':
            case 'records':
            case 'record':
            case 'notice_display':
                $entity_type = TYPE_NOTICE;
                break;
            case 'animation':
            case 'animations':
                $entity_type = TYPE_ANIMATION;
                break;
            case 'articles':
            case 'article':
                $entity_type = TYPE_CMS_ARTICLE;
                break;
            case 'sections':
            case 'section':
                $entity_type = TYPE_CMS_SECTION;
                break;
            case 'cms_editorial':
                $entity_type = TYPE_CMS_EDITORIAL;
                break;
            case 'authperso_see':
            default:
                if(strpos($lvl,'authperso') !== false) {
                    $entity_type = TYPE_AUTHPERSO;
                }
                break;
        }
        return $entity_type;
    }

	public static function get_aut_table_from_type($type) {
	    switch ($type) {
	        case TYPE_AUTHOR :
	            return AUT_TABLE_AUTHORS;
	        case TYPE_CATEGORY :
	            return AUT_TABLE_CATEG;
	        case TYPE_PUBLISHER :
	            return AUT_TABLE_PUBLISHERS;
	        case TYPE_COLLECTION :
	            return AUT_TABLE_COLLECTIONS;
	        case TYPE_SUBCOLLECTION :
	            return AUT_TABLE_SUB_COLLECTIONS;
	        case TYPE_SERIE :
	            return AUT_TABLE_SERIES;
	        case TYPE_TITRE_UNIFORME :
	            return AUT_TABLE_TITRES_UNIFORMES;
	        case TYPE_INDEXINT :
	            return AUT_TABLE_INDEXINT;
	        case TYPE_CONCEPT_PREFLABEL:
	        case TYPE_CONCEPT:
	            return AUT_TABLE_CONCEPT;
	        case TYPE_AUTHPERSO :
	            return AUT_TABLE_AUTHPERSO;
	        default:
	            return 0;
	    }
	}

	public static function get_prefixes() {
	    return array(
	        'author',
	        'categ',
	        'publisher',
	        'collection',
	        'subcollection',
	        'serie',
	        'indexint',
	        'skos',
	        'tu',
	        'authperso',
	        'expl',
	        'notices'
	    );
	}

	//transform URL what parameter into an authority constant of PMB
	public static function get_constant_from_what_parameter($what) {
	    switch($what) {
	        case "auteur" :
	            return TYPE_AUTHOR;
	        case "categorie" :
	            return TYPE_CATEGORY;
	        case "editeur" :
	            return TYPE_PUBLISHER;
	        case "collection" :
	            return TYPE_COLLECTION;
	        case "subcollection" :
	            return TYPE_SUBCOLLECTION;
	        case "titre_uniforme" :
	            return TYPE_TITRE_UNIFORME;
	        case "indexint" :
	        	return TYPE_INDEXINT;
	        case "serie" :
	        case "series" :
	        	return TYPE_SERIE;
// 	        case "" :
// 	            return TYPE_CONCEPT;
	        case "authperso" :
	            return AUT_TABLE_AUTHPERSO;
	        default :
	            return $what;
	    }
	}

	public static function get_label_from_entity(int $id, string $type) {
	    switch ($type) {
	        case "notice" ;
	        case "record" :
	            return notice::get_notice_title($id);
	        case "bulletin" :
	            $notice_id = bulletinage::get_notice_id_from_id($id);
	            return notice::get_notice_title($notice_id);
	        case "authority" :
	        default:
	            $authority = authorities_collection::get_authority($type, $id);
	            return $authority->get_isbd();
	    }
	}

	/**
	 * Liste les methodes, utile pour les web
	 * @return []
	 */
	public static function get_methods_infos($object) {
	    $methods_tab = [];
	    if (is_object($object)) {
	        $rc = new ReflectionClass($object);
	        $methods = $rc->getMethods(ReflectionMethod::IS_PUBLIC);
	        $excluded_methods = [
	            "__construct",
	            "get_instance",
	            "__get",
	            "__set",
	            "__call",
	        ];
	        foreach ($methods as $method) {
	            if (!in_array($method->getName(), $excluded_methods)) {
	                $doc = $method->getDocComment();
	                $doc = substr($doc, 0, strpos($doc, "@"));
	                $doc = str_replace(["/", "*"], "", $doc);
	                $methods_tab[] = [$method->getName(), trim($doc)];
	            }
	        }
	        sort($methods_tab);
	    }
	    return $methods_tab;
	}

	/**
	 * Liste les proprietes, utile pour les web
	 * @return []
	 */
	public static function get_properties_infos($object) {
	    $properties_tab = [];
	    if (is_object($object)) {
	        $rc = new ReflectionClass($object);
	        $properties = $rc->getProperties();
	        $excluded_properties = [];
	        foreach ($properties as $property) {
	            if (!in_array($property->getName(), $excluded_properties)) {
	                $doc = $property->getDocComment();
	                $doc = substr($doc, 0, strpos($doc, "@"));
	                $doc = str_replace(["/", "*"], "", $doc);
	                $properties_tab[] = [$property->getName(), trim($doc)];
	            }
	        }
	        sort($properties_tab);
	    }
	    return $properties_tab;
	}

	public static function get_entity_type_from_entity($entity) {
	    switch ($entity) {
	        case 'categories' :
	        case 'categorie' :
	        case 'category' :
	        case 'categ_see':
	            return TYPE_CATEGORY;
	        case 'authors':
	        case 'author' :
	        case 'auteur' :
	        case 'auteurs' :
	        case 'author_see':
	            return TYPE_AUTHOR;
	        case 'editeur' :
	        case 'editeurs' :
	        case 'publisher' :
	        case 'publishers':
	        case 'publisher_see':
	            return TYPE_PUBLISHER;
	        case 'work':
	        case 'works':
	        case 'titre_uniforme':
	        case 'titres_uniformes':
	        case 'titre_uniforme_see':
	            return TYPE_TITRE_UNIFORME;
	        case 'collections':
	        case 'collection':
	        case 'coll_see':
	            return TYPE_COLLECTION;
	        case 'subcoll_see':
	        case 'subcollection':
	        case 'subcollections':
	            return TYPE_SUBCOLLECTION;
	        case 'indexint':
	        case 'indexint_see':
	            return TYPE_INDEXINT;
	        case 'serie':
	        case 'series':
	        case 'serie_see':
	            return TYPE_SERIE;
	        case 'concept':
	        case 'concepts':
	        case 'concept_see' :
	            return TYPE_CONCEPT;
	        case 'notice':
	        case 'notices':
	        case 'records':
	        case 'record':
	        case 'notice_display':
	            return TYPE_NOTICE;
            case 'animations' :
            case 'animation' :
                return 'animations';
	        case 'authperso_see':
	        default:
	            if(strpos($entity, 'authperso') !== false) {
	                return TYPE_AUTHPERSO;
	            }
	            break;
	    }
	    return $entity;
	}

	public static function get_isbd($id, $type) {
		$id = intval($id);
		$type = intval($type);
		if(empty(static::$isbd[$type][$id])) {
			$entity = new entity($id, $type);
			static::$isbd[$type][$id] = $entity->get_isbd();
		}
		return static::$isbd[$type][$id];
	}

	public static function get_query_count($entity, $id_authperso=0) {
	    switch($entity){
	        case 'notices' :
	            return 'select count(*) from notices';
	        case 'authors' :
	            return 'select count(*) from authors';
	        case 'categories' :
	            return 'select count(*) from noeuds where autorite != "TOP"';
	        case 'publishers' :
	            return 'select count(*) from publishers';
	        case 'collections' :
	            return 'select count(*) from collections';
	        case 'subcollections' :
	            return 'select count(*) from sub_collections';
	        case 'series' :
	            return 'select count(*) from series';
	        case 'titres_uniformes' :
	            return 'select count(*) from titres_uniformes';
	        case 'indexint' :
	            return 'select count(*) from indexint';
	        case 'authperso' :
	            return 'select count(id_authperso_authority) from authperso join authperso_authorities on id_authperso= authperso_authority_authperso_num where id_authperso='.$id_authperso;
	        case 'concepts' :
	            return '';
	    }
	}
}