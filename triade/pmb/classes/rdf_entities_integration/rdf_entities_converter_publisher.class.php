<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_publisher.class.php,v 1.1 2018-09-24 13:39:22 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_authority.class.php');
require_once($class_path.'/publisher.class.php');

class rdf_entities_converter_publisher extends rdf_entities_converter_authority {
	
	protected $table_name = 'publishers';
	
	protected $table_key = 'ed_id';
	
	protected $ppersos_prefix = 'publisher';
	
	protected $type_constant = TYPE_PUBLISHER;
	
	protected $aut_table_constant = AUT_TABLE_PUBLISHERS;
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
    		    'ed_name' => 'http://www.pmbservices.fr/ontology#publisher_name',
    		    'ed_adr1' => 'http://www.pmbservices.fr/ontology#address_1',
    		    'ed_adr2' => 'http://www.pmbservices.fr/ontology#address_2',
    		    'ed_cp' => 'http://www.pmbservices.fr/ontology#zip_code',
    		    'ed_ville' => 'http://www.pmbservices.fr/ontology#town',
    		    'ed_pays' => 'http://www.pmbservices.fr/ontology#country',
                'ed_web' => 'http://www.pmbservices.fr/ontology#website',
                'ed_num_entite' => 'http://www.pmbservices.fr/ontology#has_supplier',
		));
		return $this->map_fields;
	}
	
// 	protected function init_foreign_fields() {
// 		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
//                 'ed_num_entite' => 'http://www.pmbservices.fr/ontology#has_supplier'
// 		));
// 		return $this->foreign_fields;
// 	}
}