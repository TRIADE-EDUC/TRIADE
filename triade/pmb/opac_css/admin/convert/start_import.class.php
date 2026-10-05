<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: start_import.class.php,v 1.1 2018/07/25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class start_import {

	public static function get_instance_from_input_type($input_type) {
		global $base_path, $msg;
		global $param_path;
		global $input_params;
		
		switch ($input_type) {
			case "xml" :
				require_once ("$base_path/admin/convert/imports/input_xml.class.php");
				return new input_xml();
				break;
			case "iso_2709" :
				require_once ("$base_path/admin/convert/imports/input_iso_2709.class.php");
				return new input_iso_2709();
				break;
			case "text" :
				require_once("$base_path/admin/convert/imports/input_text.class.php");
				return new input_text();
				break;
			case "custom" :
				require_once ("$base_path/admin/convert/imports/$param_path/".$input_params['SCRIPT']);
				$input_classname = str_replace('.class.php', '', $input_params['SCRIPT']);
				if(class_exists($input_classname)) {
					return new $input_classname();
				} else {
					return;
				}
				break;
			default :
				die($msg["ie_import_entry_not_valid"]);
		}
	}

}

?>