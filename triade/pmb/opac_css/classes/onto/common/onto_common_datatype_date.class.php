<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_date.class.php,v 1.2 2021/04/15 08:38:07 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_small_text
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_common_datatype_date extends onto_common_datatype {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	
	public function check_value() {
	    
	    if (empty($this->value)) {
	        return true;
	    }
	    
		if (is_string($this->value)) {
			$arr = explode('-',$this->value);
			
			if(count($arr) != 3) {
				return false;
			}
			
			$day = $arr[2];
			$month = $arr[1];
			$year = $arr[0];
			
			if(checkdate($month, $day, $year)) {
				return true;  
			}
		}
		return false;
	}
} // end of onto_common_datatype_small_text
