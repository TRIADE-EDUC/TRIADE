<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_abts.class.php,v 1.2.6.1 2025/01/16 10:24:11 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");
require($base_path."/selectors/templates/abts.tpl.php");

class selector_abts extends selector {

	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	public function get_title() {
		global $msg;
		return $msg["abts_sel_title"];
	}
}
?>