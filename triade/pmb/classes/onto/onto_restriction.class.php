<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_restriction.class.php,v 1.5 2014/04/18 09:06:06 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/common/onto_common_property.class.php");


/**
 * class onto_restriction
 * 
 */
class onto_restriction {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Cardinalité minimum
	 * @access private
	 */
	private $min = 0;

	/**
	 * Cardinalité maximum : -1 = *
	 * @access private
	 */
	private $max = -1;

	/**
	 * Propriétés non utilisables avec la propriété associée à  la restriction
	 * @access private
	 */
	private $exclusion;

	/**
	 * Propriétés dont la valeur doit être différente de celle de la propriété associée à la restriction
	 * @access private
	 */
	private $distinct;
	

	public function set_min($min){
		$this->min = $min;
	}
	
	public function set_max($max){
		$this->max = $max;
	}
	
	public function set_new_distinct($distinct){
		$this->distinct[$distinct->uri] = $distinct;
	}
	
	public function set_new_exclusion($exclusion){
		$this->exclusion[$exclusion->uri] = $exclusion; 
	}
	
	public function get_min(){
		return $this->min;
	}
	
	public function get_max(){
		return $this->max;
	}
	
	public function get_distinct(){
		return $this->distinct;
	}
	
	public function get_exclusion(){
		return $this->exclusion;
	}
} // end of onto_restriction