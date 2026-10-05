<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_range_selector.class.php,v 1.2 2022/11/17 15:20:53 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';

/**
 * class onto_common_datatype_resource_selector
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_ontopmb_datatype_range_selector  extends onto_common_datatype {

	/** Aggregations: */
	
	/** Compositions: */
	
	/*** Attributes: ***/
	
	/**
	 *
	 * @access public
	*/
    public static $ranges = [
        'http://www.w3.org/2000/01/rdf-schema#Literal' => 'onto_ontopmb_datatype_range_selector_literal',
        'http://www.pmbservices.fr/ontology#record' => '288',
        'http://www.pmbservices.fr/ontology#author' => '234',
        'http://www.pmbservices.fr/ontology#category' => 'isbd_categories',
        'http://www.pmbservices.fr/ontology#publisher' => 'isbd_editeur',
        'http://www.pmbservices.fr/ontology#collection' => 'isbd_collection',
        'http://www.pmbservices.fr/ontology#sub_collection' => 'isbd_subcollection',
        'http://www.pmbservices.fr/ontology#serie' => 'isbd_serie',
        'http://www.pmbservices.fr/ontology#work' => 'isbd_titre_uniforme',
        'http://www.pmbservices.fr/ontology#indexint' => 'isbd_indexint',
        'http://www.w3.org/2004/02/skos/core#Concept' => 'concept_menu',
        'http://www.pmbservices.fr/ontology#marclist' => 'parperso_marclist',
    ];
    
	
	public function check_value(){
		if (is_string($this->value)) return true;
		return false;
	}
	
	public function get_value(){
		return $this->value;
	}
	
	public function get_formated_value(){
		return $this->value;
	}

} // end of onto_common_datatype_resource_selector
