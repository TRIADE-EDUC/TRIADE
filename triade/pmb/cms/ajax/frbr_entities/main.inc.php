<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.34 2023/01/11 15:46:26 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($type)) $type = '';

require_once($class_path."/frbr/frbr_place.class.php");

$object_id = 0;
if(!empty($id_element)){
    $object_id = intval($id_element);
} else if (!empty($id)) {
    $object_id = intval($id);
}

//TODO vérifier que la classe existe avant de l'instancier !!
if (!empty($elem)) {
    //cas particulier des autorites perso
    if (strpos($elem, "authperso") !== false) {
        $authperso =  preg_split("#_([\d]+)#", $elem, 0 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $element = new $authperso[0]($object_id);
        if (!empty($authperso[1])) {
            $element->set_authperso_id($authperso[1]);
        }
    } else {
        $element = new $elem($object_id);
    }
}

switch($action){
	case "save_form" :
		$element->set_properties_from_form();
		$status = $element->save();
		$response = encoding_normalize::json_encode(array('tree_data' => ($element->get_type() == "page" ? $element->get_dojo_tree() : $element->get_page()->get_dojo_tree()), 'status' => $status, 'type' => $element->get_type()));
		break;
	case "delete" :
		$status = false;
		$id_element = ( isset($id_element) ? intval($id_element) : 0 );
		if (!empty($type) && $id_element) {
			switch ($type) {
				case 'datanode':
					$status = frbr_entity_common_entity_datanode::delete($id_element, (isset($recursive) ? $recursive : false));
					break;
				case 'cadre':
					$status = frbr_entity_common_entity_cadre::delete($id_element);
					break;
			}
		}
		$num_page = ( isset($num_page) ? intval($num_page) : 0 );
		$frbr_page = new frbr_entity_common_entity_page($num_page);
		$response = encoding_normalize::json_encode(array('tree_data' => $frbr_page->get_dojo_tree(), 'status' => $status, 'type' => $type));
		break;	
 	case "ajax" :
 		$response_array = $element->execute_ajax();
 		ajax_http_send_response($response_array['content'], $response_array['content-type']);
 		break;	
	case "get_form" :
		switch($type){
			case 'datanode':
				$frbr_page = new frbr_entity_common_entity_page($num_page);
				if ($id) {
					$frbr_datanode_name = frbr_entity_common_entity_datanode::get_class_name_from_id($id);
					$frbr_datanode = new $frbr_datanode_name($id);
				} else {
					$frbr_datanode = new frbr_entity_common_entity_datanode();
				}				
				$frbr_datanode->set_page($frbr_page);
				$frbr_datanode->set_parent_from_num($num_parent);
				$frbr_datanode->get_parent_informations();
				$response = $frbr_datanode->get_form(true);
				break;
			case 'cadre' :				
				$id = intval($id);
				if ($id) {
					$frbr_cadre_name = frbr_entity_common_entity_cadre::get_class_name_from_id($id);
					$frbr_cadre = new $frbr_cadre_name($id);
				} else {
					$entity_type = 'common_entity';					
					if ($num_parent) {
						$entity_type = frbr_entity_common_entity_datanode::get_entity_type_from_id($num_parent);
					} elseif ($num_page) {
						$entity_type = frbr_entity_common_entity_page::get_entity_type_from_id($num_page);
						//vue par défaut quand on est sur un cadre racine associé à la page
						$default_view = 'frbr_entity_'.$entity_type.'_view';
					}
					$frbr_cadre_name = 'frbr_entity_'.$entity_type.'_cadre';
					$frbr_cadre = new $frbr_cadre_name($id);

					if (isset($default_view) && $default_view) {
						$frbr_cadre->elements_used['view'] = array($default_view);
					}
					
					if ($num_page) {
						$frbr_cadre->set_page_from_num($num_page);
					}
					$frbr_cadre->set_datanode_from_num($num_parent);
				}				
				$response = $frbr_cadre->get_form(true);
				break;
			case 'page':
				if($num_page) {
					$frbr_page_name = frbr_entity_common_entity_page::get_class_name_from_id($num_page);
					$frbr_page = new $frbr_page_name($num_page);
				} else {
					$frbr_page = new frbr_entity_common_entity_page();
				}
				$response = $frbr_page->get_form(true);
				break;
			default :
				if(!isset($callback)) $callback = "";
				if(!isset($cancel_callback) || !$cancel_callback) $cancel_callback = "";
				if(!isset($delete_callback)) $delete_callback = "";
				if(isset($frbr_entity_class) && $frbr_entity_class){
					$element->set_entity_class_name($frbr_entity_class);
				}
				if(isset($frbr_selected_manage) && $frbr_selected_manage){
					$element->set_manage_id($frbr_selected_manage);
				}
				if(isset($frbr_indexation_type)) {
					$element->set_indexation_type($frbr_indexation_type);
				}
				if(isset($frbr_indexation_path)) {
					$element->set_indexation_path($frbr_indexation_path);
				}
				if (!empty($num_page) && intval($num_page)) {
				    $element->add_parameter("num_page", intval($num_page));
				}
				if (isset($dom_node_id) && $dom_node_id == "sub_datasource_form") {
				    $form = $element->get_sub_form();
				} else {
				    $form = $element->get_form(true);
				}
				if(isset($filter_refresh) && $filter_refresh && isset($sort_refresh) && $sort_refresh) {
					if($element->get_entity_type()){
						$entity_class_name = "frbr_entity_".$element->get_entity_type()."_datanode";
					} else {
						$entity_class_name = "frbr_entity_common_entity_datanode";
					}
					$datanode = new $entity_class_name($element->get_num_datanode());
					$datanode->set_entity_type($element->get_entity_type());
					$datanode->set_page_from_num($num_page);
					
					if (strpos($elem, "authperso") !== false && strpos($entity_class_name, "authperso") !== false) {
					    $authperso =  preg_split("#_([\d]+)#", $elem, 0 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
					    if (!empty($authperso[1])) {
					        $datanode->set_temp_authperso_id($authperso[1]);
					    }
					}
					
					$element_form = array(
						$dom_node_id => $form,
						'datasource_filters' => $datanode->get_filters_selector(),
						'filter_form' => "",
						'datasource_sort' => $datanode->get_sort_selector(),
						'sort_form' => ""
					);
					$response = encoding_normalize::json_encode($element_form);
				} else {
					if (isset($dom_node_id) && $dom_node_id) {
						$element_form = array($dom_node_id => $form);
						$response = encoding_normalize::json_encode($element_form);
					} else {
						$response = $form;
					}
				}
				break;
		}
		break;
	case 'get_data_tree' :
		$frbr_page = new frbr_entity_common_entity_page($num_page);
		$response = $frbr_page->get_dojo_tree();
		break;
	case 'get_already_selected_filters' :
		$response = $element->get_already_selected_fields('filters');
		break;
	case 'get_already_selected_sorting' :
		$response = $element->get_already_selected_fields('sorting');
		break;
	case 'get_already_selected_backbones' :
		$response = $element->get_already_selected_fields('backbones');
		break;
	case "get_manage_form" :
		$response = $element->get_manage_forms();
		break;
	case "save_manage_form" :
		$status = $element->save_manage_forms();

		$name = '';
		$deleted = false;
		if (!empty($status["status"])) {
			switch ($quoi) {
				case 'filters':
					if($filter_delete) {
						$deleted = true;
					} else {
						$name = $element->get_managed_datas()[$quoi]["filter".$manage_id]["name"];
					}
					break;
				case 'sorting':
					if($sort_delete) {
						$deleted = true;
					} else {
						$name = $element->get_managed_datas()[$quoi]["sort".$manage_id]["name"];
					}
					break;
				case 'backbones':
					if($backbone_delete) {
						$deleted = true;
					} else {
						$name = $element->get_managed_datas()[$quoi]["backbone".$manage_id]["name"];
					}
					break;
				default:
					break;
			}
		}
		$response = encoding_normalize::json_encode(array('manage_id' => $manage_id, 'name' => $name, 'status' => $status['status'], 'deleted' => $deleted, 'message' => $status['message']));
		break;
	case "get_parameters_form" :
		switch($type){
			case 'datanode':		
				if ($id) {
					$frbr_datanode_name = frbr_entity_common_entity_datanode::get_class_name_from_id($id);
					$frbr_datanode = new $frbr_datanode_name($id);
				} else {
					$frbr_datanode = new frbr_entity_common_entity_datanode();
				}
				$response = $frbr_datanode->get_parameters_form(true);
				break; 
			case 'cadre':
				$default_view = '';
				if ($id) {
					$entity_type = frbr_entity_common_entity_datanode::get_entity_type_from_id($id);
					$frbr_cadre_name = 'frbr_entity_'.$entity_type.'_cadre';
				} elseif ($num_page) {
					$entity_type = frbr_entity_common_entity_page::get_entity_type_from_id($num_page);
					$frbr_cadre_name = 'frbr_entity_'.$entity_type.'_cadre';
					//vue par défaut quand on est sur un cadre racine associé à la page
					$default_view = 'frbr_entity_'.$entity_type.'_view';
				} else {
					$frbr_cadre_name = 'frbr_entity_common_entity_cadre';
				}				
				$frbr_cadre = new $frbr_cadre_name();
				if (isset($default_view) && $default_view) {
					$frbr_cadre->elements_used['view'] = array($default_view);
				}
				$frbr_cadre->set_page_from_num($num_page);
				$response = $frbr_cadre->get_parameters_form();
				break; 
			default:
				$response = '';
				break;
		}
		break;
	case "save_cadres_placement" :

        $num_page = ( isset($num_page) ? intval($num_page) : 0 );
		$obj_cadres = [];
		if (isset($cadres)) {
		  $json_cadres = encoding_normalize::utf8_normalize(stripslashes($cadres));
		  $obj_cadres = json_decode($json_cadres);
		}
		
		$frbr_place = new frbr_place($num_page);
		$frbr_place->set_cadres($obj_cadres);
		$response = $frbr_place->save();
		break;
	default :
		if(!isset($callback)) $callback = "";
		if(!isset($cancel_callback) || !$cancel_callback) $cancel_callback = "";
		if(!isset($delete_callback)) $delete_callback = "";
		$response = $element->get_form(true,$callback,$cancel_callback,$delete_callback);
		break;
}

if($action!="ajax"){
	ajax_http_send_response($response);
}
