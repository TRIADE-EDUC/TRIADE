<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_category.class.php,v 1.3 2022/06/02 14:04:36 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_authority.class.php');

class rdf_entities_converter_category extends rdf_entities_converter_authority {
	
	protected $table_name = 'noeuds';
	
	protected $table_key = 'id_noeud';
	
	protected $ppersos_prefix = 'categ';
	
	protected $type_constant = TYPE_CATEGORY;
	
	protected $aut_table_constant = AUT_TABLE_CATEGORIES;
	
	protected function init_map_fields() 
	{
	    $this->map_fields = array_merge(parent::init_map_fields(), array(
	        'id_noeud' => 'http://www.pmbservices.fr/ontology#identifier',
	        'num_thesaurus' => 'http://www.pmbservices.fr/ontology#has_thesaurus',
	        'autorite' => 'http://www.pmbservices.fr/ontology#authority_number'
	    ));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() 
	{
	    $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
	        'num_parent' => array(
	            'type' => 'category',
	            'property' => 'http://www.pmbservices.fr/ontology#parent_category'
	        ),
	        'num_renvoi_voir' => array(
	            'type' => 'category',
	            'property' => 'http://www.pmbservices.fr/ontology#category_see'
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
	                'type_object' => TYPE_CATEGORY
	            )
	        ),
	        'http://www.pmbservices.fr/ontology#category_see_also' => array(
	            'table' => 'voir_aussi',
	            'reference_field_name' => 'num_noeud_orig',
	            'external_field_name' => 'num_noeud_dest',
	            'other_fields' => array(
	                'langue' => 'fr_FR'
	            )
	        )
	    ));
	    return $this->linked_entities;
	}
}