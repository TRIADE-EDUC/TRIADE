<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_datatype_resource_selector_ui.class.php,v 1.10 2019/08/14 08:02:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';


/**
 * class onto_common_datatype_resource_selector_ui
 * 
 */
class onto_skos_datatype_resource_selector_ui extends onto_common_datatype_resource_selector_ui {

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

		//on regarde si le flag change quelque chose pour notre sélecteur 
		$fixed = false;
		/* Pourquoi plus de bouton de selection ??
		if(strpos($flag, "_selector_form") !== false){
			$fixed =true;
		}
		*/
		$form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!",htmlentities($property->get_label() ,ENT_QUOTES,$charset) , $form);
		
		$range_for_form = "";
		foreach($property->range as $range){
			if($range_for_form) $range_for_form.="|||";
			$range_for_form.=$range;
		}
		$content='';
		if(!$fixed){
			
			$content.=$ontology_tpl['form_row_content_input_sel'];
// 			if($restrictions->get_max()<$i || c$restrictions->get_max()===-1){ //Edit VT 16/10/17 pour moi la condition n'a plus lieu d'être
				$content.=$ontology_tpl['form_row_content_input_add_ressource_selector'];
// 			}
		}
		$content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);
		if (!empty($datas)) {
			$i=1;
			$first=true;
			$new_element_order=max(array_keys($datas));
			
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			
			foreach($datas as $key=>$data){
				$row=$ontology_tpl['form_row_content'];
				
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				 
				$inside_row=$ontology_tpl['form_row_content_resource_selector'];
				$inside_row=str_replace("!!form_row_content_resource_selector_display_label!!",htmlentities($data->get_formated_value(),ENT_QUOTES,$charset) , $inside_row);
				$inside_row=str_replace("!!form_row_content_resource_selector_value!!",$data->get_raw_value() , $inside_row);
				$inside_row=str_replace("!!form_row_content_resource_selector_range!!",$data->get_value_type() , $inside_row);
				$inside_row=str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
				
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$input='';
				if (!$fixed){
					if($first){
						$input.=$ontology_tpl['form_row_content_input_remove'];
					}else{
						$input.=$ontology_tpl['form_row_content_input_del'];
					}
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
			
			$inside_row=$ontology_tpl['form_row_content_resource_selector'];
			$inside_row=str_replace("!!form_row_content_resource_selector_display_label!!","" , $inside_row);
			$inside_row=str_replace("!!form_row_content_resource_selector_display_label!!","" , $inside_row);
			$inside_row=str_replace("!!form_row_content_resource_selector_value!!","" , $inside_row);
			$inside_row=str_replace("!!form_row_content_resource_selector_range!!","" , $inside_row);
			$inside_row=str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
			
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
			
			$input='';
			if (!$fixed){
				$input.=$ontology_tpl['form_row_content_input_remove'];
			}
			$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
			$row=str_replace("!!onto_row_inputs!!",$input , $row);
				
			$row=str_replace("!!onto_row_order!!","0" , $row);
				
			$content.=$row;
		}
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form=str_replace("!!onto_completion!!",self::get_completion_from_range($range_for_form), $form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		$form=str_replace("!!onto_current_range!!",$range_for_form,$form);
		return $form;
	} // end of member function get_form
	

} // end of onto_common_datatype_resource_selector_ui
