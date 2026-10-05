<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.6 2021/02/08 10:30:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;

switch($sub) {
	case 'check' :
		require_once $class_path."/pnb/pnb_check.class.php";
		pnb_check::proceed();
		break;
	case 'param' :
    default:
		require_once $class_path."/pnb/pnb_param.class.php";
		pnb_param::proceed();
		break;
}
