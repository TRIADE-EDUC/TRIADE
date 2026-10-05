<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_resource_selector.class.php,v 1.6.8.1 2024/06/25 09:46:20 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_resource_selector.class.php';



class onto_contribution_datatype_resource_selector extends onto_common_datatype_resource_selector {

    public static function get_properties_from_uri($uri) {
        $contribution_area_store = new contribution_area_store();
        return $contribution_area_store->get_properties_from_uri($uri);
    }
    
    
    public function get_management_data() {
        if (empty($this->value)) {
            return [];
        }
        
        $properties = static::get_properties_from_uri($this->value);
        if (empty($properties["http://www.pmbservices.fr/ontology#is_draft"])) $properties["http://www.pmbservices.fr/ontology#is_draft"] = false;
        
        return [
            'value' => $this->value,
            'display_label' => !empty($properties["http://www.pmbservices.fr/ontology#display_label"]) ? $properties["http://www.pmbservices.fr/ontology#display_label"] : "",
            'area_id' => !empty($properties["http://www.pmbservices.fr/ontology#area"]) ? $properties["http://www.pmbservices.fr/ontology#area"] : 0,
            'form_uri' => !empty($properties["http://www.pmbservices.fr/ontology#form_uri"]) ? $properties["http://www.pmbservices.fr/ontology#form_uri"] : "",
            'form_id' => !empty($properties["http://www.pmbservices.fr/ontology#form_id"]) ? $properties["http://www.pmbservices.fr/ontology#form_id"] : 0,
            'is_draft' => ($properties["http://www.pmbservices.fr/ontology#is_draft"] == true ? "1" : "0"),
        ];
    }
    /**
     * Encode les assertions en JSON.
     *
     * @return string Les assertions encodées en JSON, ou une chaîne vide si le tableau des assertions est vide.
     */
    public function json_encode_assertions()
    {
        $strlen = strlen("http://www.pmbservices.fr/ontology#");
        $assertions = [];
        $assertionsList = $this->offsetget_value_property("assertions");
        if (is_array($assertionsList)) {
	        /* @var $assertion onto_assertion */
	        foreach ($assertionsList as $assertion) {
                $property = substr($assertion->get_predicate(), $strlen);
                $assertions[$property] = $assertion->get_object();
            }
        }
        return !empty($assertions) ? encoding_normalize::json_encode($assertions) : "";
    }
}