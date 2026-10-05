<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_pmbdatatype_selector.class.php,v 1.5 2020/12/01 10:31:07 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';




/**
 * class onto_common_datatype_resource_selector
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_ontopmb_datatype_pmbdatatype_selector  extends onto_common_datatype {
    
	public static $options = array(
		'http://www.pmbservices.fr/ontology#small_text' 			=> "onto_onto_pmb_datatype_pmb_datatype_small_text",
		'http://www.pmbservices.fr/ontology#text'  					=> "onto_onto_pmb_datatype_pmb_datatype_text",
		'http://www.pmbservices.fr/ontology#date' 					=> "onto_onto_pmb_datatype_pmb_datatype_date",
		'http://www.pmbservices.fr/ontology#resource_selector' 		=> "onto_onto_pmb_datatype_pmb_datatype_resource_selector",
		'http://www.pmbservices.fr/ontology#small_text_card'		=> "onto_onto_pmb_datatype_pmb_datatype_small_text_card",
		'http://www.pmbservices.fr/ontology#url'					=> "parperso_url",
		'http://www.pmbservices.fr/ontology#resource_pmb_selector'	=> "onto_onto_pmb_datatype_resource_pmb_selector",
		'http://www.pmbservices.fr/ontology#marclist'				=> "onto_onto_pmb_datatype_marclist",
		'http://www.pmbservices.fr/ontology#file' 					=> "onto_onto_pmb_datatype_file",
		'http://www.pmbservices.fr/ontology#small_text_link' 		=> "onto_onto_pmb_datatype_pmb_datatype_small_text_link",
	);
	
	/** Aggregations: */

	/** Compositions: */

    /*** Attributes: ***/
	
	/**
	 *
	 * @access public
	 */
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
