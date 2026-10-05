<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_author.class.php,v 1.1 2018/09/24 13:39:21 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_authority.class.php');
require_once($class_path.'/author.class.php');

class rdf_entities_converter_author extends rdf_entities_converter_authority {
	
	protected $table_name = 'authors';
	
	protected $table_key = 'author_id';
	
	protected $ppersos_prefix = 'author';
	
	protected $type_constant = TYPE_AUTHOR;
	
	protected $aut_table_constant = AUT_TABLE_AUTHORS;
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'author_date' => 'http://www.pmbservices.fr/ontology#date',
				'author_type' => 'http://www.pmbservices.fr/ontology#author_type',
				'author_name' => 'http://www.pmbservices.fr/ontology#author_name',
				'author_rejete' => 'http://www.pmbservices.fr/ontology#author_first_name',
				'author_lieu' => 'http://www.pmbservices.fr/ontology#place',
				'author_ville' => 'http://www.pmbservices.fr/ontology#town',
				'author_pays' => 'http://www.pmbservices.fr/ontology#country',
				'author_subdivision' => 'http://www.pmbservices.fr/ontology#subdivision',
				'author_numero' => 'http://www.pmbservices.fr/ontology#number',
				'author_web' => 'http://www.pmbservices.fr/ontology#website'
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
				'author_see' => array(
                    'type' => 'record',
                    'property' => 'http://www.pmbservices.fr/ontology#author_see'
				),
		));
		return $this->foreign_fields;
	}
	
// 	protected function init_linked_entities() {
// 		$this->linked_entities = array_merge(parent::init_linked_entities(), array(
// 				'http://www.pmbservices.fr/ontology#has_concept' => array(
// 				        'type' => 'concept',
// 						'table' => 'index_concept',
// 						'reference_field_name' => 'num_object',
// 						'external_field_name' => 'num_concept',
// 						'other_fields' => array(
// 						    'type_object' => $this->type_constant
// 						)
// 				)
// 		));
// 		return $this->linked_entities;
// 	}
}