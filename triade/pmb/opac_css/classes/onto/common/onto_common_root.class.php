<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_root.class.php,v 1.3 2023/02/07 15:31:40 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class onto_common_root
 * 
 */
class onto_common_root {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access public
	 */
	public $label;

	/**
	 * 
	 * @access protected
	 */
	public $uri;

	/**
	 *
	 * @access protected
	 *
	 * @var onto_ontology
	 */
	protected $ontology;
	
	/**
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $flags;
	
	/**
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $onto_name;
	
	/**
	 * Store de données
	 * @var onto_store
	 * @access private
	 */
	protected $data_store;
	/**
	 * 
	 *
	 * @param string uri
	 * @param onto_ontology ontology

	 * @return void
	 * @access public
	 */
	public function __construct($uri,$ontology) {
		$this->uri = $uri;
		$this->ontology = $ontology;
		$this->fetch_label();
		$this->fetch_flags();
	} // end of member function __construct
	
	protected function fetch_label(){
		return "";
	}	
	
	public function set_label($label)
	{
	    $this->label = $label;
	}
	
	protected function fetch_flags(){
		$this->flags = $this->ontology->get_flags($this->uri);
	}
	
	public function set_onto_name($onto_name){
		$this->onto_name = $onto_name;
	}
	
	public function set_data_store($data_store){
		$this->data_store = $data_store;
	}
	
	public function get_ontology()
	{
		return $this->ontology;
	}
} // end of onto_common_root
