<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_controler.class.php,v 1.16 2019-06-04 14:58:14 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/rdf_entities_conversion/rdf_entities_converter_controller.class.php");

class onto_contribution_controler extends onto_common_controler {
	
	protected function proceed_edit(){
		global $params;
		$this->item->set_contribution_area_form(contribution_area_form::get_contribution_area_form($this->params->sub,$this->params->form_id,$this->params->area_id,$this->params->form_uri));
				
		print $this->item->get_form("./".$this->get_base_resource()."lvl=".$this->params->lvl."&sub=".$this->params->sub."&area_id=".$this->params->area_id."&id=".$this->params->id.'&form_id='.$this->params->form_id.'&form_uri='.$this->params->form_uri);
	}
	
	public function proceed(){	
		global $msg;	
		//on affecte la proprité item par une instance si nécessaire...
		$this->init_item();
		switch($this->params->action){
			case 'push':
				print $msg["onto_contribution_push_in_progress"];
				$data = $this->proceed_push();
				print "<script type='text/javascript'>window.location = './empr.php?tab=contribution_area&lvl=contribution_area_done&last_id=".$data['id']."'</script>";
				break;
			case 'save_push' :
				print encoding_normalize::json_encode($this->proceed_push());
				break;
			case 'save' :
				print encoding_normalize::json_encode($this->proceed_save());
				break;
			case 'delete' :
				print $msg["onto_contribution_delete_in_progress"];
				$this->proceed_delete(true);
				print "<script type='text/javascript'>window.location = './empr.php?tab=contribution_area&lvl=contribution_area_list'</script>";
				break;
			case "edit_entity" :
			    $this->proceed_edit_entity();
			    break;
			default:
				parent::proceed();
				break;
		}
	}
	
	protected function proceed_push() {
		global $class_path;
		global $pmb_contribution_ws_url, $pmb_contribution_ws_username, $pmb_contribution_ws_password;
		
		$return = array();
		if ($this->params->action == "save_push") {
			$return = $this->proceed_save(false);
		}
		
		require_once($class_path.'/jsonRPCClient.php');
		
		$jsonRPC = new jsonRPCClient(stripslashes($pmb_contribution_ws_url));
		$jsonRPC->setUser(stripslashes($pmb_contribution_ws_username));
		$jsonRPC->setPwd(stripslashes($pmb_contribution_ws_password));
		
		$result = $jsonRPC->pmbesContributions_integrate_entity($this->item->get_uri());
		if (!$return) {
			$return = array("uri" => $this->item->get_uri(), "id" => $this->item->get_id());
		}
		$return["entity"] = $result;
		
		//on enregitre un triplet faisant le lien entre l'URI et l'id de l'entité créée 
		$data_store = $this->handler->get_data_store(); 
		$this->save_entity_id_in_store($result, $data_store);
		
		return $return;
	}
	
	/**
	 * On enregitre les triplets faisant le lien entre l'URI et l'id des entités créées
	 * @param array $data Tableau des entités à insérer sous la forme uri, id, children
	 * @param onto_store $data_store Store dans lequel on agit
	 */
	protected function save_entity_id_in_store($data, $data_store) {
		$query = '	select ?pmb_id where {
						<'.$data['uri'].'> pmb:identifier "'.$data["id"].'" .
						<'.$data['uri'].'> pmb:identifier ?pmb_id
					}';	
		$data_store->query($query);
			
		if (!$data_store->num_rows()) {			
			$query_insert = 'insert into <pmb> {
								<'.$data['uri'].'> pmb:identifier "'.$data["id"].'" .
							}';
			$data_store->query($query_insert);
		}
		if (count($data['children'])) {
			foreach ($data['children'] as $child) {
				$this->save_entity_id_in_store($child, $data_store);
			}
		}
	}
	
	protected function proceed_save($list = true){
		$this->item->get_values_from_form();
	
		$result = $this->handler->save($this->item);
		if($result !== true){
			$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
			$ui_class_name::display_errors($this,$result);
		}else {
			$display_label = $this->item->get_label($this->handler->get_display_labels($this->handler->get_class_uri($this->params->sub)));
			return array("uri" => $this->item->get_uri(), "displayLabel" => $display_label, "id" => $this->item->get_id());
		}
	}

	protected function proceed_delete($force_delete = false){
		$result = $this->handler->delete($this->item,$force_delete);
	}
	
	protected function proceed_edit_entity(){
	    global $params;
	    $this->item->set_contribution_area_form(contribution_area_form::get_contribution_area_form($this->params->sub,$this->params->form_id,$this->params->area_id,$this->params->form_uri));
	    $this->item->set_assertions(rdf_entities_converter_controller::convert($this->params->id, $this->params->sub));
	    print $this->item->get_form("./".$this->get_base_resource()."lvl=".$this->params->lvl."&sub=".$this->params->sub."&area_id=".$this->params->area_id."&id=".$this->params->id.'&form_id='.$this->params->form_id.'&form_uri='.$this->params->form_uri);
	}
	
	protected function init_item() {
	    if (!intval($this->params->id)) {
	        $this->params->id = onto_common_uri::get_id($this->params->id);
	    }
	    switch ($this->params->action) {
	        case 'edit_entity':
	            $this->params->item_uri = 'http://www.pmbservices.fr/ontology/'.$this->params->type.'#'.$this->params->id;
	            $this->item = $this->handler->get_item($this->handler->get_class_uri($this->params->sub), $this->params->item_uri);
	            break;
	        default:
	            parent::init_item();
	            break;
	    }
	}
}