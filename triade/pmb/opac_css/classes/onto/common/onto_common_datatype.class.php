<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype.class.php,v 1.13.8.1 2025/03/05 10:20:03 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class onto_common_datatype
 * 
 */
abstract class onto_common_datatype {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Indice de la valeur si ordonnée, 0 sinon
	 * @access public
	 */
	public $order = 0;
	
	/**
	 * 
	 * @access protected
	 */
	protected $value;
	
	/**
	 * 
	 * @access protected
	 */
	protected $value_type;
	
	/**
	 * Propriétés de la valeur (type, langue, ...)
	 * @access protected
	 */
	protected $value_properties;
	
	/**
	 * Le nom de la class UI à utiliser
	 * 
	 * @var string
	 */
	protected $datatype_ui_class_name;
	
	/**
	 * 
	 *
	 * @param  value valeur associé

	 * @param bool multiple 

	 * @return void
	 * @access public
	 */
	public function __construct($value, $value_type, $value_properties,$datatype_ui_class_name='') {
		$this->value = $value;
		$this->value_type = $value_type;
		$this->value_properties = $value_properties;
		$this->set_datatype_ui_class_name($datatype_ui_class_name);
	} // end of member function __construct


	public abstract function check_value();
	
	public function get_value() {
		return $this->value;
	}
	
	public function get_formated_value() {
		//si c'est un tableau, on retourne la première valeur dans le cas générale
		if (is_array($this->value)) {
			foreach ($this->value as $key => $value) {
				return $value;
			}
		}
		return $this->value;
	}
	
	public function get_raw_value() {
		return $this->value;
	}
	
	public function get_lang() {
		if (isset($this->value_properties["lang"])) return $this->value_properties["lang"];
		return false;
	}
	
	public function set_order($order) {
		$this->order = $order;
	}
	
	public function get_order() {
		return $this->order;
	}
	
	public function get_value_type() {
		return $this->value_type;
	}
	
	public function get_value_properties() {
		return $this->value_properties;
	}
		
	public function offsetget_value_property($offset) {
	    return isset($this->value_properties[$offset]) ? $this->value_properties[$offset] : null;
	}
	
	/**
	 * 
	 * Rempli la variable datatype_ui_class_name
	 * 
	 * @param string $ui_class_name
	 */
	public function set_datatype_ui_class_name($datatype_ui_class_name='', $restriction = NULL){
		if($datatype_ui_class_name && $this->datatype_ui_class_name != $datatype_ui_class_name && class_exists($datatype_ui_class_name)){
			//on peut vouloir le forcer ...
			$this->datatype_ui_class_name=$datatype_ui_class_name;
		}	
	}
	
	/**
	 * 
	 * Renvoi le nom de la class ui datatype_ui_class_name à utiliser pour le datatype
	 * 
	 * @return string
	 */
	public function get_datatype_ui_class_name(){
		return $this->datatype_ui_class_name;
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
				$data = stripslashes_array($data);
				if(! is_array($data) || !isset($data["value"])){
					continue;
				}
	            // On test si on vient des contributions ou des concepts
	            if ($property->onto_name === "contribution") {
	                if (is_string($data["value"]) && strlen($data["value"]) > 1) {
	                    $check = ($data["value"] !== null && !empty($data["value"]));
	                } else {
	                    $check = ($data["value"] !== null);
	                }
	            } else {
	                $check = (!empty($data["value"]));
	            }
	            
	            if ($check) {
				    
					$data_properties = array();
					
					if (!empty($data["lang"])) {
						$data_properties["lang"] = $data["lang"];
					}
					
					if (isset($data["type"]) && $data["type"] == "http://www.w3.org/2000/01/rdf-schema#Literal") {
						$data_properties["type"] = "literal";
					} else {
						$data_properties["type"] = "uri";
					}
					
					if (!empty($data["display_label"])) {
						$data_properties["display_label"] = $data["display_label"];
					}
					
					$class_name = static::class;
					$datatypes[$property->uri][] = new $class_name($data["value"], (isset($data["type"]) ? $data["type"] : null), $data_properties);
				}
			}
		}
		
		return $datatypes;
	}
	
	public static function get_properties_from_uri($uri) {
	    $contribution_area_store = new contribution_area_store();
	    return $contribution_area_store->get_properties_from_uri($uri);
	}
	
	public static function get_assertion_from_uri_with_predicate($item_uri, $predicate) {
	    $item = onto_handler::get_item_instance($item_uri);
	    $item = $item->get_assertions();
	    
	    foreach ($item as $properties){
	        if ($predicate == $properties->get_predicate()) {
	            return $properties;
	        }
	    }
	    return '';
	}
} // end of onto_common_datatype
