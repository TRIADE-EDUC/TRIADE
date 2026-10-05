<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_frbr.class.php,v 1.18.10.1 2025/03/04 15:50:02 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/modules/module.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_items.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_item.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_datanodes.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_datanode.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_datanode_ui.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_category.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_category_ui.class.php");
require_once($include_path."/templates/modules/module_frbr.tpl.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_schemes_controler.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_entities_links.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_graph.class.php");


class module_frbr extends module{

	public function proceed_cataloging(){
		global $sub, $module_frbr_cataloging_content;
		switch($sub){
			case "schemes" :
		 		$frbr_cataloging_schemes_controler = new frbr_cataloging_schemes_controler();
		 		print $frbr_cataloging_schemes_controler->proceed();
				break;
			default :
				print $module_frbr_cataloging_content;
				break;
		}
	}

	public function proceed(){
		global $categ;
		global $module_layout_end;
		global $module_frbr_cataloging_content;

		if($categ && method_exists($this, "proceed_".$categ)) {
			$method_name = "proceed_".$categ;
			$this->{$method_name}();
		} else {
			//par defaut on affiche le catalogage frbr
			print $module_frbr_cataloging_content;
		}
		print $module_layout_end;
	}

	public function proceed_ajax_cataloging() {
		global $sub;
		global $action;
		global $form;
		global $id;
		global $type;
		global $num_datanode;
		global $msg;

		switch($sub){
			case 'entities':
				switch($action){
					case 'get_list':
						print encoding_normalize::json_encode(frbr_cataloging_items::get_items_types());
						break;
				}
				break;
			case 'datanodes' :
				switch($action) {
					case 'get_datas':
						$frbr_cataloging_datanodes = new frbr_cataloging_datanodes();
						print encoding_normalize::json_encode(array($frbr_cataloging_datanodes->get_format_data()));
						break;
					case "save_datanode":
						$frbr_cataloging_datanode = new frbr_cataloging_datanode($id);
						$frbr_cataloging_datanode->set_properties_from_form();
						$result = $frbr_cataloging_datanode->save();
						$response = "";
						if($frbr_cataloging_datanode->get_id()){
							$response = $frbr_cataloging_datanode->get_informations();
						}
						$response = array(
								'result' => $result,
								'elementId' => $frbr_cataloging_datanode->get_id(),
								'response' => $response
						);
						print encoding_normalize::json_encode($response);
						break;
					case "delete_datanode":
						$frbr_cataloging_datanode = new frbr_cataloging_datanode($id);
						$result = $frbr_cataloging_datanode->delete();
						$response = "";
						if(!$result){
							$response = $frbr_cataloging_datanode->get_error();
						}
						$response = array(
								'result' => $result,
								'elementId' => $frbr_cataloging_datanode->get_id(),
								'response' => $response
						);
						print encoding_normalize::json_encode($response);
						break;
					case "save_category" :
						$frbr_cataloging_category = new frbr_cataloging_category($id);
						$frbr_cataloging_category->set_properties_from_form();
						$result = $frbr_cataloging_category->save();
						$response = "";
						if($frbr_cataloging_category->get_id()){
							$frbr_cataloging_datanodes = new frbr_cataloging_datanodes($frbr_cataloging_category->get_id());
							$response = $frbr_cataloging_datanodes->get_format_data();
						}

						$response = array(
								'result' => $result,
								'elementId' => $frbr_cataloging_category->get_id(),
								'response' => $response
						);
						print encoding_normalize::json_encode($response);
						break;
					case "delete_category":
						$frbr_cataloging_category = new frbr_cataloging_category($id);
						$result = $frbr_cataloging_category->delete();
						$response = "";
						if(!$result){
							$response = $frbr_cataloging_category->get_error();
						}
						$response = array(
								'result' => $result,
								'elementId' => $frbr_cataloging_category->get_id(),
								'response' => $response
						);
						print encoding_normalize::json_encode($response);
						break;
				}
				break;
			case 'items':
				switch($action){
					case 'get_list':
						$frbr_cataloging_items = new frbr_cataloging_items($num_datanode);
						print encoding_normalize::utf8_normalize($frbr_cataloging_items->get_list());
						break;
				}
				break;
			case 'item':
				switch($action){
					case 'add':
						$frbr_cataloging_item = new frbr_cataloging_item($id, $type, $num_datanode);
						$reload = true;
						$message = "";
						if(!$frbr_cataloging_item->save()){
							$reload = false;
							$message = $msg['frbr_cataloging_form_already_added'];
						}
						print encoding_normalize::json_encode(
								array('message' => $message, 'reload'=> $reload)
								);
						break;
					case 'remove':
						$frbr_cataloging_item = new frbr_cataloging_item($id, $type, $num_datanode);
						$frbr_cataloging_item->delete();
						print encoding_normalize::json_encode(
								array('message' => '', 'reload'=> true)
								);
						break;
				}
				break;
			case "forms" :
				switch($action) {
					case "get_form":
						switch($form){
							case "datanode_form_tpl":
								print frbr_cataloging_datanode_ui::get_form();
								break;
							case "category_form_tpl" :
								print frbr_cataloging_category_ui::get_form();
								break;
						}
						break;
					case "get_datas":
						print encoding_normalize::json_encode(array(
							"categoryForm" => frbr_cataloging_category_ui::get_form(),
							"datanodeForm" => frbr_cataloging_datanode_ui::get_form()
						));
						break;
				}
				break;
			case 'graph':
				switch($action){
					case 'add_start_node':
						global $num_datanode, $entity_type, $entity_id, $items_list;
						if (isset($num_datanode)) {
							$graph = new frbr_cataloging_graph($num_datanode);
							$items = json_decode(stripslashes($items_list));
							$graph->set_graph_data($items);
							print encoding_normalize::json_encode($graph->get_graph_data());

						}
						break;
					case "get_entity_info" :
						global $type, $id;
						/*
                        // DB : commente car la methode correspondante ne comporte pas les memes parametres
						$entity = new frbr_cataloging_entity();
						*/
						break;
					case 'get_graph_data':
						global $num_datanode;
						if (isset($num_datanode)) {
							$graph = new frbr_cataloging_graph($num_datanode);
							print encoding_normalize::json_encode($graph->get_graph_data());
						}
						break;
					case 'set_graph_data':
						global $num_datanode, $items_list;
						if (isset($num_datanode)) {
							$graph = new frbr_cataloging_graph($num_datanode);
							$items = json_decode(stripslashes($items_list));
							$graph->set_graph_data($items);
							print encoding_normalize::json_encode($graph->get_graph_data());
						}
						break;
					case 'get_link_form':
						global $source, $target;
						$source = json_decode(stripslashes($source));
						$target = json_decode(stripslashes($target));
						print encoding_normalize::json_encode(frbr_cataloging_entities_links::get_link_form($source, $target));

						break;
					case 'add_link':
						global $source, $target, $link, $link_type;
						$linking = frbr_cataloging_entities_links::link_entities($source, $target, $link, $link_type);
						print '<textarea>'.encoding_normalize::json_encode(array('response' => $linking)).'</textarea>';
						break;

				}
				break;
		}
	}
}