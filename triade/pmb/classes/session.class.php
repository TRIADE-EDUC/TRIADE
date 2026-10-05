<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: session.class.php,v 1.3 2024/03/21 11:06:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class session {
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct() {

	}
	
	public static function get_last_used($type) {
		return $_SESSION["last_".$type."_used"];
	}
	
	public static function set_last_used($type, $value) {
		$_SESSION["last_".$type."_used"] = $value;
	}
	
// 	static function get_value($name) {
// 		return $_SESSION[$name];
// 	}
	
	public static function set_value($name, $value) {
		$_SESSION[$name] = $value;
	}
	
} // class session


