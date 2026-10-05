<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_responsability_selector_ui.class.php,v 1.47.2.1.2.2 2025/02/03 13:38:09 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/contribution/onto_contribution_datatype_resource_selector_ui.class.php';
require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once $include_path.'/templates/onto/contribution/onto_contribution_datatype_ui.tpl.php';
require_once $class_path.'/authority.class.php';
require_once $class_path.'/notice.class.php';
/**
 * class onto_common_datatype_responsability_selector_ui
 *
 */
class onto_contribution_datatype_responsability_selector_ui extends onto_contribution_datatype_resource_selector_ui {

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
	 * @param string $item_uri Uri de l'item
	 * @param onto_common_property $property Propriété concernée
	 * @param onto_restriction $restrictions Tableau des restrictions associées à la propriété
	 * @param array $datas Tableau des datatypes
	 * @param string $instance_name Nom de l'instance
	 * @param string $flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag) {
	    global $charset, $ontology_tpl, $area_id, $pmb_authors_qualification, $ontology_contribution_tpl;

	    $form=$ontology_contribution_tpl['form_row_responsability'];
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);

		$content='';
		$add_button = '';
		if($restrictions->get_max()===-1){
		    $add_button = $ontology_tpl['form_row_content_input_add_responsability_selector'];
    		$add_button = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $add_button);
		}

		//$content.=$ontology_tpl['form_row_content_input_sel'];
		if (!empty($datas)) {
			$i=1;
			$new_element_order=max(array_keys($datas));

			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			foreach($datas as $key=>$data){

				if($data->get_order()){
					$order = $data->get_order();
				}else{
					$order = $key;
				}
				$add_button_form = ( $key == (count($datas)-1) ? $add_button : "");
				$formated_value = $data->get_formated_value();
				$row = self::get_template($item_uri, $property, $property->range[0], $order, $data, $formated_value['author']['is_draft'] ?? false);
				$row = str_replace("!!onto_row_inputs!!", (empty($property->pmb_extended['readonly']) ? self::get_inputs($item_uri, $property, $order, $add_button, $formated_value, $new_element_order) : ""), $row);
				$row = str_replace("!!onto_row_inputs_add!!", $add_button_form, $row);

				// Hack afin d'avoir un meilleur affichage dans le cas des qualifs sans avoir à modifier toutes les contrib
				$resource_selector = '';
				if ($pmb_authors_qualification) {
				    switch (explode('_', $instance_name)[0]) {
				        case 'work':
				            $grammar = 'tu_authors';
				            break;
				        case 'record':
				            $grammar = 'notice_authors';
				            break;
				        default:
				            $grammar = 'rameau';
				            break;
				    }
				    $vedette = new vedette_composee(0, $grammar);
				    $qualification = '';
				    if (!empty($formated_value['author_qualification'])){
				        $qualification = $formated_value['author_qualification'];
				        if(!is_object($qualification)){
				            $qualification = json_decode($qualification, false);
				        }
				    }
				    $vedette->feed($qualification);
				    $vedette_ui = new vedette_ui($vedette);
				    $vedette_row = $ontology_contribution_tpl['form_row_content_vedette'];
				    $vedette_row = str_replace("!!vedette_value!!", $vedette->get_label(), $vedette_row);
				    $type = $vedette_ui->get_vedette_type_from_pmb_name($property->pmb_name);
				    $vedette_row = str_replace("!!vedette_author!!", $vedette_ui->get_form($property->pmb_name, $i-1, $instance_name, $type, 1, true), $vedette_row);
				    $resource_selector .= $vedette_row;
				}
				$resource_selector .= $ontology_tpl['form_row_content_resource_template'];

				$row = str_replace("!!onto_row_resource_selector!!", $resource_selector, $row);
				$row = str_replace("!!onto_row_order!!", $order, $row);

				$content .= $row;
				$i++;
			}
		} else {

		    $order = "0";
		    $form = str_replace("!!onto_new_order!!", $order, $form);

		    $row = self::get_template($item_uri, $property, $property->range[0], $order);
		    $row = str_replace("!!onto_row_inputs!!", ($property->pmb_extended['readonly'] == false ? self::get_inputs($item_uri, $property, $order) : ""), $row);
		    $row = str_replace("!!onto_row_inputs_add!!", $add_button, $row);

			$resource_selector = '';
			if ($pmb_authors_qualification) {
			    switch (explode('_', $instance_name)[0]) {
			        case 'work':
			            $grammar = 'tu_authors';
			            break;
			        case 'record':
			            $grammar = 'notice_authors';
			            break;
			        default:
			            $grammar = 'rameau';
			            break;
			    }
			    $vedette_ui = new vedette_ui(new vedette_composee(0, $grammar));

			    $vedette_row = $ontology_contribution_tpl['form_row_content_vedette'];
			    $vedette_row = str_replace("!!vedette_value!!", "", $vedette_row);
			    $vedette_row = str_replace("!!vedette_author!!", $vedette_ui->get_form($property->pmb_name, 0, $instance_name, "", 1, true), $vedette_row);
			    $resource_selector .= $vedette_row;
			}
			$resource_selector .= $ontology_tpl['form_row_content_resource_template'];

			$row = str_replace("!!onto_row_resource_selector!!", $resource_selector, $row);
            $row = str_replace("!!onto_row_order!!", $order, $row);

			$content = $row;
		}

		$form = str_replace("!!onto_rows!!", $content, $form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_completion!!",'authors', $form);
		$form = str_replace("!!onto_row_id!!", $instance_name.'_'.$property->pmb_name, $form);

		$form = str_replace("!!onto_equation_query!!", htmlentities(static::get_equation_query($property),ENT_QUOTES,$charset), $form);
		$form = str_replace("!!onto_area_id!!", $area_id ?? '', $form);

		return $form;
	} // end of member function get_form

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

	            $row = $ontology_tpl['form_row_content_resource_selector_hidden'];
	            if($data->get_order()){
	                $order = $data->get_order();
	            } else {
	                $order = $key;
	            }

	            $formated_value = $data->get_formated_value();
                $is_draft = false;
                if (isset($formated_value['author']['is_draft'])) {
	                $is_draft = true;
	            }
	            $row=str_replace("!!onto_row_content_hidden_display_label!!", htmlentities(addslashes($formated_value['author']['display_label']), ENT_QUOTES, $charset), $row);
	            $row=str_replace("!!onto_row_content_hidden_value!!", htmlentities($formated_value['author']['value'], ENT_QUOTES, $charset) , $row);
	            $row = str_replace("!!onto_row_content_hidden_is_draft!!", ($is_draft ? $formated_value['author']['is_draft'] : "0"), $row);
	            $row=str_replace("!!onto_row_content_hidden_range!!", ($data->get_value_type() ?? $property->range[0]), $row);
				$row = str_replace("!!onto_row_content_hidden_assertions!!", htmlentities($data->json_encode_assertions(), ENT_QUOTES, $charset), $row);
	            $row=str_replace("!!onto_row_order!!", $order, $row);
	            $content.=$row;
	        }
	    } else {
	        $form=str_replace("!!onto_new_order!!", "0", $form);
	        $row = $ontology_tpl['form_row_content_resource_selector_hidden'];
	        $row = str_replace("!!onto_row_content_hidden_display_label!!", "", $row);
	        $row = str_replace("!!onto_row_content_hidden_value!!", htmlentities($value, ENT_QUOTES, $charset), $row);
	        $row = str_replace("!!onto_row_content_hidden_is_draft!!", "0", $row);
	        $row = str_replace("!!onto_row_content_hidden_range!!", $property->range[0], $row);
	        $row = str_replace("!!onto_row_content_hidden_assertions!!", "", $row);
	        $row=str_replace("!!onto_row_order!!", "0", $row);
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

	public static function get_author_function_options($property, $selected = "") {
	    global $msg, $charset;
	    $marc_list = marc_list_collection::get_instance('function');
	    $list_values = ($property->pmb_extended['list_values'] ? explode(',', $property->pmb_extended['list_values']) : array());
	    $options = '';
	    $options.= '<option value="" '.(empty($selected) ? 'selected=selected>' : '>').$msg['onto_contribution_fonction_author'].'</option>';
	    foreach($marc_list->table as $value => $label){
	        if (!empty($list_values) && !in_array($value, $list_values)) {
	            continue;
	        }
	        $options.= '<option value="'.$value.'" '.($selected == $value ? 'selected=selected>' : '>').htmlentities($label,ENT_QUOTES,$charset).'</option>';
	    }
	    return $options;
	}

	/**
	 * Retourne le template pour une ligne
	 *
	 * @param string $item_uri
	 * @param onto_common_property $property
	 * @param string $range
	 * @param string|int $order
	 * @param array $data
	 * @param boolean $is_draft
	 * @return mixed
	 */
	private static function get_template($item_uri, $property, $range, $order, $data = array(), $is_draft = false)
	{
	    global $ontology_tpl, $charset, $ontology_contribution_tpl;

	    $row = $ontology_contribution_tpl['form_row_content_with_flex_responsability'];
	    $template = $ontology_tpl['form_row_content_responsability_selector'];

	    $label = "";
	    $value = "";
	    $author_function_value = "";
	    $value_type = $range;
	    if (!empty($data)) {
	        $formated_value = $data->get_formated_value();
	        $label = addslashes($formated_value['author']['display_label']);
	        $value = $formated_value['author']['value'];
	        $value_type = $data->get_value_type() ?? $range;
	        $author_function_value = $formated_value['author_function'] ?? "";
			if(! is_string($value)) {
				$value = "";
			}
	    }

	    $template = str_replace("!!form_row_content_responsability_selector_display_label!!", htmlentities($label, ENT_QUOTES, $charset), $template);
	    $template = str_replace("!!form_row_content_responsability_selector_value!!", $value, $template);
	    $template = str_replace("!!form_row_content_responsability_selector_is_draft!!", ($is_draft ? $formated_value['author']['is_draft'] : "0"), $template);
	    $template = str_replace("!!form_row_content_responsability_selector_range!!", $value_type, $template);

	    $template_function_value = "";
	    if (!empty($property->pmb_extended['template']) && $property->pmb_extended['template'] == 1) {
	        $author_function_label = $formated_value['function_label'] ?? "";
	        if (!empty($author_function_value) && empty($author_function_label)) {
	            $marc_list = new marc_list("function");
	            $author_function_label = $marc_list->table[$author_function_value] ?? "";
	        }
	        $template_function_value = str_replace('!!limited_function!!', $property->pmb_extended['list_values'] ?? '', $ontology_tpl['responsability_autocomplete_function_value']);
	        $template_function_value = str_replace('!!form_row_content_responsability_selector_function_label!!', $author_function_label, $template_function_value);
	        $template_function_value = str_replace('!!form_row_content_responsability_selector_function_value!!', $author_function_value, $template_function_value);
	    } else {
	        $options = static::get_author_function_options($property, $author_function_value);
	        $template_function_value = str_replace('!!onto_row_content_marclist_options!!', $options, $ontology_tpl['responsability_selector_function_value']);
	    }

	    $template = str_replace("!!template_responsability_function_value!!", $template_function_value, $template);
	    $template = str_replace("!!onto_row_content_marclist_range!!", $property->range[0], $template);
	    $template = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri), $template);
	    $template = str_replace("!!onto_current_range!!", $value_type, $template);

	    $row = str_replace("!!onto_row_is_draft!!", ($is_draft ? 'contribution_draft' : ''), $row);
	    $row = str_replace("!!onto_inside_row!!", $template, $row);

	    return $row;
	}

	/**
	 * Retourne les boutons rechercher, créer et modifiés
	 *
	 * @param string $item_uri
	 * @param onto_common_property $property
	 * @param string|int $order
	 * @param string $add_button
	 * @param array $formated_value
	 * @param string|int $new_element_order
	 * @return string
	 */
	protected static function get_inputs($item_uri, $property, $order, $add_button = "", $formated_value = array(), $new_element_order = 0)
	{
	    global $ontology_tpl;

	    global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
	    if (($gestion_acces_active == 1) && ($gestion_acces_empr_contribution_scenario == 1)) {
	        $ac = new acces();
	        $dom_5 = $ac->setDomain(5);
	    }

	    $input = '';
	    $input .= $ontology_tpl['form_row_content_input_remove'];
	    $input .= $ontology_tpl['form_row_content_search'];

	    $params = [];
	    $params['type'] = self::get_type_from_range($property->range[0]);
	    $params['sub_form'] = 1;
	    $params['is_draft'] = $property->is_draft ?? 0;
	    $params['is_entity'] =  $property->is_entity ? true : false;
	    $params['equation'] = static::get_equation_query($property);

	    if ($property->has_linked_form) {
	        $linked_forms = false;

	        if ($property->is_entity && !empty($formated_value['author']['value'])) {
	            $linked_forms = true;
	            // On définis des valeurs par défaut
	            $formated_value['author']['form_uri'] = $property->linked_forms[0]['form_id_store'];
	            $formated_value['author']['form_id'] = $property->linked_forms[0]['form_id'];
	            $formated_value['author']['area_id'] = $property->linked_forms[0]['area_id'];
	        } else {
	            foreach ($property->linked_forms as $linked_form){
	                if (!empty($formated_value['author']['form_uri']) && $linked_form['form_id_store'] == $formated_value['author']['form_uri']) {
	                    $linked_forms = true;
	                    break;
	                }
	            }
	        }

	        //Onglet modifier
	        if ($linked_forms && $formated_value['author']['value']) {
	            //Onglet modifier
	            $input .= $ontology_tpl['form_row_content_edit'];
	            $url = static::get_edit_url($formated_value['author'], $property->linked_forms[0]['scenario_id'], $params['type'], $params) ;
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

	        // Bouton de création :
	        if ($access_granted) {

	            if ($linked_forms && $formated_value && !empty($formated_value['author']) && !empty($formated_value['author']['value']) ) {
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
	    $params['attachment'] = $property->scenarios_tab[0]['attachmentId'] ?? 0;
	    $params['area_id'] = $property->scenarios_tab[0]['area_id'] ?? 0;
	    $params['multiple_scenarios'] = $property->has_multiple_scenarios;
	    $params['select_tab'] = 0;
	    $params['is_entity'] = $property->is_entity;
	    $json_data = encoding_normalize::json_encode($params);
	    $url_search = "./select.php?what=contribution&selector_data=".urlencode($json_data);
	    $input = str_replace("!!url_search_form!!", $url_search, $input);

	    $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
	    $input = str_replace("!!linked_tab_title!!", $property->label, $input);
	    $input = str_replace("!!onto_new_order!!", $order, $input);

	    // On ajoute le "+" sur le dernier éléments
// 	    if ($order == $new_element_order) {
// 	        $input .= $add_button;
// 	    }

	    return $input;
	}
} // end of onto_common_datatype_responsability_selector_ui
