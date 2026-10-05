<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_root_controller.class.php,v 1.4 2023/07/27 06:57:38 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

abstract class caddie_root_controller {
	
	protected static $user_query = '';
	
	protected static $object_type = '';
	
	protected static $model_class_name = '';

	protected static $procs_class_name = '';
	
	public static function get_model_class_name() {
		return static::$model_class_name;
	}
	
	public static function get_procs_class_name() {
		return static::$procs_class_name;
	}
	
	public static function proceed($id = 0) {
		global $lvl;
		
		switch ($lvl) {
			case "more_results":
				static::proceed_more_results();
				break;
		}
	}
	
	public static function set_user_query($user_query) {
		static::$user_query = $user_query;
	}
} // fin de déclaration de la classe caddie_root_controller
