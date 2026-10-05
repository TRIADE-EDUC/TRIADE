<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_merge_properties.class.php,v 1.2 2017/02/22 09:11:24 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_resource_selector
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_common_datatype_merge_properties extends onto_common_datatype {

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
		$display_label = $this->offsetget_value_property("display_label");
		if ($display_label) {
			return $display_label;
		}
		return $this->value;
	}
		
	public function get_raw_value() {	
		//si c'est un tableau, on retourne la première valeur dans le cas générale
		$raw_value = '';
		if (is_array($this->value)) {
			foreach ($this->value as $key => $value) {
				if (!$raw_value) {
					$raw_value = $value;
				}
			}
		} else {
			$raw_value = $this->value;
		}
		
		return $this->value;
	}	
} // end of onto_common_datatype_resource_selector
