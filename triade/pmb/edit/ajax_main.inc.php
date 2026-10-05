<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.20.2.2 2024/09/13 09:24:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $categ, $sub, $action, $plugin;
global $object_type, $filters, $commands_ids;
global $id_proc;

require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/pnb/pnb.class.php');
switch($categ){
    case 'procs':
        switch($action) {
            case "list":
                $id_proc = intval($id_proc);
                if ($id_proc) {
                    if (!empty($filters)) {
                        $parameters_filters = encoding_normalize::json_decode(stripslashes($filters), true);
                        foreach ($parameters_filters as $name=>$values) {
                            global ${$name};
                            ${$name} = $values;
                        }
                    }
                    list_query_proc_edition_ui::set_id_proc($id_proc);
                    procs_edition_controller::set_list_ui_class_name('list_query_proc_edition_ui');
                    $object_type=substr($object_type,0,strpos($object_type, '_ui_')+3);
                    procs_edition_controller::proceed_ajax($object_type);
                }
                break;
        }
        break;
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
	        case "list":
	            lists_controller::proceed_ajax($object_type);
	            break;
	    }
	    break;
	case 'expl':
		switch($action) {
			case "list":
				require_once($class_path."/loans/loans_edition_controller.class.php");
				loans_edition_controller::proceed_ajax($object_type, 'loans');
				break;
		}
		break;
	case 'notices':
		switch($action) {
			case "list":
				if($sub == 'resa_planning') {
					$directory = 'resa_planning';
				} else {
					$directory = 'reservations';
				}
				require_once($class_path."/reservations/reservations_edition_controller.class.php");
				//Les noms de filtres ont changé - on assure la rétro-compatibilité
				if($object_type == 'reservations_edition_treat_ui') {
					list_reservations_edition_treat_ui::set_globals_from_json_filters(stripslashes($filters));
				}
				reservations_edition_controller::proceed_ajax($object_type, $directory);
				break;
		}
		break;
	case 'empr':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'readers');
				break;
		}
		break;
	case 'serials':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'records');
				break;
		}
		break;
	case 'transferts':
		switch($action) {
			case "list":
				require_once($class_path."/transferts/transferts_edition_controller.class.php");
				transferts_edition_controller::proceed_ajax($object_type, 'transferts');
				break;
		}
		break;
	case 'transferts_demandes':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'transferts');
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
	default:
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'configuration/'.$categ);
				break;
		}
		break;
}