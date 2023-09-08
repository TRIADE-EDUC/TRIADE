<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_bulletin.class.php,v 1.3 2017-06-12 14:54:57 jpermanne Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");
require($base_path."/selectors/templates/sel_bulletin.tpl.php");

class selector_bulletin extends selector {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
	}
	
	public static function get_params_url() {
		global $idperio;
	
		if(!$parent) $parent=0;
		if(!$user_input) $user_input = $f_user_input;
	
		$params_url = parent::get_params_url();
		$params_url .= ($idperio ? "&idperio=".$idperio : "");
		return $params_url;
	}
}
?>