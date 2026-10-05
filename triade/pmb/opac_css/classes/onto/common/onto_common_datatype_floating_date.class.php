<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_floating_date.class.php,v 1.3 2023/05/05 09:48:09 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_floating_date
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_common_datatype_floating_date extends onto_common_datatype {

	/** Aggregations: */

	/** Compositions: */

    /*** Attributes: ***/
    
    public $formated_value;
	
	
	public function check_value(){
		if (is_string($this->value) && (strlen($this->value) < 512)) return true;
		return false;
	}
	
	public function get_formated_value(){
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
	    return $this->formated_value;
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
    	            $data_properties["type"] = "literal";
	                $class_name = static::class;
	                $formated_values = $data['value'].'|||'.$data['date_begin'].'|||'.$data['date_end'].'|||'.$data['comment'];
	                $datatypes[$property->uri][] = new $class_name($formated_values, 'http://www.w3.org/2000/01/rdf-schema#Literal', $data_properties);
	            }
	        }
	    }
	    return $datatypes;
	}
} // end of onto_common_datatype_floating_date
