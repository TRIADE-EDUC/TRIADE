<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_marclist_selector_ui.class.php,v 1.4.12.1 2025/01/30 09:08:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once($class_path.'/marc_table.class.php');


/**
 * 
 * Add use march & generate the selector
 */

/**
 * class onto_common_datatype_resource_selector_ui
 * 
 */
class onto_common_datatype_marclist_selector_ui extends onto_common_datatype_ui {

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
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset), $form);
		
		$marclist_type = $property->pmb_marclist_type;
		
		$content='';
		$content.=$ontology_tpl['form_row_content_input_sel_pmb'];
		if($restrictions->get_max()<$i || $restrictions->get_max()===-1){
			$content.=$ontology_tpl['form_row_content_input_add_selector_pmb'];
		}
		$content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);
		$content = str_replace("!!onto_selector_url!!", './select.php?what='.$marclist_type.'&caller=', $content);
	
		$content = str_replace("!!onto_pmb_selector_max_card!!", $restrictions->get_max(), $content);
		$content = str_replace("!!onto_pmb_selector_min_card!!", $restrictions->get_min(), $content);
		$content = str_replace("!!max_field_value!!", (count($datas) ? count($datas) : 1), $content);
		
		if(is_countable($datas) && sizeof($datas)){
	
			$i=1;
			$first=true;
			$new_element_order=max(array_keys($datas));
		
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			foreach($datas as $key => $data){
				$label = "";
				$id = "";

				if($data->get_value()){
					$value = str_replace($marclist_type.'_', '',str_replace($data->get_value_type().'_', '', $data->get_value()));
					$label = self::get_display_label($marclist_type, $value);
				}
				$row=$ontology_tpl['form_row_content'];
	
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				$inside_row = $ontology_tpl['form_row_content_resource_selector_pmb'];
				$inside_row = str_replace("!!form_row_content_resource_selector_range!!",htmlentities(addslashes($property->pmb_datatype.'_'.$property->pmb_marclist_type),ENT_QUOTES,$charset), $inside_row);
				$inside_row.= $ontology_tpl['form_row_content_type'];
				$inside_row = str_replace("!!form_row_content_resource_selector_display_label!!",htmlentities(addslashes($label),ENT_QUOTES,$charset), $inside_row);
				$inside_row = str_replace("!!form_row_content_resource_selector_value!!", $value, $inside_row);
				$inside_row = str_replace("!!onto_row_content_range!!",$data->get_value_type() , $inside_row);
// 				$inside_row = str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
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
			$inside_row = str_replace("!!form_row_content_resource_selector_range!!",htmlentities(addslashes($property->pmb_datatype.'_'.$property->pmb_marclist_type),ENT_QUOTES,$charset), $inside_row);
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
		$form = str_replace("!!onto_rows!!", $content, $form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_completion!!", self::get_completion_from_marclist($marclist_type), $form);
	
		$form = str_replace("!!onto_row_id!!", $instance_name.'_'.$property->pmb_name, $form);
	
		return $form;
	} // end of member function get_form
	
	protected static function get_completion_from_marclist($marclist_type){
		
		switch($marclist_type){
			case 'function':
				return 'fonction';
			case 'lang':
				return 'langue';
			case 'country':
				return 'country';
				
		}
	}
	
	protected static function get_display_label($marclist_type, $value){
		$marclist = new marc_select($marclist_type);
		return $marclist->table[$value];
	}
	
	/**
	 
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

} // end of onto_common_datatype_marclist_selector_ui