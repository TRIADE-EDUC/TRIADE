<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_resource_pmb_selector.class.php,v 1.1 2017/05/22 13:34:07 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_resource_selector
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_common_datatype_resource_pmb_selector  extends onto_common_datatype {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	public function __construct($value, $value_type, $value_properties,$datatype_ui_class_name='') {
		parent::__construct($value, $value_type, $value_properties,$datatype_ui_class_name='');
		//todo recup vrai info...
	} 
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
	
	protected function calc_uri(){
		$uri = $this->value_type."_".$this->value;
		onto_common_uri::set_new_uri($uri);
		return $uri;
	}
	
	public function get_raw_value(){
		return $this->calc_uri();
	}
	
	

} // end of onto_common_datatype_resource_selector
