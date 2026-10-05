<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_small_text.class.php,v 1.3 2020/09/07 11:59:44 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_small_text
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_common_datatype_small_text extends onto_common_datatype {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	
	public function check_value(){
		if (is_string($this->value) && (strlen($this->value) < 512)) return true;
		return false;
	}
	
	public function get_management_data() {
	    if (empty($this->value)) {
	        return [];
	    }
	    $properties = static::get_properties_from_uri($this->value);
	    return [
	        'value' => $this->value,
	        'display_label' => $properties["http://www.pmbservices.fr/ontology#display_label"],
	        'area_id' => $properties["http://www.pmbservices.fr/ontology#area"] ?? 0,
	        'form_uri' => $properties["http://www.pmbservices.fr/ontology#form_uri"] ?? "",
	        'form_id' => $properties["http://www.pmbservices.fr/ontology#form_id"] ?? 0,
	        'is_draft' => ($properties["http://www.pmbservices.fr/ontology#is_draft"] == true ? "1" : "0"),
	    ];
	}
	
	public static function get_properties_from_uri($uri) {
	    $contribution_area_store = new contribution_area_store();
	    return $contribution_area_store->get_properties_from_uri($uri);
	}
	
} // end of onto_common_datatype_small_text
