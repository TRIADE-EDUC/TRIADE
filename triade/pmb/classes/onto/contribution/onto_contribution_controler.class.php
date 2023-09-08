<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_controler.class.php,v 1.12 2019-06-04 14:58:14 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

require_once ($class_path . "/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("rdf_entities_integration", true);
class onto_contribution_controler extends onto_common_controler {
	
	// protected function proceed_edit(){
	// $this->item->set_contribution_area_form(new contribution_area_form($this->params->sub,$this->params->form_id,$this->params->area_id,$this->params->form_uri));
	
	// print $this->item->get_form("./".$this->get_base_resource()."lvl=".$this->params->lvl."&sub=".$this->params->sub."&area_id=".$this->params->area_id."&id=".$this->params->id.'&form_id='.$this->params->form_id);
	// }
	protected function proceed_grid() {
		$this->item->set_contribution_area_form(new contribution_area_form($this->params->sub, $this->params->form_id, $this->params->area_id, $this->params->form_uri));
		print $this->item->get_grid("./" . $this->get_base_resource() . "categ=" . $this->params->lvl . "&sub=" . $this->params->sub, "", "");
	}

	public function proceed() {
		global $msg;
		// on affecte la proprité item par une instance si nécessaire...
		$this->init_item();
		switch ($this->params->action) {
			case 'grid' :
				$this->proceed_grid();
				break;
			case 'edit' :
				$this->proceed_edit();
				break;
			case 'push' :
				print $msg["onto_contribution_push_in_progress"];
				$data = $this->proceed_push();
				print "<script type='text/javascript'>window.location = './catalog.php?categ=contribution_area&action=list'</script>";
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
				print "<script type='text/javascript'>window.location = './catalog.php?categ=contribution_area&action=list'</script>";
				break;
			case 'edit_entity' :
			    $this->proceed_edit_entity();
			    break;
			default :
				parent::proceed();
				break;
		}
	}

	protected function init_item() {
	    switch ($this->params->action) {
	        case 'grid':
	            $this->item = $this->handler->get_item($this->handler->get_class_uri($this->params->type), $this->params->item_uri);
	            break;
	        case 'edit_entity':
	            $this->item = $this->handler->get_item($this->handler->get_class_uri($this->params->sub), $this->params->item_uri);
	            break;
// 	            $this->item = new onto_contribution_item($this->handler->get_class_uri($this->params->sub),$this->params->item_uri);
	        default:
	            parent::init_item();
	            break;
	    }
	}

	protected function proceed_edit() {
		global $params;
		$this->item->set_contribution_area_form(contribution_area_form::get_contribution_area_form($this->params->sub, $this->params->form_id, $this->params->area_id, $this->params->form_uri));
		
		print $this->item->get_form("./" . $this->get_base_resource() . "lvl=" . $this->params->lvl . "&sub=" . $this->params->sub . "&area_id=" . $this->params->area_id . "&id=" . $this->params->id . '&form_id=' . $this->params->form_id . '&form_uri=' . $this->params->form_uri);
	}

	protected function proceed_edit_entity() {
		global $params;
		$this->item->set_contribution_area_form(contribution_area_form::get_contribution_area_form($this->params->sub, $this->params->form_id, $this->params->area_id, $this->params->form_uri));
		$this->item->set_assertions($this->params->assertions);
		print $this->item->get_form("./" . $this->get_base_resource() . "lvl=" . $this->params->lvl . "&sub=" . $this->params->sub . "&area_id=" . $this->params->area_id . "&id=" . $this->params->id . '&form_id=' . $this->params->form_id . '&form_uri=' . $this->params->form_uri);
	}

	protected function proceed_push() {
		global $class_path;
		
		$return = array();
		if ($this->params->action == "save_push") {
			$return = $this->proceed_save(false);
		}
		
		$config = array(
				'store_name' => 'contribution_area_datastore'
		);
		$rdf_entities_integrator = new rdf_entities_integrator(new rdf_entities_store_arc2($config));
		$result = $rdf_entities_integrator->integrate_entity($this->item->get_uri());
		
		$result = encoding_normalize::utf8_normalize($result);
		
		if (! $return) {
			$return = array(
					"uri" => $this->item->get_uri(),
					"id" => $this->item->get_id()
			);
		}
		$return["entity"] = $result;
		
		// on enregitre un triplet faisant le lien entre l'URI et l'id de l'entité créée
		$data_store = $this->handler->get_data_store();
		$this->save_entity_id_in_store($result, $data_store);
		
		return $return;
	}

	/**
	 * On enregitre les triplets faisant le lien entre l'URI et l'id des entités créées
	 *
	 * @param array $data
	 *        	Tableau des entités à insérer sous la forme uri, id, children
	 * @param onto_store $data_store
	 *        	Store dans lequel on agit
	 */
	protected function save_entity_id_in_store($data, $data_store) {
		$query = '	select ?pmb_id where {
						<' . $data['uri'] . '> pmb:identifier "' . $data["id"] . '" .
						<' . $data['uri'] . '> pmb:identifier ?pmb_id
					}';
		$data_store->query($query);
		
		if (! $data_store->num_rows()) {
			$query_insert = 'insert into <pmb> {
			<' . $data['uri'] . '> pmb:identifier "' . $data["id"] . '" .
						}';
			$data_store->query($query_insert);
		}
		if (count($data['children'])) {
			foreach($data['children'] as $child) {
				$this->save_entity_id_in_store($child, $data_store);
			}
		}
	}

	protected function proceed_save($list = true) {
		$this->item->get_values_from_form();
		
		$result = $this->proceed_handler_save($this->item);
		if ($result !== true) {
			$ui_class_name = self::resolve_ui_class_name($this->params->sub, $this->handler->get_onto_name());
			return array(
			    "errors" => $ui_class_name::display_errors($this, $result, true)
			);
		} else {
			$display_label = $this->item->get_label($this->handler->get_display_labels($this->handler->get_class_uri($this->params->sub)));
			return array(
					"uri" => $this->item->get_uri(),
					"displayLabel" => $display_label,
					"id" => $this->item->get_id()
			);
		}
	}

	protected function proceed_delete($force_delete = false, $print = true) {
		$result = $this->handler->delete($this->item, $force_delete);
	}

	protected function proceed_handler_save($item) {
		global $opac_url_base, $area_id, $action;
		
		if ($item->check_values()) {
			if (onto_common_uri::is_temp_uri($item->get_uri())) {
				$item->replace_temp_uri();
			}
			$assertions = $item->get_assertions();
			$nb_assertions = count($assertions);
			$i = 0;
			
			$subjects_deleted = array();
			
			// On peut y aller
			$query = "insert into <pmb> {
				";
			foreach($assertions as $assertion) {
				if (! in_array($assertion->get_subject(), $subjects_deleted)) {
					$pmb_id = 0;
					
					// on stocke l'id de l'entité en base SQL s'il existe
					$query_pmb_id = '	select ?pmb_id where {
						<' . $assertion->get_subject() . '> pmb:identifier ?pmb_id
					}';
					$this->handler->data_query($query_pmb_id);
					if ($this->handler->data_num_rows()) {
						$pmb_id = $this->handler->data_result()[0]->pmb_id;
					}
					
					// On supprime tous les triplets correspondant à cette uri pour les mettre à jour par la suite
					$query_delete = "delete {
						<" . $assertion->get_subject() . "> ?prop ?obj
						}";
					$this->handler->data_query($query_delete);
					
					$subjects_deleted[] = $assertion->get_subject();
					
					// puis on commence par ré-insèrer l'id de l'entité en base SQL dans le store
					if ($pmb_id) {
						if (!$this->handler->data_num_rows()) {
							$query_insert = 'insert into <pmb> {
									<' . $assertion->get_subject() . '> pmb:identifier "' . $pmb_id . '" .
							}';
							$this->handler->data_query($query_insert);
						}
					}
				}
				
				if ($assertion->offset_get_object_property("type") == "literal") {
					$object = "'" . addslashes($assertion->get_object()) . "'";
					$object_properties = $assertion->get_object_properties();
					if (!empty($object_properties['lang'])) {
						$object .= "@" . $object_properties['lang'];
					}
				} else {
					
					$object = "<" . addslashes($assertion->get_object()) . ">";
					
					if ($assertion->offset_get_object_property("type") == "uri") {
						
						if ($assertion->get_object_type()) {
							
							if (is_numeric($assertion->get_object())) {
								
								$uri = "<" . addslashes($opac_url_base . $this->handler->get_class_pmb_name($assertion->get_object_type()) . '#' . $assertion->get_object()) . ">";
								$object = $uri;
								
								// on teste si le triplet n'existe pas déjà
								$query_bis = "	select ?object_type where {
										" . $uri . " <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <" . addslashes($assertion->get_object_type()) . "> .
										" . $uri . " <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?object_type
												}";
								$this->handler->data_query($query_bis);
								
								if (!$this->handler->data_num_rows()) {
									
									$object .= " .\n";
									// sujet
									$object .= $uri;
									// prédicat
									$object .= ' pmb:identifier ';
									// objet
									$object .= '"' . addslashes($assertion->get_object()) . '"';
									
									$object .= " .\n";
									// sujet
									$object .= $uri;
									// prédicat
									$object .= ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ';
									// objet
									$object .= '<' . addslashes($assertion->get_object_type()) . '>';
									
									if ($assertion->get_object_properties()['display_label']) {
										
										$object .= " .\n";
										// sujet
										$object .= $uri;
										// prédicat
										$object .= ' pmb:displayLabel ';
										// objet
										$object .= '"' . $assertion->get_object_properties()['display_label'] . '"';
									}
								}
							}
						}
					}
				}
				$query .= "<" . addslashes($assertion->get_subject()) . "> <" . addslashes($assertion->get_predicate()) . "> " . $object;
				
				if ($area_id && ! $i) {
					$query .= " .\n <" . addslashes($assertion->get_subject()) . "> pmb:area " . $area_id;
				}
				
				// on ne rentre qu'une seule, afin de ne pas écraser le display label
				if ($assertion->get_object_properties()['type'] == "uri" && ! $i) {
					$display_label = $item->get_label($this->handler->get_display_label($assertion->get_object()));
					$query .= " .\n <" . addslashes($assertion->get_subject()) . "> pmb:displayLabel '" . addslashes($display_label) . "'";
				}
				
				$i++;
				if ($i < $nb_assertions) {
					$query .= " .";
				}
				
				$query .= "\n";
			}
			
			$query .= "}";
			
			if ($this->handler->data_query($query)) {
				$onto_index = onto_index::get_instance($this->get_onto_name());
				$onto_index->set_handler($this->handler);
				$onto_index->maj(0, $item->get_uri());
			}
		} else {
			return $item->get_checking_errors();
		}
		return true;
	} // end of member function save
}