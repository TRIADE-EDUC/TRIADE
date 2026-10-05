<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_subcollection.class.php,v 1.3 2022/05/25 08:24:25 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_authority.class.php');

class rdf_entities_converter_subcollection extends rdf_entities_converter_authority {
	
	protected $table_name = 'sub_collections';
	
	protected $table_key = 'sub_coll_id';
	
	protected $ppersos_prefix = 'subcollection';
	
	protected $type_constant = TYPE_SUBCOLLECTION;
	
	protected $aut_table_constant = AUT_TABLE_SUB_COLLECTIONS;
	
	protected function init_map_fields() {
	    $this->map_fields = array_merge(parent::init_map_fields(), array(
	        'subcollection_web' => 'http://www.pmbservices.fr/ontology#website',
	        'sub_coll_name' => 'http://www.pmbservices.fr/ontology#label',
	        'sub_coll_issn' => 'http://www.pmbservices.fr/ontology#issn',
	        'subcollection_comment' => 'http://www.pmbservices.fr/ontology#comment',
	    ));
	    return $this->map_fields;
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

	protected function init_foreign_fields() {
	    $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
	        'sub_coll_parent' => array(
	            'type' => 'subcollection',
	            'property' => 'http://www.pmbservices.fr/ontology#has_collection'
	        )
	    ));
	    return $this->foreign_fields;
	}
}