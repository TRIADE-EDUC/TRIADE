<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_record_selector.class.php,v 1.3.8.1 2024/06/25 09:46:20 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_resource_selector
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_contribution_datatype_linked_record_selector  extends onto_contribution_datatype_resource_selector {

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
	    $this->formated_value = [
	        "record" => [
	            'value' => $this->get_raw_value(),
	            'display_label' => $this->offsetget_value_property('display_label') ?? "",
	        ]
	    ];
	    
	    $assertions = $this->offsetget_value_property("assertions");
	    if (is_array($assertions)) {
	        /* @var $assertion onto_assertion */
	        foreach ($assertions as $assertion) {
	            switch ($assertion->get_predicate()) {
	                case 'http://www.pmbservices.fr/ontology#relation_type' :
	                case 'relation_type' :
	                    $this->formated_value['relation_type'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#add_reverse_link' :
	                    $this->formated_value['add_reverse_link'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#has_record' :
	                case 'has_record' :
	                    $this->formated_value['record'] = array(
                                'value' => $assertion->get_object(),
    	                        'display_label' => $assertion->offset_get_object_property('display_label'),
    	                        'area_id' => $properties["http://www.pmbservices.fr/ontology#area"],
    	                        'form_uri' => $properties["http://www.pmbservices.fr/ontology#form_uri"] ?? "",
    	                        'form_id' => $properties["http://www.pmbservices.fr/ontology#form_id"] ?? 0,
                                'is_draft' => ($properties["http://www.pmbservices.fr/ontology#is_draft"] ? "1" : "0")
	                    );
	                    break;
	                case 'http://www.pmbservices.fr/ontology#direction' :
	                case 'direction' :
	                    $this->formated_value['direction'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#num_reverse_link' :
	                case 'num_reverse_link' :
	                    $this->formated_value['num_reverse_link'] = $assertion->get_object();
	                    break;
	            }
	        }
	    }
		return $this->formated_value;
	}
	
	public function get_value_type() {
	    return 'http://www.pmbservices.fr/ontology#linked_record';
	}
	
	public static function get_values_from_form($instance_name, $property, $uri_item) {
	    global $opac_url_base;
	    $datatypes = array();
	    $var_name = $instance_name."_".$property->pmb_name;
	    global ${$var_name};
	    if (${$var_name} && count(${$var_name})) {
	        foreach (${$var_name} as $order => $data) {
	            $data=stripslashes_array($data);
	            if ($data["value"] !== null) {
	                $data_properties = array();
	                if (!empty($data["lang"])) {
	                    $data_properties["lang"] = $data["lang"];
	                } else {
	                    $data_properties["lang"] = '';
	                }
	                if ($data["type"] == "http://www.w3.org/2000/01/rdf-schema#Literal") {
	                    $data_properties["type"] = "literal";
	                } else {
	                    $data_properties["type"] = "uri";
	                }
	                if ($data["display_label"]) {
	                    $data_properties["display_label"] = $data["display_label"];
	                }
					$data["relation_type"] = $data["assertions"]["relation_type"] ?? "";
					$data["add_reverse_link"] = intval(!empty($data["assertions"]["add_reverse_link"]));
					//cas particulier pour les modifications de contribution
					if (!empty($data["assertions"] && is_string($data["assertions"]))) {
						$data["assertions"] = json_decode($data["assertions"], true);
						if (!empty($data["assertions"]["relation_type"]) && !empty($data["assertions"]["direction"])) {
							$data["relation_type"] = $data["assertions"]["relation_type"].'-'.$data["assertions"]["direction"];
						}
					}
	                $responsablity_uri = onto_common_uri::get_new_uri($opac_url_base."linked_record#");
	                $data_properties["object_assertions"] = array(
	                    new onto_assertion($responsablity_uri, 'http://www.pmbservices.fr/ontology#has_record', $data["value"], "http://www.pmbservices.fr/ontology#record", array('type'=>"uri", "display_label" => $data_properties["display_label"])),
	                    new onto_assertion($responsablity_uri, 'http://www.pmbservices.fr/ontology#relation_type', $data["relation_type"], "", array('type'=>"literal")),
	                    new onto_assertion($responsablity_uri, 'http://www.pmbservices.fr/ontology#add_reverse_link', $data["add_reverse_link"], "", array('type'=>"literal"))
	                );
	                $class_name = static::class;
	                $datatypes[$property->uri][] = new $class_name($responsablity_uri, 'http://www.pmbservices.fr/ontology#linked_record', $data_properties);
	            }
	        }
	    }
	    return $datatypes;
	}
	
	public function get_raw_value() {
	    //si c'est un tableau, on retourne la première valeur dans le cas générale
	    if (is_array($this->value)) {
	        foreach ($this->value as $key => $value) {
	            return $value;
	        }
	    }
	    return $this->value;
	}
 
} // end of onto_common_datatype_resource_selector
