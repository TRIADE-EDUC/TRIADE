<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_work_selector.class.php,v 1.8 2022/04/05 14:30:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_resource_selector
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_contribution_datatype_linked_work_selector  extends onto_common_datatype {

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
	
	public function get_raw_value() {
	    //si c'est un tableau, on retourne la première valeur dans le cas générale
	    if (is_array($this->value)) {
	        foreach ($this->value as $key => $value) {
	            return $value;
	        }
	    }
	    return $this->value;
	}
	
	public function get_formated_value(){
	    if (isset($this->formated_value)) {
	        return $this->formated_value;
	    }
	    
	    $this->formated_value = array(
	        'work' => array(
	            'value' => $this->get_raw_value(),
	            'display_label' => $this->offsetget_value_property('display_label') ?? "",
	        )
	    );
	    
	    $assertions = $this->offsetget_value_property("assertions");
	    if (is_array($assertions)) {
	        /* @var $assertion onto_assertion */
	        foreach ($assertions as $assertion) {
	            switch ($assertion->get_predicate()) {
	                case 'http://www.pmbservices.fr/ontology#relation_type_work' :
	                    $this->formated_value['relation_type_work'] = $assertion->get_object();
	                    break;
	                case 'http://www.pmbservices.fr/ontology#has_work' :
	                    $properties = static::get_properties_from_uri($assertion->get_object());
	                    if (empty($properties["http://www.pmbservices.fr/ontology#is_draft"])) $properties["http://www.pmbservices.fr/ontology#is_draft"] = false;
	                    
	                    $this->formated_value['work'] = array(
                            'value' => $assertion->get_object(),
                            'display_label' => $assertion->offset_get_object_property('display_label'),
	                        'form_uri' => $properties["http://www.pmbservices.fr/ontology#form_uri"] ?? "",
	                        'form_id' => $properties["http://www.pmbservices.fr/ontology#form_id"] ?? 0,
	                        'area_id' => $properties["http://www.pmbservices.fr/ontology#area"] ?? 0,
	                        'is_draft' => ($properties["http://www.pmbservices.fr/ontology#is_draft"] == true ? "1" : "0")
	                    );
	                    break;
	            }
	        }
	    }
		return $this->formated_value;
	}
	
	public function get_value_type() {
	    return 'http://www.pmbservices.fr/ontology#linked_work';
	}
	
	/**
	 * 
	 * @param string $instance_name
	 * @param onto_common_property $property
	 * @param string $uri_item
	 * @return array
	 */
	public static function get_values_from_form($instance_name, $property, $uri_item) {
	    global $opac_url_base;
	    
	    $datatypes = array();
	    $var_name = $instance_name."_".$property->pmb_name;
	    
	    global ${$var_name};
	    $values = ${$var_name};
	    
	    if ($values && count($values)) {
	        
	        foreach ($values as $order => $data) {
	            
	            $data = stripslashes_array($data);
	            
	            if (!empty($data["value"])) {
	                
	                $data_properties = array();
                    $data_properties["lang"] = "";
                    $data_properties["display_label"] = "";
                    
	                if (!empty($data["lang"])) {
	                    $data_properties["lang"] = $data["lang"];
	                }
	                
	                if ($data["type"] == "http://www.w3.org/2000/01/rdf-schema#Literal") {
	                    $data_properties["type"] = "literal";
	                } else {
	                    $data_properties["type"] = "uri";
	                }
	                
	                if (!empty($data["display_label"])) {
	                    $data_properties["display_label"] = $data["display_label"];
	                }
	                
	                
	                $work_uri = onto_common_uri::get_new_uri($opac_url_base."linked_work#");
	                $data_properties["object_assertions"] = array(
	                    new onto_assertion($work_uri, 'http://www.pmbservices.fr/ontology#has_work', $data["value"], "http://www.pmbservices.fr/ontology#work", array('type'=>"uri", "display_label" => $data_properties["display_label"])),
	                    new onto_assertion($work_uri, 'http://www.pmbservices.fr/ontology#relation_type_work', $data["relation_type_work"], "", array('type'=>"literal"))
	                );
	                
	                $class_name = static::class;
	                $datatypes[$property->uri][] = new $class_name($work_uri, 'http://www.pmbservices.fr/ontology#work', $data_properties);
	            }
	            
	        }
	    }
	    
	    return $datatypes;
	}
 
} // end of onto_common_datatype_resource_selector
