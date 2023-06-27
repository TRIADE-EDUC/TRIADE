<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_work_selector_ui.class.php,v 1.1 2018-09-24 13:39:22 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once $class_path.'/authority.class.php';
/**
 * class onto_common_datatype_responsability_selector_ui
 * 
 */
class onto_contribution_datatype_linked_work_selector_ui extends onto_common_datatype_resource_selector_ui {

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
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->label, 'utf-8') ,ENT_QUOTES,$charset) , $form);
		
		/** traitement initial du range ?!*/
		$range_for_form = ""; 
		if(is_array($property->range)){
			foreach($property->range as $range){
				if($range_for_form) $range_for_form.="|||";
				$range_for_form.=$range;
			}
		}
		/** **/
		
		/** TODO: à revoir avec le chef ** / 
		/** On part du principe que l'on a qu'un range **/
// 		$selector_url = $this->get_resource_selector_url($property->range[0]);
		
		
		
		$content='';
		$content.=$ontology_tpl['form_row_content_input_sel'];
		
		$content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);
		if(sizeof($datas)){
			$i = 1;
			$first = true;
			$new_element_order = max(array_keys($datas));
			
			$form = str_replace("!!onto_new_order!!",$new_element_order , $form);
			
			foreach($datas as $key=>$data){
				$row = $ontology_tpl['form_row_content'];
				
				if($data->get_order()){
					$order = $data->get_order();
				}else{
					$order = $key;
				}
				$formated_value = $data->get_formated_value();
				$inside_row = $ontology_tpl['form_row_content_linked_record_selector'];
				$inside_row = str_replace("!!form_row_content_linked_record_selector_display_label!!",htmlentities((isset($formated_value['work']['display_label']) ? $formated_value['work']['display_label'] : ""), ENT_QUOTES, $charset) , $inside_row);
				$inside_row = str_replace("!!form_row_content_linked_record_selector_value!!", (isset($formated_value['work']['value']) ? $formated_value['work']['value'] : ""), $inside_row);
				$inside_row = str_replace("!!form_row_content_linked_record_selector_range!!",$data->get_value_type() , $inside_row);
				
				//$selector = notice_relations::get_selector('!!onto_row_id!![!!onto_row_order!!][value]',(isset($formated_value['relation_type_work']) ? $formated_value['relation_type_work']."-".$formated_value['direction'] : ""));
				$selector = static::get_selector("have_expression", '!!onto_row_id!![!!onto_row_order!!][value]', (isset($formated_value['relation_type_work']) ? $formated_value['relation_type_work'] : ""));
				$inside_row = str_replace('!!onto_row_content_linked_record_selector!!', $selector, $inside_row);
				$inside_row = str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
				
				
				$inside_row = str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
				$inside_row = str_replace("!!onto_current_range!!",'http://www.pmbservices.fr/ontology#work',$inside_row);
				
				$row = str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$input = '';
				if($first){
					$input.= $ontology_tpl['form_row_content_input_remove'];
				}else{
					$input.= $ontology_tpl['form_row_content_input_del'];
				}
				$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
				
				$row = str_replace("!!onto_row_inputs!!",$input , $row);
				$row = str_replace("!!onto_row_order!!",$order , $row);
				
				$content.= $row;
				$first = false;
				$i++;
			}
		}else{
			$form = str_replace("!!onto_new_order!!","0" , $form);
			
			$row = $ontology_tpl['form_row_content'];
			
			$inside_row = $ontology_tpl['form_row_content_linked_record_selector'];			
			$inside_row = str_replace("!!form_row_content_linked_record_selector_display_label!!","" , $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_record_selector_value!!","" , $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_record_selector_range!!","" , $inside_row);
			
			$selector = notice_relations::get_selector('!!onto_row_id!![!!onto_row_order!!][value]');
			$inside_row = str_replace('!!onto_row_content_linked_record_selector!!', $selector, $inside_row);
			$inside_row = str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
			
			$inside_row = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri),$inside_row);
			$inside_row = str_replace("!!onto_current_range!!", 'http://www.pmbservices.fr/ontology#work', $inside_row);
			
			$row = str_replace("!!onto_inside_row!!",$inside_row , $row);
			
			$input = '';
			$input.= $ontology_tpl['form_row_content_input_remove'];
			$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
			$row = str_replace("!!onto_row_inputs!!",$input , $row);
				
			$row = str_replace("!!onto_row_order!!","0" , $row);
				
			$content.= $row;
		}
		
		$form = str_replace("!!onto_rows!!",$content ,$form);
		$form = str_replace("!!onto_completion!!",'titres_uniformes', $form);
		$form = str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
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
		$display.=$property->label.' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
	}
	
	
	protected static function get_selector($type, $name, $selected) {
	    global $charset,$msg;
	    
	    $optgroup_list=array();
	    $selector = '<select id="'.$name.'" name="'.$name.'" data-form-name='.substr($name,0,-1).'>';
	    $oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
	    foreach($oeuvre_link->table as $group=>$types) {
	        $options = '';
	        foreach($types as $code => $libelle){
	            //if ($oeuvre_link->attributes[$code]['GROUP'] == $type) {
	                if(!($code == $selected)) {
	                    $options .= "<option value='".$code."'>".$libelle."</option>";
	                } else {
                        $options .= "<option value='".$code."' selected='selected'>".$libelle."</option>";
                    }
	            //}
	        }
	        if($options) $optgroup_list[$group]=$options;
	    }
	    if(count($optgroup_list)>1){
	        foreach ($optgroup_list as $group=>$options) {
	            $selector .= '<optgroup label="'.htmlentities($group,ENT_QUOTES,$charset).'">'.$options.'</optgroup>';
	        }
	    }elseif(count($optgroup_list)){
	        foreach ($optgroup_list as $group=>$options) {
	            $selector.= $optgroup_list[$group];
	        }
	    }else{
	        $selector.= "<option value=''>".$msg['authority_marc_list_empty_filter']."</option>";
	    }
	    $selector.= '</select>';
	    
	    return $selector;
	}

} // end of onto_common_datatype_responsability_selector_ui
