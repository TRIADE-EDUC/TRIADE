<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.18 2018-10-12 13:13:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

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
	case 'resa_planning':
		include("./circ/resa_planning/resa_planning_ajax.inc.php");
		break;
	case 'empr' :
		include("./circ/empr/ajax/main.inc.php");
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
	case 'extended_search':
		require_once($class_path."/search.class.php");
		
		if(!isset($search_xml_file)) $search_xml_file = '';
		if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';
		
		$sc=new search(true, $search_xml_file, $search_xml_file_full_path);
		$sc->proceed_ajax();
		break;
	default:
		ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
		break;		
endswitch;	
