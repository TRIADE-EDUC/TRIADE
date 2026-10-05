<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_class.class.php,v 1.13 2022/11/25 14:59:27 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_root.class.php';
require_once $class_path.'/onto/onto_ontology.class.php';
require_once $class_path.'/onto/common/onto_common_property.class.php';
require_once $class_path.'/onto/onto_restriction.class.php';


/**
 * class onto_common_class
 * 
 */
class onto_common_class extends onto_common_root {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access protected
	 */
	protected $properties;

	/**
	 * 
	 * @access protected
	 */
	protected $sub_class_of;

	/**
	 * Tableau associatif des restrictions associées à chaque propriété de la classe.
	 * L'étiquette du tableau est l'URI de la propriété concernée
	 * @access private
	 */
	private $onto_restrictions;

	/**
	 * 
	 *
	 * @param onto_ontology ontology 

	 * @return void
	 * @access public
	 */
	
	public $pmb_name; 
	
	public $field;
	
	public function __construct($uri,$ontology) {
		parent::__construct($uri,$ontology);
		$this->get_properties();
		$this->get_restrictions();
		$this->get_properties_and_restrictions_from_sub_class_of();
	} // end of member function __construct

	/**
	 * Retourne la liste des URI de propriétés liées à la classe
	 *
	 * @return array()
	 * @access public
	 */
	public function get_properties( ) {
		$properties = $this->ontology->get_class_properties($this->uri);
		foreach($properties as $property_uri){
			$this->set_property($this->get_property($property_uri));
		}
		return $properties;
	} // end of member function get_properties

	/**
	 * Retourne une instance de la propriété
	 *
	 * @param string uri_property uri de la propriété

	 * @return onto_common_property
	 * @access public
	 */
	public function get_property( $uri_property ) {
		return $this->ontology->get_property($this->uri,$uri_property);
	} // end of member function get_property

	/**
	 * 
	 *
	 * @param onto_common_property property objet représentant une propriété

	 * @return void
	 * @access public
	 */
	public function set_property( $property ) {
		$this->properties[$property->uri] = $property;
	} // end of member function set_property
	
	public function set_pmb_name($pmb_name){
		$this->pmb_name = $pmb_name;
	}
	
	public function set_field($field){
	    $this->field = $field;
	}
	
	protected function fetch_label(){
		$this->label = $this->ontology->get_class_label($this->uri);
	}
	
	public function get_restrictions(){
	    if (is_array($this->properties)) {
    		foreach($this->properties as $property){
    			$this->onto_restrictions[$property->uri] = $this->ontology->get_restriction($this->uri,$property->uri);
    		}
	    }
	}
	
	public function get_property_range($uri_property) {
		$range = array();
		if (isset($this->properties[$uri_property])) {
			$range = $this->properties[$uri_property]->range;
		}
		
		return $range;
	}
	
	public function get_property_pmb_datatype($uri_property) {
		$pmb_datatype = $this->properties[$uri_property]->pmb_datatype;
		return $pmb_datatype;
	}
	
	
	/**
	 *
	 *
	 * @param string  uri_property URI d'une propriété
	
	 * @return onto_restriction
	 * @access public
	 */
	public function get_restriction($uri_property){
		return $this->onto_restrictions[$uri_property];
	}
	
	public function get_base_uri(){
		global $opac_url_base;
		return $opac_url_base."/".$this->pmb_name."#";
	}
	
	/**
	 * 
	 */
	public function get_sub_class_of() {
		if (!isset($this->sub_class_of)) {
			$this->sub_class_of = $this->ontology->get_sub_class_of($this->uri);
		}
		return $this->sub_class_of;
	} 
	
	public function get_properties_and_restrictions_from_sub_class_of() {
		$properties = array();
		foreach ($this->get_sub_class_of() as $sub_class_of) {
			$properties = $this->ontology->get_class_properties($sub_class_of);
			foreach($properties as $property_uri){
				$this->set_property($this->get_property($property_uri));
				$this->onto_restrictions[$property_uri] = $this->ontology->get_restriction($sub_class_of,$property_uri);
			}
		}
		return $properties;
	}
} // end of onto_common_class;