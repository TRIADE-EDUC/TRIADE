<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_authority_selector.class.php,v 1.3 2021/09/03 08:16:22 gneveu Exp $

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
	    if (isset($this->formated_value)) {
	        return $this->formated_value;
	    }
	    
	    $this->formated_value = [
	        "authority" => [
	            'value' => $this->get_raw_value(),
	            'display_label' => $this->offsetget_value_property('display_label') ?? "",
	        ]
	    ];
	    $assertions = $this->offsetget_value_property("assertions");
	    if (is_array($assertions)) {
	        /* @var $assertion onto_assertion */
	        foreach ($assertions as $assertion) {
	            switch ($assertion->get_predicate()) {
	                case 'http://www.pmbservices.fr/ontology#relation_type_authority' :
	                    $this->formated_value['relation_type_authority'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#authority_type' :
	                    $this->formated_value['authority_type'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#comment' :
	                    $this->formated_value['comment'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#start_date' :
	                    $this->formated_value['start_date'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#end_date' :
	                    $this->formated_value['end_date'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#has_authority' :
	                    $properties = static::get_properties_from_uri($assertion->get_object());
	                    if (empty($properties["http://www.pmbservices.fr/ontology#is_draft"])) $properties["http://www.pmbservices.fr/ontology#is_draft"] = false;
	                    
	                    $this->formated_value['authority'] = array(
	                        'value' => $assertion->get_object(),
	                        'display_label' => $assertion->offset_get_object_property('display_label'),
	                        'area_id' => $properties["http://www.pmbservices.fr/ontology#area"] ?? 0,
	                        'form_uri' => $properties["http://www.pmbservices.fr/ontology#form_uri"] ?? "",
	                        'form_id' => $properties["http://www.pmbservices.fr/ontology#form_id"] ?? 0,
	                        'is_draft' => (!empty($properties["http://www.pmbservices.fr/ontology#is_draft"]) ? "1" : "0")
	                    );
	                    break;
	            }
	        }
	    }
	    return $this->formated_value;
	}
	
	public function get_value_type() {
	    return 'http://www.pmbservices.fr/ontology#linked_authority';
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
	                
	                $linked_authority = onto_common_uri::get_new_uri($opac_url_base."linked_authority#");
	                $data_properties["object_assertions"] = array(
	                    new onto_assertion($linked_authority, 'http://www.pmbservices.fr/ontology#has_authority', $data["value"], "http://www.pmbservices.fr/ontology#authority", array('type'=>"uri", "display_label" => $data_properties["display_label"])),
	                    new onto_assertion($linked_authority, 'http://www.pmbservices.fr/ontology#relation_type_authority', $data["relation_type_authority"], "", array('type'=>"literal")),
	                    new onto_assertion($linked_authority, 'http://www.pmbservices.fr/ontology#authority_type', $data["authority_type"], "", array('type'=>"literal")),
	                    new onto_assertion($linked_authority, 'http://www.pmbservices.fr/ontology#comment', $data["comment"], "", array('type'=>"literal")),
	                    new onto_assertion($linked_authority, 'http://www.pmbservices.fr/ontology#start_date', $data["start_date"], "", array('type'=>"literal")),
	                    new onto_assertion($linked_authority, 'http://www.pmbservices.fr/ontology#end_date', $data["end_date"], "", array('type'=>"literal")),
	                );
	                $class_name = static::class;
	                //$datatypes[$property->uri][] = new $class_name($responsablity_uri, $data["type"], $data_properties);
	                $datatypes[$property->uri][] = new $class_name($linked_authority, 'http://www.pmbservices.fr/ontology#linked_authority', $data_properties);
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
