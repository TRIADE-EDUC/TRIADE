<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sugg.inc.php,v 1.2 2017/03/08 12:40:04 jpermanne Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/suggestions.class.php');

switch($sub){
	case 'get_doublons':
		ajax_http_send_response(suggestions::get_doublons());
		break;
	break;
}