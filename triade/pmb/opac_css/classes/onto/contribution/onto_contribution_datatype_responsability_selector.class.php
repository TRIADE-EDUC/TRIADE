<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_responsability_selector.class.php,v 1.5 2019-06-11 08:53:57 btafforeau Exp $

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
	
	/**
	 *
	 * @param $instance_name string
	 * @param $property onto_common_property
	 * @return boolean
	 */
	public static function get_values_from_form($instance_name, $property, $uri_item) {
	    global $opac_url_base;
        $datatypes = array();
        $var_name = $instance_name."_".$property->pmb_name;
        global ${$var_name};
        if (${$var_name} && count(${$var_name})) {
            foreach (${$var_name} as $order => $data) {
                $data=stripslashes_array($data);
                if (($data["value"] !== null) && ($data["value"] !== '')) {
                    $data_properties = array();
                    if ($data["lang"]) {
                        $data_properties["lang"] = $data["lang"];
                    }
                    if ($data["type"] == "http://www.w3.org/2000/01/rdf-schema#Literal") {
                        $data_properties["type"] = "literal";
                    } else {
                        $data_properties["type"] = "uri";
                    }
                    if ($data["display_label"]) {
                        $data_properties["display_label"] = $data["display_label"];
                    }
                    $responsablity_uri = onto_common_uri::get_new_uri($opac_url_base."responsability#");
                    $data_properties["object_assertions"] = array(
                        new onto_assertion($responsablity_uri, 'http://www.pmbservices.fr/ontology#has_author', $data["value"], "", array('type'=>"uri")),
                        new onto_assertion($responsablity_uri, 'http://www.pmbservices.fr/ontology#author_function', $data["author_function"], "", array('type'=>"literal"))
                    );                    
                    $class_name = static::class;
                    //$datatypes[$property->uri][] = new $class_name($responsablity_uri, $data["type"], $data_properties);
                    $datatypes[$property->uri][] = new $class_name($responsablity_uri, 'http://www.pmbservices.fr/ontology#responsability', $data_properties);
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
