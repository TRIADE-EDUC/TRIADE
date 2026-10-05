<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_pmbname_ui.class.php,v 1.3 2022/11/22 10:13:15 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($include_path."/templates/onto/ontopmb/onto_ontopmb_datatype_pmbname_ui.tpl.php");

/**
 * class onto_ontopmb_pmbname_ui
 * 
 */
class onto_ontopmb_datatype_pmbname_ui extends onto_common_datatype_small_text_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param property property la propriété concernée
	 * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété 
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
		
		$tab_lang = array();
		$content='';
		$multilingue = "";
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
				
				
				$inside_row=$ontology_tpl['form_row_content_existing_pmbname'];
				
				$inside_row=str_replace("!!onto_row_content_small_text_value!!",htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset) ,$inside_row);
				// AR - 13/09/22 : Ca complexifie plus que ca n'offre quelque chose d'avoir la langue ici...
				$inside_row=str_replace("!!onto_row_combobox_lang!!", "", $inside_row);
				$inside_row=str_replace("!!onto_row_content_small_text_range!!",$property->range[0] , $inside_row);
				
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$row=str_replace("!!onto_row_inputs!!",'' , $row);
				$row=str_replace("!!onto_row_order!!",$order , $row);
				
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!","0" , $form);
			$row=$ontology_tpl['form_row_content'];
			$inside_row=$ontology_tpl['form_row_content_pmbname'];
			$inside_row=str_replace("!!onto_row_content_small_text_value!!","" , $inside_row);
			$inside_row=str_replace("!!onto_row_content_small_text_range!!",$property->range[0] , $inside_row);
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
			$row=str_replace("!!onto_row_inputs!!",'' , $row);
			$row=str_replace("!!onto_row_order!!","0" , $row);
			$content.=$row;
		}
		
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
	} // end of member function get_form	

} // end of onto_common_datatype_ui