<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.9 2019-02-12 08:28:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ){	
	case 'bannettes':
		switch($action) {
			case "list":
				require_once($class_path.'/list/lists_controller.class.php');
				lists_controller::proceed_ajax($object_type, 'bannettes');
				break;
			default:
				include('./dsi/bannettes/main.inc.php');
				break;
		}
		break;		
	break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'docwatch' :
		include("./dsi/docwatch/ajax_main.inc.php");
		break;
	case 'empr':
		switch($action) {
			case "list":
				require_once($class_path.'/list/lists_controller.class.php');
				lists_controller::proceed_ajax($object_type, 'readers');
				break;
		}
		break;
	case 'fluxrss':
		switch($action) {
			case "list":
				require_once($class_path.'/list/lists_controller.class.php');
				lists_controller::proceed_ajax($object_type);
				break;
		}
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("dsi",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	default:
	//tbd
	break;		
}	
