<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_class.class.php,v 1.3 2018-03-12 16:44:30 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/onto_resource.class.php");


/**
 * class onto_class
 * 
 */
class onto_class extends onto_resource {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 *
	 * @access protected
	 */
	public $sub_class_of;


	public function add_sub_class_of($sub_class_of) {
		if (!isset($this->sub_class_of)) {
			$this->sub_class_of = array();
		}
		if (!in_array($sub_class_of, $this->sub_class_of)) {
			$this->sub_class_of[] = $sub_class_of;
		}
	}

} // end of onto_class