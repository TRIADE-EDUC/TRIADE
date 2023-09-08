<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_property.class.php,v 1.13 2019-04-19 14:33:32 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_root.class.php';
require_once $class_path.'/onto/common/onto_common_class.class.php';


/**
 * class onto_common_property
 * 
 */
class onto_common_property extends onto_common_root {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access public
	 */
	public $domain;
	
	/**
	 *
	 * @access public
	 */
	public $pmb_name;
	
	/**
	 * 
	 * @access public
	 */
	public $range;

	/**
	 *
	 * @access public
	 */
	public $pmb_datatype;
	
	/**
	 *
	 * @access public
	 */
	public $default_value;
	/**
	 * 
	 *
	 * @return void
	 * @access public
	 */

	/**
	 * 
	 * @var unknown
	 */
	public $pmb_marclist_type;
	
	/**
	 *
	 * @var unknown
	 */
	public $pmb_list_item;
	
	/**
	 *
	 * @var unknown
	 */
	public $pmb_list_query;
	
	/**
	 * 
	 * @var unknown
	 */
	public $pmb_extended;
	
	/**
	 * Tableau des URI des propriétés inverses à la propriété représentée
	 * @access public
	 */
	public $inverse_of;
	
	/**
	 * Options de champ perso en json
	 * @var string
	 */
	public $cp_options;
	
	/**
	 * Obligatoire
	 * 
	 * @var unknown
	 */
	protected $mandatory = false;
	
	/**
	 * Caché
	 * 
	 * @var unknown
	 */
	protected $hidden = false;
	
	/**
	 * Non affiché
	 * 
	 * @var unknown
	 */
	protected $undisplayed = false;

	protected $framework_params;
	
	public function __construct($uri,$ontology) {
		parent::__construct($uri,$ontology);
		$this->fetch_pmb_datatype();
		$this->fetch_default_value();
	} // end of member function __construct

	protected function fetch_label(){
		$this->label = $this->ontology->get_property_label($this->uri);
	}
	
	protected function fetch_pmb_datatype(){
		$this->pmb_datatype = $this->ontology->get_property_pmb_datatype($this->uri);
	}

	protected function fetch_default_value(){
		$this->default_value = $this->ontology->get_property_default_value($this->uri);
	}

	protected function fetch_flags(){
		$this->flags = $this->ontology->get_flags("",$this->uri);
	}
	
	public function set_domain($domain){
		$this->domain = $domain;	
	}
	
	public function set_range($range){
		$this->range = $range;
	}
	
	public function set_pmb_name($pmb_name){
		$this->pmb_name = $pmb_name;
	}
	
	public function set_inverse_of($inverse_of){
		$this->inverse_of = $inverse_of;
	}
	
	public function set_pmb_marclist_type($pmb_marclist_type) {
		$this->pmb_marclist_type = $pmb_marclist_type;
	}
	
	public function set_pmb_list_item($pmb_list_item) {
		$this->pmb_list_item = $pmb_list_item;
	}
	
	public function set_pmb_list_query($pmb_list_query) {
		$this->pmb_list_query = $pmb_list_query;
	}
	
	public function set_cp_options($cp_options) {
		$this->cp_options = $cp_options;
	}
	
	public function get_pmb_datatype_label($datatype)
	{
		global $msg;
		switch($datatype){
			case "http://www.pmbservices.fr/ontology#small_text":
				$label = $msg['onto_onto_pmb_datatype_pmb_datatype_small_text']; 
				break;
			case "http://www.pmbservices.fr/ontology#resource_selector":
				$label = $msg['onto_onto_pmb_datatype_pmb_datatype_resource_selector'];
				break;
			case "http://www.pmbservices.fr/ontology#text":
				$label = $msg['onto_onto_pmb_datatype_pmb_datatype_text'];
				break;
			case "http://www.pmbservices.fr/ontology#date":
				$label = $msg['onto_onto_pmb_datatype_pmb_datatype_date'];
				break;
			case "http://www.pmbservices.fr/ontology#small_text_card":
				$label = $msg['onto_onto_pmb_datatype_pmb_datatype_small_text_card'];
				break;
			default :
				$label = $datatype;
				break;
		}
		return $label;
	}
	
	public function set_mandatory($mandatory) {
		$this->mandatory = $mandatory;
		return $this;
	}
	
	public function is_mandatory() {
		return $this->mandatory;
	}
	
	public function set_hidden($hidden) {
		$this->hidden = $hidden;
		return $this;
	}
	
	public function is_hidden() {
		return $this->hidden;
	}
    
	public function set_pmb_extended($pmb_extended) {
		$this->pmb_extended = $pmb_extended;
	}
		
	public function get_pmb_extended() {
		return $this->pmb_extended;
	}
	
	public function set_framework_params($framework_params){
		if(!isset($this->framework_params)){
			$this->framework_params = $framework_params;
		}
	}
	
	public function get_framework_params(){
		return $this->framework_params;
	}
	
	public function set_undisplayed($undisplayed) {
		if ($undisplayed) {
			$this->undisplayed = true;
		}
		return $this;
	}
	
	public function is_undisplayed() {
		return $this->undisplayed;
	}
} // end of onto_common_property
