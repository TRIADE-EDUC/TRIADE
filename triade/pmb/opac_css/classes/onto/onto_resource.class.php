<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_resource.class.php,v 1.1 2017-01-06 16:10:51 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/onto/onto_ontology.class.php");


/**
 * class onto_resource
 * 
 */
class onto_resource {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access public
	 */
	public $uri;

	/**
	 * 
	 * @access public
	 */
	public $name;

	/**
	 * Nom associé à  utilisé pour la factory
	 * @access private
	 */
	public $pmb_name;






} // end of onto_resources