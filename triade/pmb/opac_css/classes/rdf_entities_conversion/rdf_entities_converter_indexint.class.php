<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_indexint.class.php,v 1.3 2020/11/26 13:29:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_authority.class.php');

class rdf_entities_converter_indexint extends rdf_entities_converter_authority {
	
	protected $table_name = 'indexint';
	
	protected $table_key = 'indexint_id';
	
	protected $ppersos_prefix = 'indexint';
	
	protected $type_constant = TYPE_INDEXINT;
	
	protected $aut_table_constant = AUT_TABLE_INDEXINT;
	
	protected function init_map_fields() 
	{
	    $this->map_fields = array_merge(parent::init_map_fields(), array(
	        'indexint_name' => 'http://www.pmbservices.fr/ontology#label',
	        'indexint_comment' => 'http://www.pmbservices.fr/ontology#comment'
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() 
	{
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array());
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
	                'type_object' => TYPE_INDEXINT
	            )
	        )
	    ));
	    return $this->linked_entities;
	}
}