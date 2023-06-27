<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.12 2018-12-27 14:36:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/pnb/pnb.class.php');
switch($categ){
	case "editions_state" :
		include("./edit/editions_state/ajax_main.inc.php");
		break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'pnb':
	    switch($action) {
	        case 'mailto':
	            $pnb = new pnb();
	            if(isset($commands_ids)){
	                $commands_ids = explode(',',$commands_ids);
	            }
	            print encoding_normalize::json_encode($pnb->get_mailto_data($commands_ids));
	            break;
	    }
	case 'transferts':
	case 'empr':
	case 'expl':
	case 'notices':
		switch($action) {
			case "list":
				require_once($class_path.'/list/lists_controller.class.php');
				$directory = $categ;
				switch($categ){
					case 'empr':
						$directory = 'readers';
						break;
					case 'expl':
						$directory = 'loans';
						break;
					case 'notices':
						$directory = 'reservations';
						break;
				}
				lists_controller::proceed_ajax($object_type, $directory);
				break;
		}
		break;
	case 'campaigns' :
		require_once($class_path.'/campaigns/campaigns_controller.class.php');
		campaigns_controller::proceed_ajax($object_type);
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("edit",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
}