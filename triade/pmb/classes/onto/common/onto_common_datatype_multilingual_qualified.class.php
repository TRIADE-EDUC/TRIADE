<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_multilingual_qualified.class.php,v 1.2 2021/07/15 15:43:55 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';

/**
 * class onto_common_datatype_multilingual_qualified
 */
class onto_common_datatype_multilingual_qualified extends onto_common_datatype {

	/** Aggregations: */

	/** Compositions: */

	/*** Attributes: ***/
	
	public function check_value(){
		if (is_string($this->value)) return true;
		return false;
	}
	
	
	public function get_formated_value() {
	    if (isset($this->formated_value)) {
	        return $this->formated_value;
	    }
	    
	    $val = $this->value;
	    if (is_array($this->value)) {
	        foreach ($this->value as $value) {
	            $val = $value;
	            break;
	        }
	    }
	    
	    $this->formated_value = explode('|||', $val);
	    if (empty($this->formated_value[1])) {
	        $this->formated_value[1] = $this->get_lang();
	    }
	    return $this->formated_value;
	}
	
	/**
	 *
	 * @param $instance_name string
	 * @param $property onto_common_property
	 * @return boolean
	 */
	public static function get_values_from_form($instance_name, $property, $uri_item) {
	    $datatypes = array();
	    $var_name = $instance_name."_".$property->pmb_name;
	    
	    global ${$var_name};
	    if (${$var_name} && count(${$var_name})) {
	        foreach (${$var_name} as $data) {
	            $data=stripslashes_array($data);
	            if (($data["value"] !== null) && ($data["value"] !== '')) {
	                $data_properties = array();
	                $data_properties["lang"] = $data['lang'];
                    $data_properties["type"] = "literal";
	                $formated_values = $data['value'].'|||'.$data['lang'].'|||'.$data['qualification'];
	                $class_name = static::class;
	                $datatypes[$property->uri][] = new $class_name($formated_values, 'http://www.w3.org/2000/01/rdf-schema#Literal', $data_properties);
	            }
	        }
	    }
	    
	    return $datatypes;
	}
} // end of onto_common_datatype_text
