<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.14 2023/09/12 12:26:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $categ, $sub, $action, $class_path, $object_type, $plugin;

//En fonction de $categ, il inclut les fichiers correspondants
switch($categ){
    case 'diffuser':
        switch ($sub) {
            case 'history':
                switch($action) {
                    case "list":
                        require_once($class_path."/dsi/bannettes_diffusions_controller.class.php");
                        bannettes_diffusions_controller::proceed_ajax($object_type, 'bannettes');
                        break;
                }
                break;
            default:
                break;
        }
        break;
	case 'bannettes':
		switch ($sub) {
			case 'classements':
				switch($action) {
					case "list":
						require_once($class_path.'/dsi/classements_controller.class.php');
						classements_controller::proceed_ajax($object_type, 'bannettes');
						break;
				}
				break;
			default:
				switch($action) {
					case "list":
						lists_controller::proceed_ajax($object_type, 'bannettes');
						break;
					default:
						include('./dsi/bannettes/main.inc.php');
						break;
				}
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
				lists_controller::proceed_ajax($object_type, 'readers');
				break;
		}
		break;
	case 'fluxrss':
		switch($action) {
			case "list":
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
	case 'search':
	    require_once($class_path."/search.class.php");
	    
	    if(!isset($search_xml_file)) $search_xml_file = '';
	    if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';
	    
	    $sc=new search(true, $search_xml_file, $search_xml_file_full_path);
	    $sc->proceed_ajax();
	    break;
	default:
	//tbd
	break;		
}	
