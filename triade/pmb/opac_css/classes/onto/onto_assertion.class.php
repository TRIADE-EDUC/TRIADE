<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_assertion.class.php,v 1.4 2022/10/18 10:04:54 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class onto_assertion
 * Un triplet (une déclaration) !
 */
class onto_assertion {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * URI du sujet de la déclaration
	 * @access private
	 */
	private $subject;

	/**
	 * URI du prédicat de la déclaration
	 * @access private
	 */
	private $predicate;

	/**
	 * URI ou valeur litérale de l'objet de la déclaration
	 * @access private
	 */
	private $object;

	/**
	 * Type de l'objet de la déclaration (URI ou litérale)
	 * @access private
	 */
	private $object_type;
	

	/**
	 * Type de l'objet de la déclaration (URI ou litérale)
	 * @access private
	 */
	private $object_properties;
	
	public function __construct($subject="",$predicate="",$object="",$object_type="",$object_properties= array()){
		$this->subject = $subject;
		$this->predicate = $predicate;
		$this->object = $object;
		$this->object_type = $object_type;
		$this->object_properties = $object_properties;
	}


	public function get_subject() {
	    if (is_numeric($this->subject)) {
	        $this->subject = intval($this->subject);
	        $this->subject = onto_common_uri::get_uri($this->subject);
	    }
	    return $this->subject;
	}
	public function get_predicate(){
		return $this->predicate;
	} 
	public function get_object(){
		return $this->object;
	}	
	public function get_object_type(){
		return $this->object_type;
	} 
	public function get_object_properties(){
		return $this->object_properties;
	}
	
	public function offset_get_object_property($offset){
	    if (isset($this->object_properties[$offset])) {
	        return $this->object_properties[$offset];
	    }
	    return null;
	}
	
	public function set_object($obj){
	   $this->object = $obj;
    }
} // end of onto_assertion