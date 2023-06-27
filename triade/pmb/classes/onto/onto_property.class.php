<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_property.class.php,v 1.4 2014-08-07 14:31:53 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/onto/onto_resource.class.php");


/**
 * class onto_property
 * 
 */
class onto_property extends onto_resource {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * tableau d'uri
	 * @access public
	 */
	public $domain;

	/**
	 * 
	 * @access public
	 */
	public $range;
	
	
	/**
	 *
	 * @access public
	 */
	public $default_value = array();
	
	/**
	 *
	 * @access public
	 */
	public $flags = array();




} // end of onto_property