<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_record_selector_ui.class.php,v 1.20.2.1 2024/06/25 09:46:20 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once $class_path.'/authority.class.php';
require_once $class_path.'/notice.class.php';
/**
 * class onto_common_datatype_responsability_selector_ui
 * 
 */
class onto_contribution_datatype_linked_record_selector_ui extends onto_contribution_datatype_resource_selector_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param Array() class_uris URI des classes de l'ontologie listées dans le sélecteur

	 * @return void
	 * @access public
	 */
	public function __construct( $class_uris ) {
	} // end of member function __construct

	/**
	 * 
	 *
	 * @param string class_uri URI de la classe d'instances à lister

	 * @param integer page Numéro de page à afficher

	 * @return Array()
	 * @access public
	 */
	public function get_list( $class_uri,  $page ) {
	} // end of member function get_list

	/**
	 * Recherche
	 *
	 * @param string user_query Chaine de recherche dans les labels

	 * @param string class_uri Rechercher iniquement les instances de la classe

	 * @param integer page Page du résultat de recherche à afficher

	 * @return Array()
	 * @access public
	 */
	public function search( $user_query,  $class_uri,  $page ) {
	} // end of member function search


	/**
	 * 
	 *
	 * @param onto_common_property $property la propriété concernée
	 * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété 
	 * @param array $datas le tableau des datatypes
	 * @param string $instance_name nom de l'instance
	 * @param string $flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag) {
	    global $area_id, $charset, $ontology_tpl;
	    
	    if (empty($datas)) {
	        $datas = array();
	    }
	    //gestion des droits
	    global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
	    if (($gestion_acces_active == 1) && ($gestion_acces_empr_contribution_scenario == 1)) {
	        $ac = new acces();
	        $dom_5 = $ac->setDomain(5);
	    }
		$form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
		/** traitement initial du range ?!*/
		$range_for_form = "";
		if(is_array($property->range)){
			foreach($property->range as $range){
				if ($range_for_form) {
					$range_for_form.="|||";
				}
				$range_for_form.=$range;
			}
		}
		if ($restrictions->get_max() < count($datas) || $restrictions->get_max() === -1) {
		    $add_button = $ontology_tpl['form_row_content_input_add_linked_record'];
		    $add_button = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $add_button);
		}
		if (!empty($datas)) {
			$i = 1;
			$new_element_order = max(array_keys($datas));
			$form = str_replace("!!onto_new_order!!", $new_element_order, $form);
			foreach($datas as $key=>$data){
				$row = $ontology_tpl['form_row_content_with_flex'];
				if($data->get_order()){
					$order = $data->get_order();
				}else{
					$order = $key;
				}
				$formated_value = $data->get_formated_value();
				$inside_row = $ontology_tpl['form_row_content_linked_record_selector'];
				$selector = static::get_selector_notice_relations($property, '!!onto_row_id!![!!onto_row_order!!][assertions][relation_type]',(isset($formated_value['relation_type']) ? $formated_value['relation_type'] : ""));
				$inside_row = str_replace([
					"!!form_row_content_linked_record_selector_display_label!!",
					"!!form_row_content_linked_record_selector_value!!",
					"!!form_row_content_linked_record_selector_is_draft!!",
					"!!form_row_content_linked_record_selector_add_reverse_link_checked!!",
					"!!form_row_content_linked_record_selector_range!!",
					"!!onto_row_content_linked_record_selector!!",
					"!!onto_row_content_marclist_range!!",
					"!!onto_current_element!!",
					"!!onto_current_range!!",
				],[
					htmlentities((isset($formated_value['record']['display_label']) ? addslashes($formated_value['record']['display_label']) : ""), ENT_QUOTES, $charset),
					(isset($formated_value['record']['value']) ? $formated_value['record']['value'] : ""),
					$formated_value['record']['is_draft'],
					(!empty($formated_value['add_reverse_link']) ? 'checked' : ''),
					$data->get_value_type(),
					$selector,
					$property->range[0],
					onto_common_uri::get_id($item_uri),
					$data->get_value_type(),
				], $inside_row);
				$class = "";
				if (!empty($formated_value['record']['is_draft']) && $formated_value['record']['is_draft']) {
				    $class = "contribution_draft";
				}
				$row = str_replace("!!onto_row_is_draft!!", $class, $row);
				$row = str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$input = '';
// 				if($first){
					$input.= $ontology_tpl['form_row_content_input_remove'];
// 				}else{
// 					$input.= $ontology_tpl['form_row_content_input_del'];
// 				}

				$input.=$ontology_tpl['form_row_content_search'];
				
				$params = [];
				$params['type'] = self::get_type_from_range($property->range[0]);
				$params['sub_form'] = 1;
				$params['is_draft'] = $property->is_draft ?? 0;
				$params['is_entity'] =  $property->is_entity ? true : false;
				$params['equation'] = static::get_equation_query($property);
					
				if ($property->has_linked_form) {
				    $linked_forms = false;
				    
				    if ($property->is_entity && !empty($formated_value['record']['value'])) {
				        $linked_forms = true;
				        // On définis des valeurs par défaut
				        $formated_value['record']['form_uri'] = $property->linked_forms[0]['form_id_store'];
				        $formated_value['record']['form_id'] = $property->linked_forms[0]['form_id'];
				        $formated_value['record']['area_id'] = $property->linked_forms[0]['area_id'];
				    } else {
    				    foreach ($property->linked_forms as $linked_form){
    				        if (!empty($formated_value['record']['form_uri']) && $linked_form['form_id_store'] == $formated_value['record']['form_uri']) {
    				            $linked_forms = true;
    				        }
    				    }
				    }
				    
				    //Onglet modifier
				    if ($linked_forms && $formated_value['record']['value']) {
				        //Onglet modifier
				        $input .= $ontology_tpl['form_row_content_edit'];
				        $url = static::get_edit_url($formated_value['record'], $property->linked_forms[0]['scenario_id'], $params['type'], $params);
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
				        
				        
				        if ($linked_forms && !empty($formated_value['record']) && !empty($formated_value['record']['value'])) {
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
				        
				        $json_data = encoding_normalize::json_encode($params);
				        $url = "./select.php?what=contribution&selector_data=".urlencode($json_data);
				        $input = str_replace("!!url_linked_form!!", $url, $input);
				    }
				    $input = str_replace("!!linked_scenario!!", $property->linked_forms[0]['scenario_id'] ?? 0, $input);
				}
				
				$params['scenario'] = $property->linked_scenarios[0] ?? 0;
				$params['multiple_scenarios'] = $property->has_multiple_scenarios;
				$params['select_tab'] = 0;
				$params['is_entity'] = $property->is_entity;
				$json_data = encoding_normalize::json_encode($params);
				$url_search = "./select.php?what=contribution&selector_data=".urlencode($json_data);
				$input = str_replace("!!url_search_form!!", $url_search, $input);
				
				$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
				$input = str_replace("!!linked_tab_title!!", $property->label, $input);
				$input = str_replace("!!onto_new_order!!", $order, $input);
				
				$input .= $add_button;
				
				$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
				
				$row = str_replace("!!onto_row_inputs!!", $input, $row);
				$row = str_replace("!!onto_row_resource_selector!!", $ontology_tpl['form_row_content_resource_template'], $row);
				$row = str_replace("!!onto_row_order!!", $order, $row);
				
				$content .= $row;
// 				$first = false;
				$i++;
			}
		} else {
			$form = str_replace("!!onto_new_order!!","0" , $form);
			
			$row = $ontology_tpl['form_row_content_with_flex'];
			
			$inside_row = $ontology_tpl['form_row_content_linked_record_selector'];
			$inside_row = str_replace("!!form_row_content_linked_record_selector_display_label!!", "", $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_record_selector_value!!","" , $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_record_selector_is_draft!!", "0", $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_record_selector_add_reverse_link_checked!!", "", $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_record_selector_range!!","" , $inside_row);
			
			$selector = static::get_selector_notice_relations($property, '!!onto_row_id!![!!onto_row_order!!][assertions][relation_type]');
			$inside_row = str_replace('!!onto_row_content_linked_record_selector!!', $selector, $inside_row);
			$inside_row = str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
			
			$inside_row = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri),$inside_row);
			$inside_row = str_replace("!!onto_current_range!!", 'http://www.pmbservices.fr/ontology#record', $inside_row);
			
			$row = str_replace("!!onto_inside_row!!",$inside_row , $row);
			
			$input = '';
			$input .= $ontology_tpl['form_row_content_input_remove'];
			$row = str_replace("!!onto_row_resource_selector!!", $ontology_tpl['form_row_content_resource_template'], $row);
			$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
			$input .= $add_button;
			
			$row = str_replace("!!onto_row_inputs!!", $input, $row);
			$row = str_replace("!!onto_row_order!!", "0", $row);
				
			$content.= $row;
		}
		
		$form = str_replace("!!onto_rows!!", $content, $form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_completion!!", 'notice', $form);
		$form = str_replace("!!onto_row_id!!", $instance_name.'_'.$property->pmb_name, $form);
		$form = str_replace("!!onto_equation_query!!", htmlentities(static::get_equation_query($property),ENT_QUOTES,$charset), $form);
		$form = str_replace("!!onto_area_id!!", ($area_id ? $area_id : ''), $form);
		
		return $form;
	} // end of member function get_form
	
	/**
	 * 
	 *
	 * @param onto_common_datatype datas Tableau des valeurs à afficher associées à la propriété

	 * @param property property la propriété à utiliser

	 * @param string instance_name nom de l'instance

	 * @return string
	 * @access public
	 */
	public function get_display($datas, $property, $instance_name) {
		
		$display='<div id="'.$instance_name.'_'.$property->pmb_name.'">';
		$display.='<p>';
		$display.=$property->get_label().' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
	}

	private static function get_selector_notice_relations($property, $name='', $selected='', $multiple = false) {
	    global $msg;
	    
	    $select = "<select id='".$name."' name='".$name."' ".($multiple ? "multiple='multiple'" : "").">";
	    $select .= static::get_options_by_direction($property, 'up', $selected);
	    $select .= static::get_options_by_direction($property, 'down', $selected);
	    $select .= static::get_options_by_direction($property, 'both', $selected);
	    $select .= "</select>";
	    return $select;
	}

	private static function get_options_by_direction($property, $direction = "", $selected = "") {
	    global $msg;
	    $label = "";
	    $type_relation = notice_relations::get_liste_type_relation_by_direction($direction);
	    $list_entities = (!empty($property->pmb_extended['list_entities']) ? explode(',', $property->pmb_extended['list_entities']) : array());
	    switch ($direction) {
	        case "both":
	            $label = $msg['notice_lien_symetrique'];
	            break;
	        case "down":
	            $label = $msg['notice_lien_descendant'];
	            break;
	        case "up":
	            $label = $msg['notice_lien_montant'];
	            break;
	    }
	    $options = "<optgroup class='erreur' label='".$label."'>";
	    $total_options = count($type_relation->table);
	    $options_hidden = 0;
	    foreach($type_relation->table as $key => $val) {
	        $value = $key.'-'.$direction;
	        $style = "color: #000000;";
	        if (!empty($list_entities) && !in_array($value, $list_entities)) {
	            $options_hidden++;
	            continue;
	        }
	        $reverse_code = $type_relation->attributes[$key]['REVERSE_CODE'];
	        $reverse_direction = $type_relation->attributes[$key]['REVERSE_DIRECTION'];
	        $option_selected = '';
	        if((is_array($selected) && in_array($value, $selected)) || ($value == $selected)) {
	            $option_selected = 'selected="selected"';
	        }
	        $options .='<option  style="'.$style.'" value="'.$value.'" '.$option_selected.' data-reverse-code="'.$reverse_code.'-'.$reverse_direction.'">'.$val.'</option>';
	    }
	    $options .= "</optgroup>";
	    return ($options_hidden === $total_options ? '' : $options);
	}

	protected function get_resource_selector_url($resource_uri){
		/**
		 * TODO: 
		 * Deux solutions possibles ?
		 * Générer Les urls côté php et concatener avec les variables spéciales issues du formulaire dans les fonctions JS ? 
		 * 	Ex: transmetre './select.php?what=notice&caller='; et passer les params directement dans la fonction js appelée à l'appui sur ajouter
		 *   -> Si l'on a qu'une fonction JS, ça impose de ressortir un type depuis le php ?!
		 */
		switch($resource_uri){
			case 'http://www.pmbservices.fr/ontology#record':
				$selector_url = './select.php?what=notice&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#author':
			case 'http://www.pmbservices.fr/ontology#responsability':
				$selector_url = './select.php?what=auteur&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#category':
				$selector_url = './select.php?what=categorie&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#publisher':
				$selector_url = './select.php?what=editeur&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#collection':
				$selector_url = './select.php?what=collection&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#sub_collection':
				$selector_url = './select.php?what=subcollection&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#serie':
				$selector_url = './select.php?what=serie&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#work':
				$selector_url = './select.php?what=titre_uniforme&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#indexint':
				$selector_url = './select.php?what=indexint&caller=';
				break;
			case 'http://www.w3.org/2004/02/skos/core#Concept':
				$selector_url = './select.php?what=ontology&objs=&element=concept&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#bulletin':
				$selector_url = './select.php?what=bulletin&caller=';
				break;
			default:
				$selector_url = './select.php?what=ontologies&caller=';
				break;
		}
		return $selector_url;
	}


	protected static function get_completion_from_range($range) {
		$completion = '';
		//on récupère le type de range en enlevant le préfixe propre à l'ontologie
		switch ($range) {
			case 'http://www.pmbservices.fr/ontology#linked_record' :
			case 'http://www.pmbservices.fr/ontology#record' :
				$completion = 'notice';
				break;
			case 'http://www.pmbservices.fr/ontology#author' :
			case 'http://www.pmbservices.fr/ontology#responsability' :
				$completion = 'authors';
				break;
			case 'http://www.pmbservices.fr/ontology#category' :
				$completion = 'categories';
				break;
			case 'http://www.pmbservices.fr/ontology#publisher' :
				$completion = 'publishers';
				break;
			case 'http://www.pmbservices.fr/ontology#collection' :
				$completion = 'collections';
				break;
			case 'http://www.pmbservices.fr/ontology#sub_collection' :
				$completion = 'subcollections';
				break;
			case 'http://www.pmbservices.fr/ontology#serie' :
				$completion = 'serie';
				break;
			case 'http://www.pmbservices.fr/ontology#work' :
				$completion = 'titre_uniforme';
				break;
			case 'http://www.pmbservices.fr/ontology#indexint' :
				$completion = 'indexint';
				break;
			case 'http://www.w3.org/2004/02/skos/core#Concept' :
				$completion = 'onto';
				break;
			default:
				$completion ='onto';
				break;
		}
		return $completion;
	}
}
