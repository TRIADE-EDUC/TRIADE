<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: session.class.php,v 1.1 2015-08-10 10:32:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class session {
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	function __construct() {

	}
	
	static function get_last_used($type) {
		return $_SESSION["last_".$type."_used"];
	}
	
	static function set_last_used($type, $value) {
		$_SESSION["last_".$type."_used"] = $value;
	}
	
// 	static function get_value($name) {
// 		return $_SESSION[$name];
// 	}
	
// 	static function set_value($name, $value) {
// 		$_SESSION[$name] = $value;
// 	}
	
} // class session


