<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_class.class.php,v 1.6 2023/05/05 07:16:43 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

require_once($class_path."/onto/onto_resource.class.php");

class onto_class extends onto_resource {


	/**
	 *
	 * @access protected
	 */
	public $sub_class_of;
	
	public $field;

	public $label;

	public $id;

	public function add_sub_class_of($sub_class_of) {
		if (!isset($this->sub_class_of)) {
			$this->sub_class_of = array();
		}
		if (!in_array($sub_class_of, $this->sub_class_of)) {
			$this->sub_class_of[] = $sub_class_of;
		}
	}

} // end of onto_class