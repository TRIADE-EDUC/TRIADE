<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_lang.class.php,v 1.2.6.1 2025/01/16 11:24:28 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;

require_once($base_path."/selectors/classes/selector_marc_list.class.php");
require_once($class_path."/marc_table.class.php");

class selector_lang extends selector_marc_list {

	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	protected function get_marc_list_instance() {
		global $s_lang;
		if (!count($s_lang)) {
			$s_lang = new marc_list('lang');
		}
		return $s_lang;
	}
}
?>