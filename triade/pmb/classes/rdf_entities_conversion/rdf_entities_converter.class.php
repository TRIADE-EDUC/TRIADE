<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter.class.php,v 1.4.8.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/rdf_entities_conversion/rdf_entities_converter_controller.class.php");
require_once($class_path."/authperso.class.php");
require_once($class_path."/cms/cms_article.class.php");
require_once($class_path."/cms/cms_section.class.php");
require_once($class_path."/explnum.class.php");
require_once($class_path."/expl.class.php");

class rdf_entities_converter {

	protected $entity_id;

	protected $entity_type;

	protected $table_name;

	protected $table_key;

	protected $ppersos_prefix;

	/**
	 * Store RDF associé
	 * @var rdf_entities_store
	 */
	protected $store;

	/**
	 * Tableau associatif champ table SQL / propriété classe RDF
	 * @var array
	 */
	protected $map_fields;

	/**
	 * Tableau associatif des champs de clé étrangère SQL / propriété RDF
	 * @var array
	 */
	protected $foreign_fields;

	/**
	 * Tableau contenant les entités et leurs tables de liaison
	 * @var array
	 */
	protected $linked_entities;

	/**
	 * Association champ / callable
	 * @var array
	 */
	protected $special_fields;

	/**
	 * Identifiant du contributeur
	 * @var int
	 */
	protected $contributor_id;

	/**
	 * Type de contributeur : 0 : Utilisateur gestion, 1 : Lecteur OPAC
	 * @var int
	 */
	protected $contributor_type;

	/**
	 * Tableau des données de l'entité à renvoyer
	 * @var array
	 */
	protected $entity_data;

	protected $prefix_rdf = "http://www.pmbservices.fr/ontology";

	protected $assertions;

	protected $depth;

	protected $uri = "";

	/**
	 * Constructeur
	 * @param rdf_entities_store $store Store rdf à utiliser
	 */
	public function __construct($entity_id, $entity_type, $uri = "", $depth = 1, $store = null) {
	    $this->entity_id = $entity_id * 1;
	    $this->entity_type = $entity_type;
	    if (!is_numeric($depth*1)) {
            $this->depth = -1;
	    }
	    if (!empty($store)) {
		  $this->store = $store;
	    }
	    $this->uri = $uri;
	    if (empty($this->uri)) {
	        $this->init_uri();
	    }
	}

	protected function init_map_fields() {
		$this->map_fields = array();
		return $this->map_fields;
	}

	protected function init_foreign_fields() {
		$this->foreign_fields = array();
		return $this->foreign_fields;
	}

	protected function init_linked_entities() {
		$this->linked_entities = array();
		return $this->linked_entities;
	}

	protected function init_special_fields() {
		$this->special_fields = array();
		return $this->special_fields;
	}

	/**
	 * Retourne la classe d'intégration associé au type d'entité
	 * @param string $uri URI du type d'entité
	 */
	protected function get_entity_integrator_from_type_uri($type_uri) {
		switch ($type_uri) {
			default :
				$is_cms = false;
				$type = substr($type_uri, strpos($type_uri, '#') + 1);
				$type = strtolower($type);

				$integrator_class = 'rdf_entities_integrator_'.$type;
				if (strpos($type, 'article') !== false) {
					$integrator_class = 'rdf_entities_integrator_article';
					$is_cms = true;
				}
				if (strpos($type, 'section') !== false) {
					$integrator_class = 'rdf_entities_integrator_section';
					$is_cms = true;
				}
				if (class_exists($integrator_class)) {
					$integrator = new $integrator_class($this->store);
					if($is_cms){
						$type_explode = explode('_', $type);
						$num_type = $type_explode[count($type_explode) - 1];
						if (is_numeric($num_type)) {
							$integrator->set_cms_type($num_type);
						}
					}
					return $integrator;

				}
				return null;
		}
	}

	/**
	 * Retourne l'identifiant de l'entité en cours d'intégration
	 */
	protected function get_entity_id() {
		return $this->entity_id;
	}

	/**
	 * renseigne l'identifiant de l'entité
	 */
	public function set_entity_id($id) {
	    $this->entity_id = intval($id);
		return $this;
	}

	protected function get_contributor_id($uri) {
		$this->contributor_id = 0;
		$this->contributor_type = 0;
		$contributor_property = $this->store->get_property($uri, 'pmb:has_contributor');
		if (!empty($contributor_property[0]['value'])) {
			if ($contributor_property[0]['value']*1) {
			    $this->contributor_id = intval($contributor_property[0]['value']);
				$this->contributor_type = 1;
			}
		}
		return $this->contributor_id;
	}

	public function get_store() {
		return $this->store;
	}

	public function update_property($property, $value) {
		if (!$this->entity_id) {
			return null;
		}
		$query = 'UPDATE '.$this->table_name.' SET '.$property.' = '.$value.' WHERE '.$this->table_key.' = '.$this->entity_id;
		pmb_mysql_query($query);
		return true;
	}

	public function get_map_fields() {
	    if (!isset($this->map_fields)) {
	        $this->init_map_fields();
	    }
	    return $this->map_fields;
	}

	public function save_in_store ($store) {

	}

	public function get_assertions() {
	    global $opac_url_base;
	    if (!isset($this->assertions)) {
    	    $this->assertions = array();

    	    if (empty($this->uri)) {
    	        $this->init_uri();
    	    }
	        $subject = $this->uri;

    	    $query = "SELECT * FROM ".$this->table_name." WHERE ".$this->table_key." = ".$this->entity_id;
    	    $result = pmb_mysql_query($query);
    	    $row = pmb_mysql_fetch_assoc($result);
    	    $this->init_map_fields();
    	    foreach ($this->map_fields as $key => $property) {
    	        if (isset($row[$key])) {
    	            $this->assertions[] = new onto_assertion($subject, $property, $row[$key],'http://www.w3.org/2000/01/rdf-schema#Literal', array('type' => 'literal'));
    	            //$this->assertions[$this->prefix_rdf.$this->entity_type."#".$this->entity_id][$property] = $row[$key];
    	        }
    	    }
    	    $this->init_foreign_fields();
    	    foreach ($this->foreign_fields as $key => $property) {
    	        if (isset($row[$key])) {
    	            $object_properties = $this->get_object_properties($row[$key], $property['type']);
    	            $value = "";
    	            if (!empty($row[$key])) {
    	                $value = $row[$key];
    	            }
    	            $this->assertions[] = new onto_assertion($subject, $property['property'], $value, $this->prefix_rdf."#".$property['type'], $object_properties);
        	    }
    	    }
    	    $this->get_assertions_from_linked_entities();
    	    $this->get_assertions_from_special_fields();

    	    if ($this->ppersos_prefix) {
    	        $onto_parametres_perso = new onto_parametres_perso($this->ppersos_prefix);
    	        $assertions = $onto_parametres_perso->get_assertions_for_rdf($this->entity_id, $subject);
    	        if (count($assertions)) {
    	            $this->assertions = array_merge($this->assertions, $assertions);
    	        }
    	    }
	    }
	    return $this->assertions;
	}

	protected function get_assertions_from_linked_entities() {
	    $this->init_linked_entities();
	    if (!count($this->linked_entities) || !$this->entity_id) {
	        return null;
	    }

	    if (empty($this->uri)) {
	        $this->init_uri();
	    }
	    $subject = $this->uri;

	    if (!isset($this->assertions)) {
	        $this->assertions = array();
	    }
	    foreach ($this->linked_entities as $property => $linked_entity) {
	        $query = 'SELECT '.$linked_entity['external_field_name'].'
                    FROM '.$linked_entity['table'].'
                    WHERE '.$linked_entity['reference_field_name'].' = "'.$this->entity_id.'"';
	        if (isset($linked_entity['other_fields']) && is_array($linked_entity['other_fields'])) {
	            foreach ($linked_entity['other_fields'] as $key => $value) {
	                $query .= ' AND '.$key.' = "'.$value.'"';
	            }
	        }
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            while ($row = pmb_mysql_fetch_array($result)) {
	                $object =  $this->get_uri_from_pmb_identifer($row[0], $linked_entity['type']);
// 	                $object =  onto_common_uri::get_new_uri($this->prefix_rdf."/".$linked_entity['type'].'#');
	                $object_type = $this->get_object_type_from_type($linked_entity['type']);
	                $object_properties = $this->get_object_properties($row[0], $linked_entity['type']);
	                if (!empty($linked_entity['abstract_entity'])) {
	                    $type = $linked_entity['converter'] ?? $linked_entity['type'];
	                    $object_properties['object_assertions'] = rdf_entities_converter_controller::convert($row[0], $type, $object, $this->depth, $this->store);
	                    $object_properties['sub_uri'] = $object;
	                }
	                $this->assertions[] = new onto_assertion($subject, $property, $row[0], $object_type, $object_properties);
	            }
	        }
	    }
	}

	protected function get_assertions_from_special_fields() {
	    $this->init_special_fields();
	    if (!count($this->special_fields) || !$this->entity_id) {
	        return null;
	    }

	    if (empty($this->uri)) {
	        $this->init_uri();
	    }
	    $subject = $this->uri;

	    if (!isset($this->assertions)) {
	        $this->assertions = array();
	    }
	    foreach ($this->special_fields as $property => $callable) {
	        $assertion = call_user_func_array($callable["method"], $callable["arguments"]);
	        if (is_object($assertion)) {
	            $this->assertions[] = $assertion;
	        }
	    }
	}

	protected function get_object_type_from_type($type) {
	    switch ($type) {
	        case 'concept' :
	            return 'http://www.w3.org/2004/02/skos/core#Concept';
	            break;
	        default :
	            return $this->prefix_rdf."#".$type;
	            break;
	    }
	}

	protected function get_object_properties($id, $type) {
	    $properties = array('type' => 'uri');
	    $display_label = static::get_entity_isbd($id, $type);
	    if ($display_label) {
	        $properties['display_label'] = html_entity_decode($display_label);
	    }
	    return $properties;
	}

	public static function get_entity_isbd($id, $type){
	    //on récupère le type de range en enlevant le préfixe propre à l'ontologie
	    switch ($type) {
	        //case 'linked_record' :
	        case 'record' :
	            /** Tempo, code brut issu de select.php **/
	            $mono_display = new mono_display($id, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
	            return $mono_display->header_texte;
	        case 'author' :
	        //case 'responsability' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $id);
	            return $authority_instance->get_header();
	        case 'category' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_CATEG, $id);
	            return $authority_instance->get_header();
	        case 'publisher' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $id);
	            return $authority_instance->get_header();
	        case 'collection' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $id);
	            return $authority_instance->get_header();
	        case 'sub_collection' :
	        case 'subcollection' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_SUB_COLLECTIONS, $id);
	            return $authority_instance->get_header();
	        case 'serie' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_SERIES, $id);
	            return $authority_instance->get_header();
	        case 'work' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $id);
	            return $authority_instance->get_header();
	        case 'indexint' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_INDEXINT, $id);
	            return $authority_instance->get_header();
	        case 'docnum' :
	            return explnum::get_explnum_name($id);
	        case 'concept' :
	            //TODO A reprendre, on ne devrait pas avoir un coup l'id, un coup l'URI
	            if(is_numeric($id)){
	                $authority_instance = authorities_collection::get_authority(AUT_TABLE_CONCEPT, $id);
	            } else {
	                $authority_instance = authorities_collection::get_authority(AUT_TABLE_CONCEPT, onto_common_uri::get_id($id));
	            }
	            return $authority_instance->get_header();
	        case 'expl':
	            $exemplaire = new exemplaire($id);
	            return $exemplaire->get_notice_title();
	        default :

	            if (strpos($type, 'article') !== false) {
	                $cms_article = new cms_article($id);
	                return $cms_article->title;
	            }

	            if (strpos($type, 'section') !== false) {
	                $cms_section = new cms_section($id);
	                return $cms_section->title;
	             }

	            if (strpos($type, "authperso") !== false) {
	                return authperso::get_isbd($id);
	            }
	            return "";
	    }
	}

	public static function get_entity($id, $type){
	    //on récupère le type de range en enlevant le préfixe propre à l'ontologie
	    switch ($type) {
	        //case 'linked_record' :
	        case 'record' :
	            /** Tempo, code brut issu de select.php **/
	            $mono_display = new notice_affichage($id);
	            $mono_display->do_header_without_html();
	            return $mono_display;
	        case 'author' :
	        //case 'responsability' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $id);
	            return $authority_instance;
	        case 'category' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_CATEG, $id);
	            return $authority_instance;
	        case 'publisher' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $id);
	            return $authority_instance;
	        case 'collection' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $id);
	            return $authority_instance;
	        case 'sub_collection' :
	        case 'subcollection' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_SUB_COLLECTIONS, $id);
	            return $authority_instance;
	        case 'serie' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_SERIES, $id);
	            return $authority_instance;
	        case 'work' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $id);
	            return $authority_instance;
	        case 'indexint' :
	            $authority_instance = authorities_collection::get_authority(AUT_TABLE_INDEXINT, $id);
	            return $authority_instance;
	        case 'docnum' :
	            $authority_instance = new explnum($id);
	            return $authority_instance;
	        case 'concept' :
	            //TODO A reprendre, on ne devrait pas avoir un coup l'id, un coup l'URI
	            if(is_numeric($id)){
	                $authority_instance = authorities_collection::get_authority(AUT_TABLE_CONCEPT, $id);
	            } else {
	                $authority_instance = authorities_collection::get_authority(AUT_TABLE_CONCEPT, onto_common_uri::get_id($id));
	            }
	            return $authority_instance;
	        default :
	            return "";
	    }
	}

	/**
	 * Retourne le tye d'entité en fonction du du type de l'object dans l'audit
	 *
	 * @param int $object_type
	 * @return string
	 */
	static public function get_entity_type_from_object_type_audit(int $object_type)
	{
	    switch($object_type) {
	        case AUDIT_NOTICE:
	            return 'record';
	        case AUDIT_AUTHOR:
	            return 'author';
	        case AUDIT_COLLECTION:
	            return 'collection';
	        case AUDIT_CATEG:
	            return 'category';
	        case AUDIT_TITRE_UNIFORME:
	            return 'work';
	        case AUDIT_EDITORIAL_SECTION:
	            return 'section';
	        case AUDIT_SERIE:
	            return 'serie';
	        case AUDIT_COLLECTION:
	            return 'collection';
	        case AUDIT_SUB_COLLECTION:
	            return 'subcollection';
	        case AUDIT_EXPL:
	            return 'expl';
	        case AUDIT_EXPLNUM:
	            return 'docnum';
	        case AUDIT_EDITORIAL_ARTICLE:
	            return 'article';
	        case AUDIT_INDEXINT:
	            return 'indexint';
	        case AUDIT_PUBLISHER:
	            return 'publisher';
	        case AUDIT_CONCEPT:
	           return 'concept';
	        default:
	            if (!empty($object_type)) {
    	            return 'authority';
	            }
	            return '';
	    }
	}

	public static function get_assertion_with_predicate_from_assertions($predicate, $assertions) {

	    foreach ($assertions as $properties){
	        if ($predicate == $properties->get_predicate()) {
	            return $properties;
	        }
	    }
	    return '';
	}

	protected function init_uri(){
	    $this->uri = $this->get_uri_from_pmb_identifer($this->entity_id, $this->entity_type);
	}

	public function set_store($store) {
	    $this->store = $store;
	}

	protected function get_uri_from_pmb_identifer($id, $type) {
	    global $opac_url_base;
	    if ($this->store) {
	        $this->store->close();
	        $this->store->connect();
    	    $query = "	select ?uri where {
    						?uri <http://www.pmbservices.fr/ontology#identifier> '".addslashes($id)."' .
    						?uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <".addslashes($this->prefix_rdf."#".$type).">
    					}";
    	    $this->store->query($query);
    	    if ($this->store->num_rows()) {
    	        return $this->store->get_result()[0]->uri;
    	    } else {
//     	        var_dump($query);
    	    }
	    }
	    return  onto_common_uri::get_new_uri($opac_url_base.$type.'#');
	}

	public function get_uri() {
	    return $this->uri;
	}
}