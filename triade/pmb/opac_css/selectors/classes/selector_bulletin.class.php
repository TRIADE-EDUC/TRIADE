<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_bulletin.class.php,v 1.2.6.1 2025/01/16 11:24:28 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");
require($base_path."/selectors/templates/sel_bulletin.tpl.php");

class selector_bulletin extends selector {

	public function __construct($user_input=''){
		parent::__construct($user_input);
	}
}
?>