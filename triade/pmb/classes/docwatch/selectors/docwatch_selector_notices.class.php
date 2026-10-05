<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_notices.class.php,v 1.1 2014/12/24 11:18:09 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/selectors/docwatch_selector.class.php");

/**
 * class docwatch_selector_notice
 * 
 */
class docwatch_selector_notices extends docwatch_selector{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * @return void
	 * @access public
	 */
	
	
	public function __construct($id=0) {
		parent::__construct($id);
		$this->value = array();
	}
} // end of docwatch_selector_notice

