<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_collection.class.php,v 1.3 2020/11/26 13:29:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_authority.class.php');

class rdf_entities_converter_collection extends rdf_entities_converter_authority {
	
	protected $table_name = 'collections';
	
	protected $table_key = 'collection_id';
	
	protected $ppersos_prefix = 'collection';
	
	protected $type_constant = TYPE_COLLECTION;
	
	protected $aut_table_constant = AUT_TABLE_COLLECTIONS;
	
	protected function init_map_fields() 
	{
	    $this->map_fields = array_merge(parent::init_map_fields(), array(
            'collection_web' => 'http://www.pmbservices.fr/ontology#website',
	        'collection_name' => 'http://www.pmbservices.fr/ontology#label',
	        'collection_issn' => 'http://www.pmbservices.fr/ontology#issn'
	    ));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() 
	{
	    $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
	        'collection_parent' => array(
	            'type' => 'publisher',
	            'property' => 'http://www.pmbservices.fr/ontology#has_publisher'
	        )
	    ));
		return $this->foreign_fields;
	}
	
	protected function init_special_fields() {
	    $this->special_fields = array_merge(parent::init_special_fields(), array(
	        'http://www.pmbservices.fr/ontology#thumbnail_url' => array(
	            "method" => array($this,"get_thumbnail_url"),
	            "arguments" => array($this->aut_table_constant)
	        )
	    ));
	    return $this->special_fields;
	}
	
	protected function init_linked_entities() 
	{
	    $this->linked_entities = array_merge(parent::init_linked_entities(), array(
	        'http://www.pmbservices.fr/ontology#has_concept' => array(
	            'table' => 'index_concept',
	            'reference_field_name' => 'num_object',
	            'external_field_name' => 'num_concept',
	            'other_fields' => array(
	                'type_object' => TYPE_COLLECTION
	            )
	        )
	    ));
	    return $this->linked_entities;
	}
}