<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_responsability_selector.class.php,v 1.3 2018-12-28 16:27:30 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_resource_selector
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_contribution_datatype_responsability_selector  extends onto_common_datatype {

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
	    if (isset($this->formated_value)) {
	        return $this->formated_value;
	    }
	    $this->formated_value = $this->value;
	    $assertions = $this->offsetget_value_property("assertions");
	    if (is_array($assertions)) {
	        $this->formated_value = array();
	        /* @var $assertion onto_assertion */
	        foreach ($assertions as $assertion) {
	            switch ($assertion->get_predicate()) {
	                case 'http://www.pmbservices.fr/ontology#author_function' :
	                    $this->formated_value['author_function'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#has_author' :
	                    $this->formated_value['author'] = array(
                                'value' => $assertion->get_object(),
                                'display_label' => $assertion->offset_get_object_property('display_label')
	                    );
	                    break;
	            }
	        }
	    }
		return $this->formated_value;
	}
	
	public function get_value_type() {
	    return 'http://www.pmbservices.fr/ontology#responsability';
	}
 
} // end of onto_common_datatype_resource_selector