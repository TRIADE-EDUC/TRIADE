<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_authority_selector.class.php,v 1.1 2018-09-24 13:39:22 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';

class onto_contribution_datatype_linked_authority_selector  extends onto_common_datatype {

	public function check_value(){
		if (is_string($this->value)) return true;
		return false;
	}
	
	public function get_value(){
		return $this->value;
	} 
	
	public function get_formated_value(){
	    if (isset($formated_value)) {
	        return $formated_value;
	    }
	    $formated_value = $this->value;
	    $assertions = $this->offsetget_value_property("assertions");
	    if (is_array($assertions)) {
	        $formated_value = array();
	        /* @var $assertion onto_assertion */
	        foreach ($assertions as $assertion) {
	            switch ($assertion->get_predicate()) {
	                case 'http://www.pmbservices.fr/ontology#relation_type_authority' :
	                    $formated_value['relation_type_authority'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#has_authority' :
	                    $formated_value['authority'] = array(
	                    'value' => $assertion->get_object(),
	                    'display_label' => $assertion->offset_get_object_property('display_label')
	                    );
	                    break;
	            }
	        }
	    }
		return $formated_value;
	}
	
	public function get_value_type() {
	    return 'http://www.pmbservices.fr/ontology#authority';
	}
 
} // end of onto_common_datatype_resource_selector
