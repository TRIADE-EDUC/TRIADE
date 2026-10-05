<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_resource_pmb_selector_ui.class.php,v 1.8 2019/08/14 08:02:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_resource_selector_ui.class.php';
require_once $class_path.'/authority.class.php';
require_once $class_path.'/mono_display.class.php';
/**
 * class onto_contribution_datatype_resource_selector_ui
 * 
 */
class onto_common_datatype_resource_pmb_selector_ui extends onto_common_datatype_resource_selector_ui {
	
	/**
	 *
	 *
	 * @param onto_common_property $property la propriété concernée
	 * @param restriction $restrictions le tableau des restrictions associées à la propriété
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag
	
	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
		global $msg,$charset,$ontology_tpl;
		
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

		$content='';
		$content.=$ontology_tpl['form_row_content_input_sel_pmb'];
		
		if($restrictions->get_max()<$i || $restrictions->get_max()===-1){			
			$content.=$ontology_tpl['form_row_content_input_add_selector_pmb'];
		}
		$content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);
		$content = str_replace("!!onto_selector_url!!", self::get_resource_selector_url($property->range[0]), $content);

		$content = str_replace("!!onto_pmb_selector_max_card!!", $restrictions->get_max(), $content);
		$content = str_replace("!!onto_pmb_selector_min_card!!", $restrictions->get_min(), $content);
		$content = str_replace("!!max_field_value!!", (count($datas) ? count($datas) : 1), $content);
		
		
		if (!empty($datas)) {
				
			$i=1;
			$first=true;
			$new_element_order=max(array_keys($datas));
				
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			foreach($datas as $key => $data){
				$label = "";
				$id = "";
				if($data->get_value()){
					$id = str_replace($data->get_value_type().'_', '', $data->get_value());
					$label = self::get_entity_isbd($id, $data->get_value_type());
				}			
				$row=$ontology_tpl['form_row_content'];
				
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				
				$inside_row = $ontology_tpl['form_row_content_resource_selector_pmb'];
				$inside_row = str_replace("!!form_row_content_resource_selector_range!!",htmlentities(addslashes($property->range[0]),ENT_QUOTES,$charset), $inside_row);
				$inside_row.= $ontology_tpl['form_row_content_type'];
				$inside_row = str_replace("!!form_row_content_resource_selector_display_label!!",htmlentities(addslashes($label),ENT_QUOTES,$charset), $inside_row);
				$inside_row = str_replace("!!form_row_content_resource_selector_value!!", $id, $inside_row);
				$inside_row = str_replace("!!onto_row_content_range!!",$data->get_value_type() , $inside_row);
				$inside_row = str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
				$inside_row = str_replace("!!onto_current_range!!",$range_for_form,$inside_row);
	
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
	
				$input='';
				if($first){
					$input.=$ontology_tpl['form_row_content_input_remove_pmb'];
				}else{
					$input.=$ontology_tpl['form_row_content_input_del'];
				}
				
				$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
	
				$row=str_replace("!!onto_row_inputs!!",$input , $row);
				$row=str_replace("!!onto_row_order!!",$order , $row);
	
				$content.=$row;
				$first=false;
				$i++;
					
				
			}
		}else{
			$form=str_replace("!!onto_new_order!!","0" , $form);
				
			$row=$ontology_tpl['form_row_content'];
				
			$inside_row=$ontology_tpl['form_row_content_resource_selector_pmb'];
			$inside_row=str_replace("!!form_row_content_resource_selector_range!!",htmlentities(addslashes($property->range[0]),ENT_QUOTES,$charset), $inside_row);
			$inside_row .= $ontology_tpl['form_row_content_type'];
			
			$inside_row=str_replace("!!form_row_content_resource_selector_display_label!!","" , $inside_row);
			$inside_row=str_replace("!!form_row_content_resource_selector_value!!","" , $inside_row);
			$inside_row=str_replace("!!onto_row_content_range!!",$range_for_form , $inside_row);
			$inside_row=str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
			$inside_row=str_replace("!!onto_current_range!!",$range_for_form,$inside_row);
				
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				
			$input='';
			$input.=$ontology_tpl['form_row_content_input_remove_pmb'];
				
			$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
			$row=str_replace("!!onto_row_inputs!!",$input , $row);
	
			$row=str_replace("!!onto_row_order!!","0" , $row);
	
			$content.=$row;
		}	
	
		$input = '';
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form=str_replace("!!onto_completion!!",self::get_completion_from_range($range_for_form), $form);			
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
	
		return $form;
	} // end of member function get_form
	
	
	public static function get_entity_isbd($id, $type){
		$isbd = '';
		//on récupère le type de range en enlevant le préfixe propre à l'ontologie
		switch ($type) {
			case 'http://www.pmbservices.fr/ontology#linked_record' :
			case 'http://www.pmbservices.fr/ontology#record' :
				/** Tempo, code brut issu de select.php **/
				$mono_display = new mono_display($id, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
				$isbd = $mono_display->header_texte;
				break;
			case 'http://www.pmbservices.fr/ontology#author' :
			case 'http://www.pmbservices.fr/ontology#responsability' :
				$authority_instance = new authority(0, $id, AUT_TABLE_AUTHORS);
				$isbd = $authority_instance->get_object_instance()->get_header();
				break;
			case 'http://www.pmbservices.fr/ontology#category' :
				$authority_instance = new authority(0, $id, AUT_TABLE_CATEG);
				$isbd = $authority_instance->get_object_instance()->get_header();
				break;
			case 'http://www.pmbservices.fr/ontology#publisher' :
				$authority_instance = new authority(0, $id, AUT_TABLE_PUBLISHERS);
				$isbd = $authority_instance->get_object_instance()->get_header();
				break;
			case 'http://www.pmbservices.fr/ontology#collection' :
				$authority_instance = new authority(0, $id, AUT_TABLE_COLLECTIONS);
				$isbd = $authority_instance->get_object_instance()->get_header();
				break;
			case 'http://www.pmbservices.fr/ontology#sub_collection' :
				$authority_instance = new authority(0, $id, AUT_TABLE_SUB_COLLECTIONS);
				$isbd = $authority_instance->get_object_instance()->get_header();
				break;
			case 'http://www.pmbservices.fr/ontology#serie' :
				$authority_instance = new authority(0, $id, AUT_TABLE_SERIES);
				$isbd = $authority_instance->get_object_instance()->get_header();
				break;
			case 'http://www.pmbservices.fr/ontology#work' :
				$authority_instance = new authority(0, $id, AUT_TABLE_TITRES_UNIFORMES);
				$isbd = $authority_instance->get_object_instance()->get_header();
				break;
			case 'http://www.pmbservices.fr/ontology#indexint' :
				$authority_instance = new authority(0, $id, AUT_TABLE_INDEXINT);
				$isbd = $authority_instance->get_object_instance()->get_header();
				break;
			case 'http://www.w3.org/2004/02/skos/core#Concept' :
			    //TODO A reprendre, on ne devrait pas avoir un coup l'id, un coup l'URI
			    if(is_numeric($id)){
				    $authority_instance = new authority(0, $id, AUT_TABLE_CONCEPT);
			    } else {
			        $authority_instance = new authority(0, onto_common_uri::get_id($id), AUT_TABLE_CONCEPT);
			    } 
				$isbd = $authority_instance->get_object_instance()->get_header();
				break;
		}
		return $isbd;
	}
	
} // end of onto_common_datatype_resource_selector_ui
