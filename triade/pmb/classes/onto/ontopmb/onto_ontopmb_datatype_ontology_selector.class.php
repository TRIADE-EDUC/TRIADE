<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_ontology_selector.class.php,v 1.1 2015/08/10 23:16:25 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';

/**
 * class onto_common_datatype_resource_selector
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_ontopmb_datatype_ontology_selector  extends onto_common_datatype {

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
