<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_pmb_entities_mapping.class.php,v 1.8 2018/03/21 15:53:19 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/onto_pmb_entities_store.class.php");
require_once($class_path."/onto/onto_ontology.class.php");

/**
 * classe qui gere le mappage entre les entites, les liens de l'ontology et les entites relationnelles de pmb
 */
class onto_pmb_entities_mapping { 
	
	protected static $ontology;
	
	protected static $rdf_dom_mapping_links;
	protected static $rdf_dom_mapping_link_types;
	
	protected static $rdf_linked_entities_mapping;
	protected static $rdf_relational_mapping_link_types;
	
	public function __construct() {
		
	}
	
	protected static function get_rdf_type_from_relational_type($type) {
		switch($type) {
			case 'notice' :
			case 'notices' :
			case 'records' :
				return 'record';
			case 'titre_uniforme' :
			case 'titre_uniformes' :
			case 'uniform_title' :
			case 'works' :
				return 'work';
			case 'auteur' :
			case 'auteurs' :
			case 'author' :
				return 'author';
			default :
				return $type;
		}
	}
	
	/**
	 *	DOM MAPPING 
	 */
	protected static function init_rdf_dom_mapping_links() {
		//TODO : revoir le nom des cles (titre_uniforme,work, notice, record...) 
		self::$rdf_dom_mapping_links = array(
			'notice' => array(
				'tparent' => 'f_tparent',
				'has_main_author' => 'f_aut0',
				'has_other_author' => 'f_aut10',
				'has_secondary_author' => 'f_aut20',
				'has_publisher' => 'f_ed1',
				'has_other_publisher' => 'f_ed2',
				'has_collection' => 'f_coll',
				'has_subcollection' => 'f_subcoll',
				'has_concept' => 'concept_label',
				'has_category' => 'f_categ',
				'has_indexint' => 'f_indexint',
				'has_work' => 'f_titre_uniforme',
				'has_linked_record' => 'f_rel_',
			),
			'auteur' => array(
				'author_see' => 'voir_libelle',
				'has_concept' => 'concept_label',
			),
			'category' => array(
				'has_concept' => 'concept_label',
				'parent_category' => 'category_parent',
				'category_see' => 'category_voir',
				'category_see_also' => 'f_categ0',
			),
			'editeur' => array(
				'has_supplier' => 'lib_fou',
				'has_concept' => 'concept_label',
			),
			'collection' => array(
				'has_publisher' => 'ed_libelle',
				'has_concept' => 'concept_label',
			),
			'subcollection' => array(
				'has_publisher' => 'ed_libelle',
				'has_collection' => 'coll_libelle',
				'has_concept' => 'concept_label',
			),
			'serie' => array(
				'has_concept' => 'concept_label',
			),
			'titre_uniforme' => array(
				'has_concept' => 'concept_label',
				'has_record' => 'f_tu_notices',
				'expression_of' => 'f_oeuvre_expression',
				'has_expression' => 'f_oeuvre_expression_from',
				'has_other_link' => 'f_other_link',
				'has_event' => 'f_oeuvre_event',
				'has_responsability_author' => 'f_aut0',
				'has_responsability_performer' => 'f_aut1'
			),
			'indexint' => array(
				'has_concept' => 'concept_label',
			),
			'concept' => array(
				'has_concept' => 'concept_label',
			),
			'authperso' => array()
		);
		return self::$rdf_dom_mapping_links;
	}
	
	protected static function init_rdf_dom_mapping_link_types() {
		self::$rdf_dom_mapping_link_types = array(
			'notice' => array(
				'has_main_author' => 'f_f0',
				'has_other_author' => 'f_f1',
				'has_secondary_author' => 'f_f2',
				'has_linked_record' => 'f_rel_type_0'
			),
			'titre_uniforme' => array(
				'expression_of' => 'f_oeuvre_expression_type0',
				'has_expression' => 'f_oeuvre_expression_from_type0',
				'has_other_link' => 'f_oeuvre_other_link0',
				'has_responsability_author' => 'f_f0',
				'has_responsability_performer' => 'f_f1'
			),
		);
		return self::$rdf_dom_mapping_link_types;
	}
	
	public static function get_rdf_dom_mapping_links () {
		if (!isset(self::$rdf_dom_mapping_links)) {
			self::init_rdf_dom_mapping_links();
		}
		return self::$rdf_dom_mapping_links;
	}
	
	public static function get_entity_rdf_dom_mapping_links ($entity) {
		if (!isset(self::$rdf_dom_mapping_links)) {
			self::init_rdf_dom_mapping_links();
		}
		if (isset(self::$rdf_dom_mapping_links[$entity])) {
			return self::$rdf_dom_mapping_links[$entity];
		}
		return array();
	}
	
	public static function get_rdf_dom_mapping_link_types () {
		if (!isset(self::$rdf_dom_mapping_link_types)) {
			self::init_rdf_dom_mapping_link_types();
		}
		return self::$rdf_dom_mapping_link_types;
	}
	
	public static function get_entity_rdf_dom_mapping_link_types ($entity) {
		if (!isset(self::$rdf_dom_mapping_link_types)) {
			self::init_rdf_dom_mapping_link_types();
		}
		if (isset(self::$rdf_dom_mapping_link_types[$entity])) {
			return self::$rdf_dom_mapping_link_types[$entity];
		}
		return array();
	}
	
	protected static function get_parameters_entities_link($element_type, $type, $property='', $arguments=array()) {
		return array(
				'element_type' => $element_type,
				'type' => $type,
				'property' => $property,
				'arguments' => $arguments,			
		);
	}
	
	/**
	 * LINKED ENTITIES
	 */
	protected static function init_rdf_linked_entities_mapping() {
		self::$rdf_linked_entities_mapping = array(
				'record' => array(
						'tparent' => static::get_parameters_entities_link('authorities',TYPE_SERIE),
						'has_main_author' => static::get_parameters_entities_link('authorities',TYPE_AUTHOR, 'get_linked_authors_id', array(0)),
						'has_other_author' => static::get_parameters_entities_link('authorities',TYPE_AUTHOR, 'get_linked_authors_id', array(1)),
						'has_secondary_author' => static::get_parameters_entities_link('authorities',TYPE_AUTHOR, 'get_linked_authors_id', array(2)),
						'has_publisher' => static::get_parameters_entities_link('authorities',TYPE_PUBLISHER, 'ed1_id'),
						'has_other_publisher' => static::get_parameters_entities_link('authorities',TYPE_PUBLISHER, 'ed2_id'),
						'has_collection' => static::get_parameters_entities_link('authorities',TYPE_COLLECTION, 'coll_id'),
						'has_subcollection' => static::get_parameters_entities_link('authorities',TYPE_SUBCOLLECTION, 'subcoll_id'),
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT, 'get_linked_concepts_id'),
						'has_category' => static::get_parameters_entities_link('authorities',TYPE_CATEGORY, 'get_linked_categories_id'),
						'has_indexint' => static::get_parameters_entities_link('authorities',TYPE_INDEXINT, 'indexint'),
						'has_work' => static::get_parameters_entities_link('authorities',TYPE_TITRE_UNIFORME, 'get_linked_works_id'),
						'has_linked_record' => static::get_parameters_entities_link('records',TYPE_NOTICE),
				),
				'author' => array(
						'author_see' => static::get_parameters_entities_link('authorities',TYPE_AUTHOR, 'see'),
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT),
				),
				'category' => array(
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT),
						'parent_category' => static::get_parameters_entities_link('authorities',TYPE_CATEGORY),
						'category_see' => static::get_parameters_entities_link('authorities',TYPE_CATEGORY),
						'category_see_also' => static::get_parameters_entities_link('authorities',TYPE_CATEGORY),
				),
				'publisher' => array(
						'has_supplier' => static::get_parameters_entities_link('authorities',TYPE_PUBLISHER),
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT),
				),
				'collection' => array(
						'has_publisher' => static::get_parameters_entities_link('authorities',TYPE_PUBLISHER),
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT),
				),
				'subcollection' => array(
						'has_publisher' => static::get_parameters_entities_link('authorities',TYPE_PUBLISHER),
						'has_collection' => static::get_parameters_entities_link('authorities',TYPE_COLLECTION),
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT),
				),
				'serie' => array(
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT),
				),
				'work' => array(
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT),
						'has_record' => static::get_parameters_entities_link('records',TYPE_NOTICE, 'get_linked_records_id'),
						'expression_of' => static::get_parameters_entities_link('authorities',TYPE_TITRE_UNIFORME, 'get_linked_works_id', array('oeuvre_expressions_from')),
						'has_expression' => static::get_parameters_entities_link('authorities',TYPE_TITRE_UNIFORME, 'get_linked_works_id', array('oeuvre_expressions')),
						'has_other_link' => static::get_parameters_entities_link('authorities',TYPE_TITRE_UNIFORME, 'get_linked_works_id', array('other_links')),
						'has_event' => static::get_parameters_entities_link('authorities', TYPE_AUTHPERSO, 'get_linked_events_id'),
						'has_responsability_author' => static::get_parameters_entities_link('authorities',TYPE_AUTHOR,'get_linked_responsabilities_id', array('authors')),
						'has_responsability_performer' => static::get_parameters_entities_link('authorities',TYPE_AUTHOR,'get_linked_responsabilities_id', array('performers')),
				),
				'indexint' => array(
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT),
				),
				'concept' => array(
						'has_concept' => static::get_parameters_entities_link('authorities',TYPE_CONCEPT),
				),
				'authperso' => array()
		);
		
		//TODO : ajout des champs perso et des autorites perso
		$query = '
			select * where {
				?uri pmb:name ?name .
				?uri pmb:flag "auth_perso"
			}
		';
		onto_pmb_entities_store::query($query);
		if (onto_pmb_entities_store::num_rows()) {
			$results = onto_pmb_entities_store::get_result();
			foreach ($results as $result) {
				self::$rdf_linked_entities_mapping[$result->name] = array();
				
				$link_name = 'has_'.$result->name;
				$id_authperso = explode('_',$result->name)[1];
				
				self::$rdf_linked_entities_mapping['record'][$link_name] = static::get_parameters_entities_link('authorities',TYPE_AUTHPERSO, '', array($id_authperso));
				
// 				//ajout des champs perso
// 				$sub_query = '
// 					select * where {
// 						?prop pmb:name "has_'.$result->name.'" .
// 						?prop rdf:type <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property>
//						?prop pmb:datatype pmb:resource_selector
// 						?prop pmb:domain "'.$result->uri.'"
// 					}
// 				'; 

// 				onto_pmb_entities_store::query($sub_query);
// 				if (onto_pmb_entities_store::num_rows()) {
// 					$ub_results = onto_pmb_entities_store::get_result();
// 					foreach ($ub_results as $ub_result) {
						
// 					}
// 				}
			}
		}		
		return self::$rdf_linked_entities_mapping;
	}
	
	public static function get_rdf_linked_entities_mapping () {
		if (!isset(self::$rdf_linked_entities_mapping)) {
			self::init_rdf_linked_entities_mapping();
		}
		return self::$rdf_linked_entities_mapping;
	}
	
	public static function get_entity_rdf_linked_entities_mapping ($entity, $link = '') {
		if (!isset(self::$rdf_linked_entities_mapping)) {
			self::init_rdf_linked_entities_mapping();
		}
		$entity = self::get_rdf_type_from_relational_type($entity);
		if ($link && isset(self::$rdf_linked_entities_mapping[$entity][$link])) {
			return self::$rdf_linked_entities_mapping[$entity][$link];
		}
		if (isset(self::$rdf_linked_entities_mapping[$entity])) {
			return self::$rdf_linked_entities_mapping[$entity];
		}
		return array();
	}

	protected static function init_ontology() {
		if (!isset(self::$ontology)) {
			self::$ontology = new onto_ontology(onto_pmb_entities_store::get_store());
		}
		return self::$ontology;
	}
	
	public static function get_max_restriction_from_property($property, $class) {
		if (!isset(self::$ontology)) {
			self::init_ontology();
		}
		$restriction = self::$ontology->get_restriction('http://www.pmbservices.fr/ontology#'.$class, 'http://www.pmbservices.fr/ontology#'.$property);
		return $restriction->get_max();
	}
	
	public static function get_links_from_entity($entity_type, $entity_id) {		
		$entities_data = array();
		switch ($entity_type) {
			case 'records' :
				$entity = new record_datas($entity_id);
				$entity_type = 'record';
				break;
			case 'authorities' :
				$entity = new authority($entity_id);
				$entity_type = $entity->get_string_type_object();
				break;
			
		}
		
		//on recupere le type dans l'ontologie
		$entity_type = self::get_rdf_type_from_relational_type($entity_type);
		$links = static::get_entity_rdf_linked_entities_mapping($entity_type);
		foreach ($links as $link_name => $link_property) {
			$entities_data[$link_name] = array(
					'link_type' => $link_property['element_type'],
					'linked_entities' => $entity->get_linked_entities_id($link_property['type'], $link_property['property'], $link_property['arguments'])
			);
		}
		return $entities_data;
	}
	
	public static function get_link_from_entity($entity_type, $entity_id, $link_name) {		
		$entities_data = array();
		
		if($entity_type != 'record'){
			$entity = new authority($entity_id);
		}else{
			$entity = new record_datas($entity_id);
		}
		//on recupere le type dans l'ontologie
		$entity_type = self::get_rdf_type_from_relational_type($entity_type);
		$link = static::get_entity_rdf_linked_entities_mapping($entity_type, $link_name);
		
		$entities_data[$link_name] = array(
				'link_type' => $link['element_type'],
				'linked_entities' => $entity->get_linked_entities_id($link['type'], $link['property'], $link['arguments']),
				'link_max_restriction' => self::get_max_restriction_from_property($link_name, $entity_type)
		);
		return $entities_data;
	}
	
	protected static function init_rdf_relational_mapping_link_types() {
		self::$rdf_relational_mapping_link_types = array(
				'record' => array(
						'author_funtion' => 'responsability_fonction',
						'relation_type' => 'relation_type',
				),
				'work' => array(
						'relation_type_work' => 'oeuvre_link_type',
						'author_funtion' => 'responsability_tu_fonction',
				),
		);
		return self::$rdf_relational_mapping_link_types;
	}
	
	public static function get_rdf_relational_mapping_link_types () {
		if (!isset(self::$rdf_relational_mapping_link_types)) {
			self::init_rdf_relational_mapping_link_types();
		}
		return self::$rdf_relational_mapping_link_types;
	}
	
	public static function get_entity_rdf_relational_mapping_link_types ($entity) {
		if (!isset(self::$rdf_relational_mapping_link_types)) {
			self::init_rdf_relational_mapping_link_types();
		}
		$entity = self::get_rdf_type_from_relational_type($entity);
		if (isset(self::$rdf_relational_mapping_link_types[$entity])) {
			return self::$rdf_relational_mapping_link_types[$entity];
		}
		return array();
	}
}