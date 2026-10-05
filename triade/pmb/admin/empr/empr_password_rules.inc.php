<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_password_rules.inc.php,v 1.1.8.1 2024/08/02 12:44:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $lang;
global $action;

require_once "{$class_path}/empr_password_rules.class.php";

switch ($action) {

	case 'save' :
		empr_password_rules::save();
		empr_password_rules::get_form();
		break;

	case 'reset' :
		empr_password_rules::reset();
		empr_password_rules::get_form();
		break;

	case 'get_form' :
	default :
		empr_password_rules::get_form();
		break;
}

