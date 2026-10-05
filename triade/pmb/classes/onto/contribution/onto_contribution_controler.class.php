<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_controler.class.php,v 1.19 2022/04/15 12:16:07 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

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
		global $from_gestion;

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
		
		if (!empty($return) && !empty($return['id'])) {
    		//on envoie le mail d'info lorsque l'on enregistre la contrib, seulement lorsque c'est un modérateur depuis la gestion
    		if ($this->params->action == 'push' && $from_gestion){
    		    contribution_area_forms_controller::mail_empr_contribution_validate($this->item->get_uri());
    		}
    		
		    // Une fois la contribution validé on store plus aucune donner dans le store
		    $this->proceed_delete(true);
		}
		
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
		if (!empty($data['children']) && count($data['children'])) {
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
	    global $ajax;
		$result = $this->handler->delete($this->item, $force_delete);
		if ($this->item->onto_class->pmb_name == "docnum") {
		    $this->item->remove_file_uploads();
		}
		if ($ajax){
		    return $result;
		}
	}

	protected function proceed_handler_save($item) {
		global $opac_url_base, $area_id, $action;
		
		if ($item->check_values()) {
			if (onto_common_uri::is_temp_uri($item->get_uri())) {
				$item->replace_temp_uri();
			}
			$assertions = $item->get_assertions();

			// On peut y aller
			$query = "insert into <pmb> {";
			$query .= $this->build_triples($assertions, $item->get_uri());
			$query .= ".\n <".addslashes($assertions[0]->get_subject())."> pmb:area '".intval($area_id)."'";
			
			//on ne rentre qu'une seule, afin de ne pas écraser le display label
			if($assertions[0]->get_object_properties()['type'] == "uri") {
			    $display_label = $item->get_label($this->handler->get_display_label($assertions[0]->get_object()));
			    
			    //si pas de display label, on va chercher celui du parent
			    if (!$display_label) {
			        $sub_class_of = $this->ontology->get_sub_class_of($assertions[0]->get_object());
			        foreach ($sub_class_of as $parent_uri) {
			            $display_label = $item->get_label($this->handler->get_display_label($parent_uri));
			            if ($display_label) {
			                break;
			            }
			        }
			    }
			    $query .= " .\n <".addslashes($assertions[0]->get_subject())."> pmb:displayLabel '".addslashes($display_label)."'";
			}
			$query.="}";
			
			$result = $this->handler->data_query($query);
			if ($result) {
			    $item->post_save();
			    //TODO: a reprendre plus tard si besoin (indexation des contribution par exemple...)
			    if ($this->handler->get_onto_name() == "skos") {
    				$onto_index = onto_index::get_instance($this->get_onto_name());
    				$onto_index->set_handler($this->handler);
    				$onto_index->maj(0, $item->get_uri());
			    }
			} else {
			    return $result;
			}
		} else {
			return $item->get_checking_errors();
		}
		return true;
	} // end of member function save
	
	private function build_triples($assertions, $main_uri) {
	    global $opac_url_base;
	    
	    $nb_assertions = count($assertions);
	    $i = 0;
	    
	    $subjects_deleted = array();
	    
	    // On peut y aller
	    $query = "";
	    foreach ($assertions as $assertion) {
	        if (!in_array($assertion->get_subject(), $subjects_deleted)) {
	            $pmb_id = 0;
	            
	            //on stocke l'id de l'entité en base SQL s'il existe
	            $query_pmb_id = '	select ?pmb_id where {
						<'.$assertion->get_subject().'> pmb:identifier ?pmb_id
					}';
	            $this->handler->data_query($query_pmb_id);
	            if ($this->handler->data_num_rows()) {
	                $pmb_id = $this->handler->data_result()[0]->pmb_id;
	            }
	            
	            // On supprime tous les triplets correspondant à cette uri pour les mettre à jour par la suite
	            if ($assertion->get_subject() == $main_uri) {
	                $query_delete = "delete {
    						<".$assertion->get_subject()."> ?prop ?obj
    						}";
	                $this->handler->data_query($query_delete);
	                
	                $subjects_deleted[] = $assertion->get_subject();
	            } else {
	                $query_delete = "delete {
    						<".$assertion->get_subject()."> <".$assertion->get_predicate()."> <".$assertion->get_object().">
    						}";
	                $this->handler->data_query($query_delete);
	            }
	            
	            //puis on commence par ré-insèrer l'id de l'entité en base SQL dans le store
	            if ($pmb_id) {
	                if (!$this->handler->data_num_rows()) {
	                    $query_insert = 'insert into <pmb> {
									<'.$assertion->get_subject().'> pmb:identifier "'.$pmb_id.'" .
								}';
	                    $this->handler->data_query($query_insert);
	                }
	            }
	        }
	        
	        if ($assertion->offset_get_object_property("type") == "literal"){
	            $object = "'".addslashes($assertion->get_object())."'";
	            $object_properties = $assertion->get_object_properties();
	            if (!empty($object_properties['lang'])) {
	                $object.="@".$object_properties['lang'];
	            }
	        }else{
	            
	            if (empty($assertion->get_object())) {
	                // Aucune uri
	                $object = "''";
	            } else {
	                $object = "<".addslashes($assertion->get_object()).">";
	                if ($assertion->offset_get_object_property("type") == "uri"){
	                    
	                    if ($assertion->get_object_type()) {
	                        if (is_numeric($assertion->get_object())) {
	                            $query_bis = "	select ?uri where {
    													?uri pmb:identifier '".addslashes($assertion->get_object())."' .
    													?uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <".addslashes($assertion->get_object_type()).">
    												}";
	                            $this->handler->data_query($query_bis);
	                            if (!$this->handler->data_num_rows()) {
	                                
	                                $uri = "<" . addslashes($opac_url_base . $this->handler->get_class_pmb_name($assertion->get_object_type()) . '#' . $assertion->get_object()) . ">";
	                                $object = $uri;
	                                
	                                $object .= " .\n";
	                                //sujet
	                                $object .= $uri;
	                                //prédicat
	                                $object .= ' pmb:identifier ';
	                                //objet
	                                $object .= '"'.addslashes($assertion->get_object()).'"';
	                                
	                                $object .= " .\n";
	                                //sujet
	                                $object .= $uri;
	                                //prédicat
	                                $object .= ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ';
	                                //objet
	                                $object .= '<'.addslashes($assertion->get_object_type()).'>';
	                                
	                                if ($assertion->offset_get_object_property('display_label')) {
	                                    $object .= " .\n";
	                                    //sujet
	                                    $object .= $uri;
	                                    //prédicat
	                                    $object .= ' pmb:displayLabel ';
	                                    //objet
	                                    $object .= '"'.addslashes($assertion->offset_get_object_property('display_label')).'"';
	                                }
	                                $uri = "";
	                            } else {
	                                $uri = $this->handler->data_result()[0]->uri;
	                                $object = "<".$uri.">";
	                            }
	                        }
	                        if ($assertion->offset_get_object_property('object_assertions')) {
	                            
	                            $object .= " .\n";
	                            //sujet
	                            $object .= '<'.addslashes($assertion->get_object()).'>';
	                            //prédicat
	                            $object .= ' pmb:has_assertions ';
	                            //objet
	                            $object .= '"1"';
	                            
	                            $object .= " .\n";
	                            //sujet
	                            $object .= '<'.addslashes($assertion->get_object()).'>';
	                            //prédicat
	                            $object .= ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ';
	                            //objet
	                            $object .= "<".addslashes($assertion->get_object_type())."> .\n";
	                            
	                            $object .= $this->build_triples($assertion->offset_get_object_property('object_assertions'),$assertion->get_object());
	                        } else {
	                            
	                            // On essaie de recuperer le display label supprime suite à une purge des stores
	                            if (!is_numeric($assertion->get_object())) {
	                                $uri = addslashes($assertion->get_object());
	                            }
	                            
	                            if (!empty($uri)) {
	                                $query_bis = "select ?displayLabel where {
                    	                               <".$uri."> pmb:displayLabel ?displayLabel .
                                                    }";
	                                
	                                $this->handler->data_query($query_bis);
	                                if (!$this->handler->data_num_rows()) {
	                                    if ($assertion->offset_get_object_property('display_label')) {
	                                        $object .= " .\n";
	                                        //sujet
	                                        $object .= "<".$uri.">";
	                                        //prédicat
	                                        $object .= ' pmb:displayLabel ';
	                                        //objet
	                                        $object .= '"'.addslashes($assertion->offset_get_object_property('display_label')).'"';
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        $query.= " <".addslashes($assertion->get_subject())."> <".addslashes($assertion->get_predicate())."> ".$object;
	        
	        $i++;
	        if ($i < $nb_assertions) {
	            $query.=" .\n";
	        }
	    }
	    return $query;
	}
}