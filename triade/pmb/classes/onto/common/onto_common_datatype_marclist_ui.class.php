<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_marclist_ui.class.php,v 1.5 2018-10-23 14:48:35 apetithomme Exp $

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
class onto_common_datatype_marclist_ui extends onto_common_datatype_ui {

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
		
		$marc_list = new marc_list($property->pmb_marclist_type);
		$content='';
		$list_values_to_display = static::get_list_values_to_display($property);
		if(sizeof($datas)){
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
				$inside_row=$ontology_tpl['form_row_content_marclist'];
				$options = '';
				foreach($marc_list->table as $value => $label){
					$display_none = '';
					if (count($list_values_to_display) && !in_array($value, $list_values_to_display)) {
						$display_none = 'style="display:none;"';
					}
					$options.= '<option value="'.$value.'" '.($data->get_formated_value() == $value ? 'selected=selected' : '').' '.$display_none.'>'.htmlentities($label,ENT_QUOTES,$charset).'</option>';
				}
				/*generate rows *///htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset)
				$inside_row=str_replace('!!onto_row_content_marclist_options!!', $options, $inside_row);
				$inside_row=str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
		
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
		
				$input='';
				if($first){
					if($restrictions->get_max()<$i || $restrictions->get_max()===-1){
						$input=$ontology_tpl['form_row_content_input_add'];
					}
				}else{
					$input=$ontology_tpl['form_row_content_input_del'];
				}
		
				$row=str_replace("!!onto_row_inputs!!",$input , $row);
				$row=str_replace("!!onto_row_order!!",$order , $row);
		
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!","0" , $form);
				
			$row=$ontology_tpl['form_row_content'];
				
			$inside_row=$ontology_tpl['form_row_content_marclist'];

			$options = '';
			foreach($marc_list->table as $value => $label){
				$display_none = '';
				if (count($list_values_to_display) && !in_array($value, $list_values_to_display)) {
					$display_none = 'style="display:none;"';
				}
				$options.= '<option value="'.$value.'" '.$display_none.' >'.$label.'</option>';
			}
			
			$inside_row=str_replace("!!onto_row_content_marclist_options!!", $options, $inside_row);
			$inside_row=str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
				
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
			$input='';
			if($restrictions->get_max()!=1){
				$input=$ontology_tpl['form_row_content_input_add'];
			}
			$row=str_replace("!!onto_row_inputs!!",$input , $row);
				
			$row=str_replace("!!onto_row_order!!","0" , $row);
				
			$content.=$row;
		}
		
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;		
		
	} // end of member function get_form
	
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
		$display.=$property->label.' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
		
	}
	
	/**
	 * A dériver pour filtrer la liste des valeurs à afficher dans le sélecteur
	 * @return array
	 */
	public static function get_list_values_to_display($property) {
		return array();
	}

} // end of onto_common_datatype_resource_selector_ui
