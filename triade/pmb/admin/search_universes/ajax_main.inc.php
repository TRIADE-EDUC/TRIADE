<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.5 2022/04/15 12:16:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $opac_search_universes_activate;

if (!$opac_search_universes_activate) {
	die();
}

// page de switch formulaire de contact

require_once ($class_path . '/search_universes/search_universe.class.php');
require_once ($class_path . '/search_universes/search_segment.class.php');
require_once ($class_path . '/encoding_normalize.class.php');
require_once ($class_path . '/search_universes/search_universes_controller.class.php');
$search_universe_controller = new search_universes_controller();
$search_universe_controller->proceed_ajax();

/**


switch($sub) {
	case 'universe':
		switch($action){
			case "save_graph":
				$area = new contribution_area($area_id);
				$area->save_graph($data);
				break;
		}
		break;
	case 'segment':
		switch($action){
			case 'save' :
				$form_id = intval($form_id);
				$form = new contribution_area_form($type, $form_id);
				$form->set_from_form();
				$result = $form->save(true);
				print encoding_normalize::json_encode($result);
				break;
			case 'delete':
				$form_id = intval($form_id);
				$form = new contribution_area_form($type, $form_id);
				print encoding_normalize::json_encode($form->delete(true)); 
				break;
			case 'get_filter_form' :
				$segment_id = intval($segment_id);
				$entity = "search_universes_entity_".$entity_type;
				$handler_parameters = array('segment_id'=>$segment_id,'entity_type'=>$entity_type );
				$handler = $entity::get_set_handler($segment_id, $handler_parameters);
				/**
				 * TODO: voir pour récupérer les parametres directement depuis l'entité ?
				 * Elle seule sera capable de connaitre ce dont a besoin le handler (en terme de globales et cie)
				 
				print encoding_normalize::json_encode(
					array(
							'segment_filter_form' => $handler->get_form(),
							'segment_sub_form' 	  => $handler->get_sub_form()
					)
				);
				break;
			default :
				if($type){
					$form_id = intval($form_id);
					$form = new contribution_area_form($type, $form_id);
					print $form->get_form();
				}else{
					print 'todo helper';
				}
				break;
		}
		break;
     case 'universes':
		switch($action) {
			case 'get_data':
				$search_universes_controller = new search_universes_controller();
				print encoding_normalize::json_encode($search_universes_controller->get_data());
               	break;
			}		
		break;
}


**/