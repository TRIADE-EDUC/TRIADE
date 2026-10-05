<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.30.6.1 2024/05/21 09:55:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
global $categ, $sub, $action, $plugin, $object_type;
global $msg, $search_xml_file, $search_xml_file_full_path;
global $filters, $datas;
//En fonction de $categ, il inclut les fichiers correspondants

switch($categ):
	case 'pret_ajax':
		include("./circ/pret_ajax/main.inc.php");
		break;
	case 'transferts':
		include("./circ/transferts/ajax/main.inc.php");
		break;			
	case 'print_pret':
		include("./circ/print_pret/main.inc.php");
		break;				
	case 'zebra_print_pret':
		include("./circ/print_pret/zebra_print_pret.inc.php");
		break;			
	case 'periocirc':
		include("./circ/serialcirc/serialcirc_ajax.inc.php");
		break;
	case 'serialcirc' :
		switch($action) {
			case "list":
				require_once($class_path.'/serialcirc/serialcirc_controller.class.php');
				serialcirc_controller::set_list_ui_class_name('list_serialcirc_ui');
				serialcirc_controller::proceed_ajax($object_type, 'serialcirc');
				break;
		}
		break;
	case 'resa' :
		switch($action) {
			case "list":
				require_once($class_path."/reservations/reservations_circ_controller.class.php");
			    //Les noms de filtres ont changé - on assure la rétro-compatibilité
			    if($object_type == 'reservations_circ_ui') {
			        list_reservations_circ_ui::set_globals_from_json_filters(stripslashes($filters));
			    }
			    reservations_circ_controller::proceed_ajax($object_type, 'reservations');
				break;
		}
		break;
	case 'resa_planning':
		include("./circ/resa_planning/resa_planning_ajax.inc.php");
		break;
	case 'empr' :
		include("./circ/empr/ajax/main.inc.php");
		break;
	case 'pret' :
		switch($action) {
			case "list":
				require_once($class_path.'/readers/readers_controller.class.php');
				readers_controller::set_list_ui_class_name('list_readers_circ_ui');
				readers_controller::proceed_ajax($object_type, 'readers');
				break;
		}
		break;
	case 'relance' :
		switch ($sub) {
			case 'recouvr':
				switch($action) {
					case "list":
						require_once($class_path.'/readers/readers_recouvr_controller.class.php');
						readers_recouvr_controller::proceed_ajax($object_type, 'readers');
						break;
				}
				break;
			case 'todo':
			default:
				switch($action) {
					case "list":
						require_once($class_path.'/readers/readers_relances_controller.class.php');
						readers_relances_controller::proceed_ajax($object_type, 'readers');
						break;
				}
				break;
		}
	    break;
	case 'groups' :
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'groups');
				break;
		}
		break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'zebra_print_card':
		include("./circ/print_card/zebra_print_card.inc.php");
		break;
	case 'expl':
		include("./circ/expl/ajax_main.inc.php");
		break;
	case 'scan_request':
		include('./circ/scan_request/ajax_main.inc.php');
		break;
	case 'caddie':
		include('./circ/caddie/caddie_ajax.inc.php');
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("circ",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'bannette':
		include('./circ/bannette/ajax_main.inc.php');
		break;
	case 'search_perso':
		require_once($class_path."/search_perso.class.php");
		$search_p= new search_perso(0, 'EMPR');
		$search_p->proceed_ajax();
		break;
	case 'grid' :
		require_once($class_path."/grid.class.php");
		grid::proceed($datas);
		break;
	case 'extended_search':
		require_once($class_path."/search.class.php");
		
		if(!isset($search_xml_file)) $search_xml_file = '';
		if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';
		
		$sc=new search(true, $search_xml_file, $search_xml_file_full_path);
		$sc->proceed_ajax();
		break;
	case 'groupexpl' :
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'items');
				break;
		}
		break;
	case 'caddies':
	    switch($action) {
	        case "list":
	            empr_caddie_controller::proceed_ajax();
	            break;
	    }
	    break;
	default:
		ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
		break;		
endswitch;	
