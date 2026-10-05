<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_serie.class.php,v 1.3 2022/06/02 14:04:35 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_authority.class.php');

class rdf_entities_converter_serie extends rdf_entities_converter_authority {
	
	protected $table_name = 'series';
	
	protected $table_key = 'serie_id';
	
	protected $ppersos_prefix = 'serie';
	
	protected $type_constant = TYPE_SERIE;
	
	protected $aut_table_constant = AUT_TABLE_SERIES;
	
	protected function init_map_fields() {
	    $this->map_fields = array_merge(parent::init_map_fields(), array(
	        'serie_id' => 'http://www.pmbservices.fr/ontology#identifier',
	        'serie_name' => 'http://www.pmbservices.fr/ontology#label',
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
	    $this->foreign_fields = array_merge(parent::init_foreign_fields(), array());
	    return $this->foreign_fields;
	}
}