<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_integer.class.php,v 1.3 2019/06/11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_small_text
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_common_datatype_integer extends onto_common_datatype {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	
	public function check_value(){
		if (is_numeric($this->value)){
			return true;
		}
		return false;
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
			foreach (${$var_name} as $order => $data) {
				$data=stripslashes_array($data);
				if($data['value'] || $data['value']=='0') {
					$data_properties = array();
					$data_properties["type"] = "literal";
					$class_name = static::class;
					$datatypes[$property->uri][] = new $class_name($data["value"], $data["type"], $data_properties);
				}
			}
		}
		return $datatypes;
	}
} // end of onto_common_datatype_small_text
