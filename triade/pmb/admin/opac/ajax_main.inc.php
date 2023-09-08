<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.8 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/facettes_controller.class.php");

switch ($sub) {
	case 'search_persopac':
		switch($action) {
			case "list":
				require_once($class_path.'/list/lists_controller.class.php');
				lists_controller::proceed_ajax($object_type, 'configuration/'.$categ);
				break;
		}
		break;
	default:
		if(!isset($type)) $type = 'notices';
		$facettes_controller = new facettes_controller(0, $type);
		$facettes_controller->proceed_ajax();
		break;
}	