<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.14 2019-06-03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $categ, $datas, $caddie, $object, $plugin, $sub, $file, $search_xml_file, $search_xml_file_full_path;

require_once($class_path."/caddie/authorities_caddie_controller.class.php");

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ):
	case 'commande':
		
	break;
	case 'type_empty_word':
		include('./autorites/semantique/ajax/type_empty_word.inc.php');
	break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'grid' :
		require_once($class_path."/grid.class.php");
		grid::proceed($datas);
		break;
	case 'fill_form':
		include('./autorites/fill_form/ajax_main.inc.php');
		break;
	case 'get_tu_form_vedette':
		include('./autorites/titres_uniformes/tu_form_vedette.inc.php');
		break;
	case 'caddie':
		if(isset($caddie)) {
			$idcaddie = substr($caddie, strrpos($caddie, '_')+1);
		}
		if(isset($object)) {
			$id_item = substr($object, strrpos($object, '_')+1);
		}
		authorities_caddie_controller::proceed_ajax($idcaddie, $id_item);
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("autorites",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'extended_search':
		require_once($class_path."/search_authorities.class.php");
	
		if(!isset($search_xml_file)) $search_xml_file = '';
		if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';
	
		$sc=new search_authorities(true, $search_xml_file, $search_xml_file_full_path);
		$sc->proceed_ajax();
		break;
	case 'get_auth_persos' :
		$authpersos = new authpersos();
		print encoding_normalize::json_encode($authpersos->get_data());
		break;
	case 'search_perso':
		require_once($class_path."/search_perso.class.php");
		$search_p= new search_perso(0, 'AUTHORITIES');
		$search_p->proceed_ajax();
		break;
	case 'vedettes' : 
	    include("./autorites/vedettes/ajax_main.inc.php");
	    break;
	default:
	//tbd
	break;		
endswitch;	
