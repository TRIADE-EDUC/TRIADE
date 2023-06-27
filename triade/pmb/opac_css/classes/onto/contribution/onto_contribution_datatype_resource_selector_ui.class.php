<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_resource_selector_ui.class.php,v 1.14 2019-02-20 14:03:33 tsamson Exp $

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
	 * @param restriction $restrictions le tableau des restrictions associées à la propriété
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag
	
	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
		global $msg,$charset,$ontology_tpl, $area_id;
		
		//gestion des droits
		global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
		if (($gestion_acces_active == 1) && ($gestion_acces_empr_contribution_scenario == 1)) {
			$ac = new acces();
			$dom_5 = $ac->setDomain(5);
		}
	
		$form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->label, 'utf-8') ,ENT_QUOTES,$charset) , $form);
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
	
		/** TODO: à revoir avec le chef ** /
			/** On part du principe que l'on a qu'un range **/
		// 		$selector_url = $this->get_resource_selector_url($property->range[0]);
		$content='';
		if($restrictions->get_max()===-1){			
			$content.=$ontology_tpl['form_row_content_input_add_resource_selector'];
		}
		$content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);
		
		if(sizeof($datas)){
				
			$i=1;
			$first=true;
			$new_element_order=max(array_keys($datas));
				
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
				
			foreach($datas as $key => $data){
				$row=$ontology_tpl['form_row_content'];
	
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				
				$inside_row=$ontology_tpl['form_row_content_resource_selector'];
				$inside_row .= $ontology_tpl['form_row_content_type'];
				$inside_row=str_replace("!!form_row_content_resource_selector_display_label!!",htmlentities(addslashes($data->get_formated_value()),ENT_QUOTES,$charset), $inside_row);
				$inside_row=str_replace("!!form_row_content_resource_selector_value!!",$data->get_value(), $inside_row);
				$inside_row=str_replace("!!onto_row_content_range!!",$data->get_value_type() , $inside_row);
				$inside_row=str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
				$inside_row=str_replace("!!onto_current_range!!",$range_for_form,$inside_row);
	
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
	
				$input='';
				if($first){
					$input.=$ontology_tpl['form_row_content_input_remove'];
				}else{
					$input.=$ontology_tpl['form_row_content_input_del'];
				}
				
				if ($property->has_linked_form && $first) {
					$access_granted = true;
					if (onto_common_uri::is_temp_uri($item_uri)) {
						//droit de creation
						$acces_right = 4;
					} else {
						//droit de modification
						$acces_right = 8;
					}
					if (isset($dom_5) && !$dom_5->getRights($_SESSION['id_empr_session'],onto_common_uri::get_id($property->linked_form['scenario_uri']), $acces_right)) {
						$access_granted = false;
					}
					if ($access_granted) {
						$input .= $ontology_tpl['form_row_content_linked_form'];
						$url = './ajax.php?module=ajax&categ=contribution&sub='.$property->linked_form['form_type'].'&area_id='.$property->linked_form['area_id'].'&id='.onto_common_uri::get_id($data->get_value()).'&sub_form=1&form_id='.$property->linked_form['form_id'].'&form_uri='.urlencode($property->linked_form['form_id_store']);
						$input = str_replace("!!url_linked_form!!", $url, $input);
						$input = str_replace("!!linked_form_title!!", $property->linked_form['form_title'], $input);
					}
				}
				
				$input .= $ontology_tpl['form_row_content_resource_template'];
				
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
			$inside_row .= $ontology_tpl['form_row_content_type'];
			
			$inside_row=str_replace("!!form_row_content_resource_selector_display_label!!","" , $inside_row);
			$inside_row=str_replace("!!form_row_content_resource_selector_value!!","" , $inside_row);
			$inside_row=str_replace("!!onto_row_content_range!!",$range_for_form , $inside_row);
			$inside_row=str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
			$inside_row=str_replace("!!onto_current_range!!",$range_for_form,$inside_row);
				
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				
			$input='';
			$input.=$ontology_tpl['form_row_content_input_remove'];
			
			if ($property->has_linked_form) {
				$access_granted = true;
				if (isset($dom_5) && !$dom_5->getRights($_SESSION['id_empr_session'],onto_common_uri::get_id($property->linked_form['scenario_uri']), 4)) {
					$access_granted = false;
				}
				if ($access_granted) {
					$input .= $ontology_tpl['form_row_content_linked_form'];
					$url = './ajax.php?module=ajax&categ=contribution&sub='.$property->linked_form['form_type'].'&area_id='.$property->linked_form['area_id'].'&id=0&sub_form=1&form_id='.$property->linked_form['form_id'].'&form_uri='.urlencode($property->linked_form['form_id_store']);
					$input = str_replace("!!url_linked_form!!", $url, $input);
					$input = str_replace("!!linked_form_title!!", $property->linked_form['form_title'], $input);
					
					//plusieurs formulaires liés à un sélecteur de ressource
					//$linked_forms_infos = "";
					//foreach($property->linked_form as $linked_form) {
						//if ($linked_forms_infos) {
							//$linked_forms_infos .= "<br/>";
						//}
						//$linked_forms_infos .= $ontology_tpl['form_row_content_linked_form_button'];
						//$url = './ajax.php?module=ajax&categ=contribution&sub='.$linked_form['form_type'].'&area_id='.$linked_form['area_id'].'&id=0&sub_form=1&form_id='.$linked_form['form_id'].'&form_uri='.urlencode($linked_form['form_uri']);
						//$linked_forms_infos = str_replace("!!url_linked_form!!", $url, $linked_forms_infos );
						//$linked_forms_infos = str_replace("!!linked_form_id!!", $linked_form['form_id'], $linked_forms_infos );
						//$linked_forms_infos = str_replace("!!linked_form_title!!", $linked_form['form_title'], $linked_forms_infos );	
						
					//}
					//$input = str_replace("!!linked_forms!!", $linked_forms_infos, $input);
					
	// 				$url = './ajax.php?module=ajax&categ=contribution&sub='.$property->linked_form[0]['form_type'].'&area_id='.$property->linked_form[0]['area_id'].'&id=0&sub_form=1&form_id='.$property->linked_form[0]['form_id'].'&form_uri='.urlencode($property->linked_form[0]['form_uri']);
	// 				$input = str_replace("!!url_linked_form!!", $url, $input);
	// 				$input = str_replace("!!linked_form_title!!", $property->linked_form[0]['form_title'], $input);
				}
			}
			$input .= $ontology_tpl['form_row_content_resource_template'];
			$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
			$row=str_replace("!!onto_row_inputs!!",$input , $row);	
			$row=str_replace("!!onto_row_order!!","0" , $row);	
			$content.=$row;
		}	
	
		$input = '';	
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
		global $msg,$charset,$ontology_tpl;
		
		$form=$ontology_tpl['form_row_hidden'];
		
		$content='';
		
		if(sizeof($datas)){	
			
			$new_element_order=max(array_keys($datas));
			
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
						
			foreach($datas as $key=>$data){
				$row=$ontology_tpl['form_row_content_resource_selector_hidden'];
		
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}				
				$row=str_replace("!!onto_row_content_hidden_display_label!!",htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset) ,$row);
				$row=str_replace("!!onto_row_content_hidden_value!!",htmlentities($data->get_raw_value() ,ENT_QUOTES,$charset) ,$row);
				$row=str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
				$row=str_replace("!!onto_row_order!!",$order , $row);
		
				$content.=$row;
			}
		} else {	
				
			$form=str_replace("!!onto_new_order!!","0" , $form);
					
			$row = $ontology_tpl['form_row_content_resource_selector_hidden'];
			$row = str_replace("!!onto_row_content_hidden_display_label!!", "", $row);
			$row = str_replace("!!onto_row_content_hidden_value!!", "", $row);
			$row = str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
			$row=str_replace("!!onto_row_order!!","0" , $row);
				
			$content.=$row;
		}
		
		if ($flag) {
			$form=$content;
		} else {
			$form=str_replace("!!onto_rows!!",$content ,$form);
		}
				
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
	}
	
} // end of onto_common_datatype_resource_selector_ui
