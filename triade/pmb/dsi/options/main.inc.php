<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.6 2021/12/01 13:09:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $id_classement;

switch($sub) {
    case 'classements':
    default:
    	require_once($class_path."/dsi/classements_controller.class.php") ;
    	classements_controller::proceed($id_classement);
        break;
}

