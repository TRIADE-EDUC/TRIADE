<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_resource.class.php,v 1.2 2023/05/04 14:06:29 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/onto/onto_ontology.class.php");

class onto_resource {

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
	 * @var string
	 * @access public
	 */
	public $label;

	/**
	 * @access public
	 */
	public $flags;

	/**
	 * @access public
	 */
	public $flag;

	/**
	 * @var string
	 * @access public
	 */
	public $type;

	/**
	 * Nom associщ ра utilisщ pour la factory
	 * @access private
	 */
	public $pmb_name;

}
