<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: session.inc.php,v 1.2 2019/05/29 12:03:09 btafforeau Exp $

global $class_path, $sub, $action, $type, $value;

require_once($class_path."/session.class.php");

switch($sub){
	case "last_used" :
		switch($action){
			case "save" :
				if(SESSrights & CATALOGAGE_AUTH){
					session::set_last_used($type, $value);
				}
				break;
		}
		break;
	default :
		break;
}