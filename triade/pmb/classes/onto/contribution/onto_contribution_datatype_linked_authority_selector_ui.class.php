<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_authority_selector_ui.class.php,v 1.2 2019-04-19 12:23:44 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once $class_path.'/authority.class.php';

class onto_contribution_datatype_linked_authority_selector_ui extends onto_common_datatype_resource_selector_ui {
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
		
		$marc_list = marc_list_collection::get_instance('aut_link');
		$tmp = array();
		if (count($marc_list->inverse_of)) {
		    // sous tableau genre ascendant descendant...
		    foreach ($marc_list->table as $table) {
		        $tmp = array_merge($tmp, $table);
		    }
		    $marc_list->table = $tmp;
		} 
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
				$inside_row = $ontology_tpl['form_row_content_linked_authority_selector'];				
				$inside_row = str_replace("!!form_row_content_linked_authority_selector_display_label!!",htmlentities((isset($formated_value['authority']['display_label']) ? $formated_value['authority']['display_label'] : ""), ENT_QUOTES, $charset) , $inside_row);
				$inside_row = str_replace("!!form_row_content_linked_authority_selector_value!!", (isset($formated_value['authority']['value']) ? $formated_value['authority']['value'] : ""), $inside_row);
				$inside_row = str_replace("!!form_row_content_linked_authority_selector_range!!",$data->get_value_type() , $inside_row);
				
				$options = '';
				foreach($marc_list->table as $value => $label){
				    $options.= '<option value="'.$value.'" '.(isset($formated_value['relation_type_authority']) && $formated_value['relation_type_authority'] == $value ? 'selected=selected>' : '>').htmlentities($label,ENT_QUOTES,$charset).'</option>';
				}
				/*generate rows *///htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset)
				$inside_row=str_replace('!!onto_row_content_marclist_options!!', $options, $inside_row);
				$inside_row = str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
				
				
				$inside_row = str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
				$inside_row = str_replace("!!onto_current_range!!",'http://www.pmbservices.fr/ontology#authority',$inside_row);
				
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
			
			$inside_row = $ontology_tpl['form_row_content_linked_authority_selector'];			
			$inside_row = str_replace("!!form_row_content_linked_authority_selector_display_label!!","" , $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_authority_selector_value!!","" , $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_authority_selector_range!!","" , $inside_row);
			
			$options = '';
			foreach($marc_list->table as $value => $label){
			    $options.= '<option value="'.$value.'" >'.htmlentities($label,ENT_QUOTES,$charset).'</option>';
			}
			/*generate rows *///htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset)
			$inside_row=str_replace('!!onto_row_content_marclist_options!!', $options, $inside_row);
			$inside_row = str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
			
			$inside_row = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri),$inside_row);
			$inside_row = str_replace("!!onto_current_range!!", 'http://www.pmbservices.fr/ontology#authority', $inside_row);
			
			$row = str_replace("!!onto_inside_row!!",$inside_row , $row);
			
			$input = '';
			$input.= $ontology_tpl['form_row_content_input_remove'];
			$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
			$row = str_replace("!!onto_row_inputs!!",$input , $row);
				
			$row = str_replace("!!onto_row_order!!","0" , $row);
				
			$content.= $row;
		}
		
		$form = str_replace("!!onto_rows!!",$content ,$form);
		$form = str_replace("!!onto_completion!!",'authority', $form);
		$form = str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
	}
} // end of onto_common_datatype_responsability_selector_ui
