<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: translations.inc.php,v 1.2 2019-05-29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $num_field, $table_name;

require_once($class_path."/encoding_normalize.class.php");
require_once($class_path."/translation.class.php");

switch($action){
	case 'get_languages':
		print encoding_normalize::json_encode(translation::get_languages());
		break;
	case 'get_translations':
		$translation = new translation($num_field, $table_name);
		print encoding_normalize::json_encode($translation->get_data());
		break;
}