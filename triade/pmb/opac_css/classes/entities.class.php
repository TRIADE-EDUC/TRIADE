<?php
// +-------------------------------------------------+
// ï¿½ 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities.class.php,v 1.25.4.2 2025/05/05 14:44:35 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once "$class_path/authority.class.php";
require_once "$class_path/record_display.class.php";

class entities {
    public static $entities;
    public static $isbd = [];

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
    			TYPE_CMS_EDITORIAL,
    			TYPE_CONCEPT,
    			TYPE_ANIMATION
    	);
    }

	public static function get_entities_labels() {
	    global $msg;

	    $entities = array(
	    		TYPE_NOTICE => $msg['notice'],
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
	    		TYPE_CMS_SECTION => $msg['onto_common_cms_section'],
	    		TYPE_CMS_ARTICLE => $msg['onto_common_cms_article'],
	    		TYPE_CONCEPT => $msg['search_concept_title'],
                TYPE_ANIMATION => $msg['selvars_animation_name'],
                TYPE_CMS_EDITORIAL => $msg['cms_menu_editorial'],
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
				return 'external';
			case TYPE_ONTOLOGY:
			    return 'ontologies';
			case TYPE_ANIMATION:
			    return 'animations';
			case TYPE_CMS_ARTICLE:
			    return 'articles';
			case TYPE_CMS_SECTION:
			    return 'sections';
			case TYPE_CMS_EDITORIAL:
			    return 'cms_editorial';
			default:
			    if ($type > 10000) {
			        return 'ontologies';
			    }
			    if ($type > 1000) {
			        return 'authperso';
			    }
			    break;
		}
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
	        case TYPE_CONCEPT:
	            return AUT_TABLE_CONCEPT;
	        case TYPE_AUTHPERSO :
	            return AUT_TABLE_AUTHPERSO;
	        case TYPE_AUTHORITY :
	            return AUT_TABLE_AUTHORITY;
	        default:
	            return 0;
	    }
	}

    public static function get_table_prefix_from_const($authority_type) {
        switch ($authority_type) {
            case AUT_TABLE_AUTHORS :
                return 'author';
            case AUT_TABLE_CATEG :
                return 'categorie';
            case AUT_TABLE_PUBLISHERS :
                return 'publisher';
            case AUT_TABLE_COLLECTIONS :
                return 'collection';
            case AUT_TABLE_SUB_COLLECTIONS :
                return 'sub_coll';
            case AUT_TABLE_SERIES :
                return 'serie';
            case AUT_TABLE_TITRES_UNIFORMES :
                return 'tu';
            case AUT_TABLE_INDEXINT :
                return 'indexint';
            case AUT_TABLE_CONCEPT :
                return 'concept';
            case AUT_TABLE_AUTHPERSO :
                return 'authperso';
        }
    }

    public static function get_authority_table_from_const($authority_type) {
        switch ($authority_type) {
            case AUT_TABLE_AUTHORS :
                return 'authors';
            case AUT_TABLE_CATEG :
                return 'categories';
            case AUT_TABLE_PUBLISHERS :
                return 'publishers';
            case AUT_TABLE_COLLECTIONS :
                return 'collections';
            case AUT_TABLE_SUB_COLLECTIONS :
                return 'sub_collections';
            case AUT_TABLE_SERIES :
                return 'series';
            case AUT_TABLE_TITRES_UNIFORMES :
                return 'titres_uniformes';
            case AUT_TABLE_INDEXINT :
                return 'indexint';
            case AUT_TABLE_AUTHPERSO :
                return 'authperso_authorities';
            case AUT_TABLE_CONCEPT :
            default :
                return '';
        }
    }

    public static function get_authority_id_from_const($authority_type) {
        switch ($authority_type) {
            case AUT_TABLE_AUTHORS :
                return 'author_id';
            case AUT_TABLE_CATEG :
                return 'num_noeud';
            case AUT_TABLE_PUBLISHERS :
                return 'ed_id';
            case AUT_TABLE_COLLECTIONS :
                return 'collection_id';
            case AUT_TABLE_SUB_COLLECTIONS :
                return 'sub_coll_id';
            case AUT_TABLE_SERIES :
                return 'serie_id';
            case AUT_TABLE_TITRES_UNIFORMES :
                return 'tu_id';
            case AUT_TABLE_INDEXINT :
                return 'indexint_id';
            case AUT_TABLE_AUTHPERSO :
                return 'id_authperso_authority';
            case AUT_TABLE_CONCEPT :
            default :
                return '';
        }
    }

    public static function get_searcher_mode_from_type(string $type){
        switch($type){
            case 'authors' :
            case 'author' :
            case 'auteur' :
            case 'auteurs' :
                return 'authors';
            case 'categories' :
            case 'categorie' :
            case 'category' :
                return 'categories';
            case 'editeur' :
            case 'editeurs' :
            case 'publisher' :
            case 'publishers' :
                return 'publishers';
            case 'collection' :
            case 'collections' :
                return 'collections';
            case 'subcollections' :
            case 'subcollection' :
                return 'subcollections';
            case 'serie' :
            case 'series' :
                return 'series';
            case 'indexint' :
                return 'indexint';
            case 'titres_uniformes':
            case 'titre_uniforme':
            case 'work':
            case 'works':
                return 'titres_uniformes';
            case 'skos_concepts' :
                return 'skos_concepts';
            case 'concepts':
            case 'concept':
                return 'concepts';
            case 'authpersos' :
            case 'authperso' :
                return 'authpersos';
            case 'record' :
            case 'records' :
            case 'notices' :
            case 'notice' :
                return 'records';
            case 'serial' :
                return 'serials';
            case 'bull' :
                return 'bulletins';
            case 'animations' :
            case 'animation' :
                return 'animations';
            default :
                if(strpos($type,'authperso') !== false){
                    return $type;
                }
                return '';
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
            case "authorities" :
            default:
                $authority = authorities_collection::get_authority($type, $id);
                return $authority->get_isbd();
        }
    }

    public static function get_entity_template($id, $type, $django_directory = "") {
        $template = "";
        $aut_table = "";
        if (!empty($type) && !empty($id) && is_numeric($id)) {

            switch ($type) {
                case 'categories' :
                case 'categorie' :
                case 'category' :
                    $aut_table = AUT_TABLE_CATEG;
                    $string_type = 'category';
                    break;
                case 'authors' :
                case 'author' :
                case 'auteur' :
                case 'auteurs' :
                    $aut_table = AUT_TABLE_AUTHORS;
                    $string_type = 'author';
                    break;
                case 'editeur' :
                case 'editeurs' :
                case 'publisher' :
                case 'publishers' :
                    $aut_table = AUT_TABLE_PUBLISHERS;
                    $string_type = 'publisher';
                    break;
                case 'titres_uniformes':
                case 'titre_uniforme':
                case 'work':
                case 'works':
                    $aut_table = AUT_TABLE_TITRES_UNIFORMES;
                    $string_type = 'titre_uniforme';
                    break;
                case 'collections':
                case 'collection':
                    $aut_table = AUT_TABLE_COLLECTIONS;
                    $string_type = 'collection';
                    break;
                case 'subcollections':
                case 'subcollection':
                    $aut_table = AUT_TABLE_SUB_COLLECTIONS;
                    $string_type = 'subcollection';
                    break;
                case 'indexint':
                    $aut_table = AUT_TABLE_INDEXINT;
                    $string_type = 'indexint';
                    break;
                case 'serie':
                case 'series':
                    $aut_table = AUT_TABLE_SERIES;
                    $string_type = 'serie';
                    break;
                case 'concepts':
                case 'concept':
                    $aut_table = AUT_TABLE_CONCEPT;
                    $string_type = 'concept';
                    break;
                case 'notice':
                case 'notices':
                case 'records':
                case 'record':
                    if (!empty($id)) {
                        $template = record_display::get_display_in_contribution($id, $django_directory);
                    }
                    break;
                default:
                    if(strpos($type,'authperso') !== false){
                        $aut_table = AUT_TABLE_AUTHPERSO;
                        $string_type = 'authperso';
                    }
                    break;
            }
            if ($aut_table) {
                $authority = new authority(0, $id, $aut_table);
                $template = $authority->get_display_in_contribution($string_type, $django_directory);
            }
        }
        return $template;
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

    /**
     * Retourne le type d'entité en chaine de caractère
     * @param int $type constante
     * @return string
     */
    public static function get_entity_name_from_type($type) {
        switch ($type) {
            case TYPE_CATEGORY:
                return 'category';
            case TYPE_AUTHOR:
                return 'author';
            case TYPE_PUBLISHER:
                return 'publisher';
            case TYPE_TITRE_UNIFORME:
                return 'titre_uniforme';
            case TYPE_COLLECTION:
                return 'collection';
            case TYPE_SUBCOLLECTION:
                return 'subcollection';
            case TYPE_INDEXINT:
                return 'indexint';
            case TYPE_SERIE:
                return 'serie';
            case TYPE_CONCEPT:
                return 'concept';
            case TYPE_NOTICE:
                return 'record';
            case TYPE_AUTHPERSO:
                return 'authperso';
            case TYPE_CMS_SECTION:
                return 'section';
            case TYPE_CMS_ARTICLE:
                return 'article';
            case TYPE_CMS_EDITORIAL:
                return 'cms_editorial';
            case TYPE_ANIMATION:
                return 'animation';
            default:
                if ($type > 10000) {
                    return 'ontologies';
                }
                if ($type > 1000) {
                    return 'authperso';
                }
                return '';
        }
    }

    /**
     * Retourne l'url du picto de l'entité
     * @param string $entity
     * @param int $id
     * @return string
     */
    public static function get_picto_url_from_entity(string $entity, int $id) {

        switch ($entity) {
            case 'notice':
            case 'notices':
            case 'record':
            case 'records':
                $bibliographical_lvl = notice::get_niveau_biblio($id);
                $doctype = notice::get_typdoc($id);
                if (!empty($bibliographical_lvl) && !empty($doctype)) {
                    return notice::get_picture_url_no_image($bibliographical_lvl, $doctype);
                }
                return get_url_icon('icon_a.gif');

            default:
                $auth_const = authority::get_const_type_object($entity);
                if ($auth_const != 0) {
                    $authority = new authority(0, $id, $auth_const);
                    return $authority->get_type_icon();
                }
                break;
        }
        return "";
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

    public static function get_isbd($id, $type) {
    	$id = intval($id);
    	$type = intval($type);
    	if(empty(static::$isbd[$type][$id])) {
    		$entity = new entity($id, $type);
    		static::$isbd[$type][$id] = $entity->get_isbd();
    	}
    	return static::$isbd[$type][$id];
    }
}