<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation.inc.php,v 1.2 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub;

require_once($class_path."/indexation_stack.class.php");
require_once($class_path."/encoding_normalize.class.php");

switch($sub){
	case 'get_infos':
		print encoding_normalize::json_encode(indexation_stack::get_indexation_state());		
		break;
}
