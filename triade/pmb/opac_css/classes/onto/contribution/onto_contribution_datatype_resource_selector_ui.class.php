<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_resource_selector_ui.class.php,v 1.65.2.1.2.1 2025/01/30 09:08:05 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_resource_selector_ui.class.php';

/**
 * class onto_contribution_datatype_resource_selector_ui
 * 
 */
class onto_contribution_datatype_resource_selector_ui extends onto_common_datatype_resource_selector_ui {
	
	/**
	 *
	 *
	 * @param onto_common_property $property la propriété concernée
	 * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag
	
	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag) {
		global $charset, $ontology_tpl, $area_id, $from_record;
        $form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
		/** traitement initial du range ?!*/
		$range_for_form = "";
		if(is_array($property->range)){
			foreach($property->range as $range){
				if($range_for_form) $range_for_form.="|||";
				$range_for_form.=$range;
			}
		} else {
			$range_for_form = $property->range;
		}
	
		$content = '';
		$add_button = '';
		if ($restrictions->get_max() === -1) {			
			$add_button = $ontology_tpl['form_row_content_input_add_resource_selector'];
		}
		
		$content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);
		
		if (!empty($datas)) {
			$new_element_order = max(array_keys($datas));
			$form = str_replace("!!onto_new_order!!", $new_element_order, $form);
			foreach($datas as $key => $data) {
			    
				if ($data->get_order()) {
					$order = $data->get_order();
				} else {
					$order = $key;
				}
				
				$management_data = $data->get_management_data();
				
				if (!isset($management_data['is_draft'])) {
				    $management_data['is_draft'] = "0";
				}
				if($data->get_value_type() == 'http://www.pmbservices.fr/ontology#record' && isset($from_record)){
				    $property->pmb_extended['readonly'] = true;
				}
				$row = self::get_template($item_uri, $property, $range, $order, $data, $management_data['is_draft']);
				$row = str_replace("!!onto_row_inputs!!", (empty($property->pmb_extended['readonly']) ? self::get_inputs($item_uri, $property, $order, $add_button, $management_data, $new_element_order) : ""), $row);
				$row = str_replace("!!onto_row_resource_selector!!", $ontology_tpl['form_row_content_resource_template'], $row);
				$row = str_replace("!!onto_row_order!!", $order, $row);
	
				$content .= $row;
			}
		} else {
		    $order = 0;
		    
		    $form = str_replace("!!onto_new_order!!", $order, $form);
		    
			$row = self::get_template($item_uri, $property, $range, $order);
			$row = str_replace("!!onto_row_inputs!!", ($property->pmb_extended['readonly'] == false ?  self::get_inputs($item_uri, $property, $order, $add_button) : ""), $row);
			$row = str_replace("!!onto_row_resource_selector!!", $ontology_tpl['form_row_content_resource_template'], $row);
			$row = str_replace("!!onto_row_order!!", $order, $row);
			
			$content .= $row;
		}	
	
		$form = str_replace("!!onto_rows!!", $content, $form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_completion!!", self::get_completion_from_range($range_for_form), $form);	
		$form = str_replace("!!onto_equation_query!!", htmlentities(static::get_equation_query($property),ENT_QUOTES,$charset), $form);	
		$form = str_replace("!!onto_area_id!!", ($area_id ? $area_id : ''), $form);		
		$form = self::get_form_with_special_properties($property, $datas, $instance_name, $form);	
		$form = str_replace("!!onto_row_id!!", $instance_name.'_'.$property->pmb_name, $form);
		return $form;
	} // end of member function get_form
	
	/**
	 * 
	 * @param onto_common_property $property
	 * @return string
	 */
	protected static function get_equation_query($property) {	
		if(empty($property->pmb_extended['equation'])) {
			return '';
		}
		$query = "SELECT contribution_area_equation_query FROM contribution_area_equations WHERE contribution_area_equation_id='".$property->pmb_extended['equation']."'";
		
		
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			return $row->contribution_area_equation_query;			
		}	
		return '';
	}
	
	public static function get_hidden_fields($property,$datas, $instance_name, $flag = false) {
		global $charset, $ontology_tpl; 
		global $origin, $origin_uri;
		
		$form=$ontology_tpl['form_row_hidden'];
		
		$content='';
		$value = "";
		if (!empty($origin) && $property->range[0] == $origin && !empty($origin_uri)) {
		   $value = $origin_uri; 
		}
		
		if(is_countable($datas) && sizeof($datas)){	
			
			$new_element_order=max(array_keys($datas));
			
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
						
			foreach($datas as $key=>$data){
				$row=$ontology_tpl['form_row_content_resource_selector_hidden'];
				$formated_value = $data->get_formated_value();
				if (isset($formated_value['record']['value'])) {
					$value = $formated_value['record']['value'];
				}
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				$row=str_replace("!!onto_row_content_hidden_display_label!!",htmlentities(addslashes($formated_value) ,ENT_QUOTES,$charset) ,$row);
				if ($key == 0 && $value) {
    				$row=str_replace("!!onto_row_content_hidden_value!!",htmlentities($value ,ENT_QUOTES,$charset) ,$row);
				} else {
				    $row=str_replace("!!onto_row_content_hidden_value!!",htmlentities($data->get_raw_value() ,ENT_QUOTES,$charset) ,$row);
				}
				$row = str_replace("!!onto_row_content_hidden_is_draft!!", "0", $row);
				$row=str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
				$row = str_replace("!!onto_row_content_hidden_assertions!!", htmlentities($data->json_encode_assertions(), ENT_QUOTES, $charset), $row);
				$row=str_replace("!!onto_row_order!!",$order , $row);
		
				$content.=$row;
			}
		} else {
			$form=str_replace("!!onto_new_order!!","0" , $form);
			$row = $ontology_tpl['form_row_content_resource_selector_hidden'];
			$row = str_replace("!!onto_row_content_hidden_display_label!!", "", $row);
			$row = str_replace("!!onto_row_content_hidden_value!!", htmlentities($value ,ENT_QUOTES,$charset), $row);
			$row = str_replace("!!onto_row_content_hidden_is_draft!!", "0", $row);
			$row = str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
			$row = str_replace("!!onto_row_content_hidden_assertions!!","" , $row);
			$row=str_replace("!!onto_row_order!!","0" , $row);
			$content.=$row;
		}
		
		if ($flag) {
			$form=$content;
		} else {
			$form=str_replace("!!onto_rows!!",$content ,$form);
		}
				
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
	}
	
	protected static function get_completion_from_range($range) {
	    //on récupère le type de range en enlevant le préfixe propre à l'ontologie
	    switch ($range) {
	        case 'http://www.pmbservices.fr/ontology#linked_record' :
	        case 'http://www.pmbservices.fr/ontology#record' :
	            return 'notice';
	        case 'http://www.pmbservices.fr/ontology#author' :
	        case 'http://www.pmbservices.fr/ontology#responsability' :
	            return 'authors';
	        case 'http://www.pmbservices.fr/ontology#category' :
	            return 'categories';
	        case 'http://www.pmbservices.fr/ontology#publisher' :
	            return 'publishers';
	        case 'http://www.pmbservices.fr/ontology#collection' :
	            return 'collections';
	        case 'http://www.pmbservices.fr/ontology#subcollection' :
	        case 'http://www.pmbservices.fr/ontology#sub_collection' :
	            return 'subcollections';
	        case 'http://www.pmbservices.fr/ontology#serie' :
	            return 'serie';
	        case 'http://www.pmbservices.fr/ontology#work' :
	            return 'titres_uniformes';
	        case 'http://www.pmbservices.fr/ontology#indexint' :
	            return 'indexint';
	        case 'http://www.w3.org/2004/02/skos/core#Concept' :
	            return 'concepts';
	        case 'http://www.w3.org/2004/02/skos/core#ConceptScheme' :
	            return 'onto';
	        case 'http://www.pmbservices.fr/ontology#bulletin':
	            return 'bull';
	        case 'http://www.pmbservices.fr/ontology#expl':
	            return 'expl';
	        default:
	            if(strpos($range,'http://www.pmbservices.fr/ontology#authperso') !== false){
	                return 'authperso_'.explode('_',$range)[1];
	            }
	            return'';
	    }
	}
	
	public static function get_type_from_range($range) {
	    switch ($range) {
	        case 'http://www.pmbservices.fr/ontology#linked_record' :
	        case 'http://www.pmbservices.fr/ontology#record' :
	            return 'record';
	        case 'http://www.pmbservices.fr/ontology#author' :
	        case 'http://www.pmbservices.fr/ontology#responsability' :
	            return 'author';
	        case 'http://www.pmbservices.fr/ontology#category' :
	            return 'category';
	        case 'http://www.pmbservices.fr/ontology#publisher' :
	            return 'publisher';
	        case 'http://www.pmbservices.fr/ontology#collection' :
	            return 'collection';
	        case 'http://www.pmbservices.fr/ontology#sub_collection' :
	        case 'http://www.pmbservices.fr/ontology#subcollection' :
	            return 'subcollection';
	        case 'http://www.pmbservices.fr/ontology#serie' :
	            return 'serie';
	        case 'http://www.pmbservices.fr/ontology#work' :
	        case 'http://www.pmbservices.fr/ontology#linked_work' :
	            return 'work';
	        case 'http://www.pmbservices.fr/ontology#indexint' :
	            return 'indexint';
	        case 'http://www.w3.org/2004/02/skos/core#Concept' :
	            return 'concept';
	        case 'http://www.pmbservices.fr/ontology#bulletin':
	            return 'bull';
	        case 'http://www.pmbservices.fr/ontology#serial' :
	            return 'serial';
	        case 'http://www.pmbservices.fr/ontology#docnum' :
	            return 'docnum';
	        case 'http://www.pmbservices.fr/ontology#expl' :
	            return 'expl';
	        default:
	            if(strpos($range,'http://www.pmbservices.fr/ontology#authperso') !== false){
    	            return 'authperso_'.explode('_',$range)[1];
	            }
	            return '';
	    }
	}
	
	public static function get_edit_url($entity, $scenario_id, $type, $params = array()) {
	    
        $uri = $entity['value'] ?? ($entity['uri'] ?? '');
        
        if (empty($params['id'])) {
    	    $params['id'] = onto_common_uri::get_id($uri);
        }
	    
	    if (!empty($params['is_entity'])) {
	        $params['id'] = $entity['value'] ?? $params['id'];
        }
	    $params['create'] = 1;
	    $params['scenario'] = $scenario_id ?? 0;
	    $params['area_id'] = $entity['area_id'] ?? ($entity['http://www.pmbservices.fr/ontology#area'] ?? 0);
	    $params['form_id'] = $entity['form_id'] ?? ($entity['http://www.pmbservices.fr/ontology#form_id'] ?? 0);
	    $params['form_uri'] = $entity['form_uri'] ?? ($entity['http://www.pmbservices.fr/ontology#form_uri'] ?? 0);
	    $params['edit_contribution'] = 1;
	    $params['select_tab'] = 1;
	    $params['type'] = $type;
	    
	    $json_data = encoding_normalize::json_encode($params);
	    return "./select.php?what=contribution&selector_data=".urlencode($json_data);
	}
	
	/**
	 * Retourne le template pour une ligne
	 *
	 * @param string $item_uri
	 * @param onto_common_property $property
	 * @param string $range
	 * @param string|int $order
	 * @param array $data
	 * @param string|boolean $is_draft
	 * @return mixed
	 */
	private static function get_template($item_uri, $property, $range, $order, $data = array(), $is_draft = false) 
	{
	    global $ontology_tpl, $charset;
	    
	    $row = $ontology_tpl['form_row_content_with_flex'];
	    $template = $ontology_tpl['form_row_content_resource_selector'];
	    $template .= $ontology_tpl['form_row_content_type'];
	    
	    $label = "";
	    $value = "";
	    $value_type = $range;
	    if (!empty($data)) {
    	    $label = addslashes($data->get_formated_value());
    	    $value = $data->get_value();
    	    $value_type = $data->get_value_type() ?? $range;
	    }
	    
	    $template = str_replace("!!form_row_content_resource_selector_display_label!!", htmlentities($label, ENT_QUOTES, $charset), $template);
	    $template = str_replace("!!form_row_content_resource_selector_value!!", $value, $template);
	    $template = str_replace("!!form_row_content_resource_selector_is_draft!!", ($is_draft ? $is_draft : "0"), $template);
	    $template = str_replace("!!onto_row_content_range!!", $value_type, $template);
	    $template = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri), $template);
	    $template = str_replace("!!onto_current_range!!", $range, $template);
	    
	    $row = str_replace("!!onto_row_is_draft!!", ($is_draft ? 'contribution_draft' : ''), $row);
	    $row = str_replace("!!onto_inside_row!!", $template, $row);
	    
	    return $row;
	}
	
	/**
	 * Retourne les bouttons rechercher, créer et modifiés
	 *
	 * @param string $item_uri
	 * @param onto_common_property $property
	 * @param string|int $order
	 * @param string $add_button
	 * @param array $formated_value
	 * @param string|int $new_element_order
	 * @return string
	 */
	protected static function get_inputs($item_uri, $property, $order, $add_button = "", $management_data = array(), $new_element_order = 0) 
	{
	    global $ontology_tpl, $charset;
	    
	    global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
	    if (($gestion_acces_active == 1) && ($gestion_acces_empr_contribution_scenario == 1)) {
	        $ac = new acces();
	        $dom_5 = $ac->setDomain(5);
	    }
	    
	    $input='';
	    
	    $input .= $ontology_tpl['form_row_content_input_remove'];
	    if (!$property->is_no_search()) {
	        $input .= $ontology_tpl['form_row_content_search'];
	    }
	    
	    $params = [];
	    $params['type'] = self::get_type_from_range($property->range[0]);
	    $params['sub_form'] = 1;
	    $params['sub_form_data'] = [];
	    $params['is_draft'] = $property->is_draft ?? 0;
	    $params['is_entity'] =  $property->is_entity ? true : false;
	    $params['equation'] = static::get_equation_query($property);
	    
	    if ($property->has_linked_form) {
	        $linked_forms = false;
	        
	        if ($property->is_entity && !empty($management_data['value'])) {
	            $linked_forms = true;
	            // On définis des valeurs par défaut
	            $management_data['form_uri'] = $property->linked_forms[0]['form_id_store'];
	            $management_data['form_id'] = $property->linked_forms[0]['form_id'];
	            $management_data['area_id'] = $property->linked_forms[0]['area_id'];
	        } else {
	            foreach ($property->linked_forms as $linked_form){
	                if (!empty($management_data['form_uri']) && $linked_form['form_id_store'] == $management_data['form_uri']) {
	                    $linked_forms = true;
	                    break;
	                }
	                if (isset($linked_form["form_type"])) {
	                    if (!isset($params['sub_form_data'][$linked_form["form_type"]])) {
    	                    $params['sub_form_data'][$linked_form["form_type"]] = [
    	                        "type" => $linked_form["form_type"], 
    	                        "multiple" => 0,
    	                    ];
	                    } else {
	                        $params['sub_form_data'][$linked_form["form_type"]]["multiple"] = 1;
	                    }
	                }
	            }
	        }
	        
	        //Onglet modifier
	        if ($linked_forms && $management_data['value']) {
	            //Onglet modifier
	            $input .= $ontology_tpl['form_row_content_edit'];
	            $url = static::get_edit_url($management_data, $property->linked_forms[0]['scenario_id'], $params['type'], $params) ;
	            $input = str_replace("!!url_edit_form!!", $url, $input);
	        } else {
	            $input .= $ontology_tpl['form_row_content_edit_hidden'];
	        }
	        
	        $params['edit_contribution'] = 0;
	        $access_granted = true;
	        
	        if (onto_common_uri::is_temp_uri($item_uri)) {
	            //droit de creation
	            $acces_right = 4;
	        } else {
	            //droit de modification
	            $acces_right = 8;
	        }
	        
	        if (isset($dom_5)) {
	            $access_granted = false;
	            $length = count($property->linked_forms);
	            for ($i = 0; $i < $length; $i++) {
	                if ($dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id($property->linked_forms[$i]['scenario_uri']), $acces_right)) {
	                    // Si on a les droits pour un scenario on autorise les accès.
	                    $access_granted = true;
	                    break;
	                }
	            }
	        }
	        if ($access_granted) {
	            
	            if ($linked_forms && !empty($management_data['value']) ) {
	                $input .= $ontology_tpl['form_row_content_hidden_linked_form'];
	            } else {
    	            $input .= $ontology_tpl['form_row_content_linked_form'];
	            }
	            
	            $params['area_id'] = $property->linked_forms[0]['area_id'] ?? 0;
	            $params['id'] = 0;
	            $params['form_id'] = $property->linked_forms[0]['form_id'] ?? 0;
	            $params['form_uri'] = isset($property->linked_forms[0]['form_id_store']) ? urlencode($property->linked_forms[0]['form_id_store']) : "";
	            $params['select_tab'] = 1;
	            $params['create'] = 1;
	            $params['scenario'] = $property->linked_forms[0]['scenario_id'] ?? 0;
	            $params['multiple_scenarios'] = $property->has_multiple_scenarios;
	            $params['attachment'] = $property->linked_forms[0]['attachment_id'] ?? 0;
	            $params['no_search'] = $property->is_no_search() ?? 0;
	            
	            $json_data = encoding_normalize::json_encode($params);
	            $url = "./select.php?what=contribution&selector_data=".urlencode($json_data);
	            $input = str_replace("!!url_linked_form!!", $url, $input);
	        }
	        $input = str_replace("!!linked_scenario!!", $property->linked_forms[0]['scenario_id'] ?? 0, $input);
	    }
	    
	    $params['scenario'] = $property->linked_scenarios[0] ?? 0;
	    $params['attachment'] = $property->scenarios_tab[0]['attachmentId'] ?? 0;
	    $params['area_id'] = $property->scenarios_tab[0]['area_id'] ?? 0;
	    $params['multiple_scenarios'] = $property->has_multiple_scenarios;
	    $params['select_tab'] = 0;
	    $params['is_entity'] = $property->is_entity;
	    $params['no_search'] = $property->is_no_search() ?? 0;
	    $json_data = encoding_normalize::json_encode($params);
	    $url_search = "./select.php?what=contribution&selector_data=".urlencode($json_data);
	    $input = str_replace("!!url_search_form!!", $url_search, $input);
	    
	    $input .= $ontology_tpl['form_row_content_input_json_data'];
	    $input = str_replace("!!onto_json_data!!", htmlentities($json_data, ENT_QUOTES, $charset), $input);
	    
	    $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
	    $input = str_replace("!!linked_tab_title!!", $property->label, $input);
	    $input = str_replace("!!onto_new_order!!", $order, $input);
	    
	    // On ajoute le "+" sur le dernier éléments
	    if ($order == $new_element_order) {
	        $input .= $add_button;
	    }
	    
	    $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
	    
	    return $input;
	}
	
} // end of onto_common_datatype_resource_selector_ui
