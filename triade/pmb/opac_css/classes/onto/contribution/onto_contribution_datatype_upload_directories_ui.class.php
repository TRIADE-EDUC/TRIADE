<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_upload_directories_ui.class.php,v 1.4.12.1 2025/01/30 09:08:05 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/contribution/onto_contribution_datatype_ui.tpl.php');
require_once($class_path.'/upload_folder.class.php');
require_once($class_path.'/encoding_normalize.class.php');


/**
 * class onto_common_datatype_list_ui
 * 
 */
class onto_contribution_datatype_upload_directories_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	static protected $directories;

	/**
	 * 
	 *
	 * @param property property la propriété concernée
	 * @param restriction $restrictions le tableau des restrictions associées à la propriété 
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
	static public function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
		global $msg,$charset,$ontology_tpl, $ontology_tpl;		
		
		$form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
		
		static::get_directories();
		
		$content='';
		if(is_countable($datas) && sizeof($datas)){
			$i=1;
			$first=true;
			$new_element_order=max(array_keys($datas));
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			foreach ($datas as $data) {
				$row=$ontology_tpl['form_row_content'];
				$inside_row = $ontology_tpl['form_row_content_upload_directories'];
				$inside_row=str_replace("!!form_row_content_upload_directories_display_label!!",htmlentities(addslashes($data->get_formated_value()),ENT_QUOTES,$charset), $inside_row);
				$inside_row=str_replace("!!form_row_content_upload_directories_value!!",$data->get_value(), $inside_row);
				$inside_row=str_replace('!!onto_row_memory_data!!', encoding_normalize::json_encode(static::$directories), $inside_row);
		
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				$row=str_replace("!!onto_row_inputs!!",'' , $row);
		
				$row=str_replace("!!onto_row_order!!",0 , $row);
		
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!", "0", $form);
				
			$row=$ontology_tpl['form_row_content'];
				
			$inside_row = $ontology_tpl['form_row_content_upload_directories'];

			$inside_row=str_replace('!!form_row_content_upload_directories_display_label!!', '', $inside_row);
			$inside_row=str_replace("!!form_row_content_upload_directories_value!!",'', $inside_row);
			$inside_row=str_replace('!!onto_row_memory_data!!', encoding_normalize::json_encode(static::$directories), $inside_row);
			$inside_row=str_replace("!!onto_row_content_list_range!!",$property->range[0] , $inside_row);
				
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
			$row=str_replace("!!onto_row_inputs!!",'' , $row);
			$row=str_replace("!!onto_row_order!!","0" , $row);
			
			$content.=$row;
		}
		
		$form = str_replace("!!onto_rows!!", $content, $form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_row_id!!", $instance_name.'_'.$property->pmb_name, $form);
		return $form;
		
	} // end of member function get_form

	/**
	 * 
	 *
	 * @param onto_common_datatype datas Tableau des valeurs à afficher associées à la propriété
	 * @param property property la propriété à utiliser
	 * @param string instance_name nom de l'instance
	 * 
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
		
	} // end of member function get_display
	
	static public function get_directories() {
		if (isset(static::$directories)) {
			return static::$directories;
		}
		static::$directories = array(
				array(
						'id' => 'root'
				)
		);
		static::get_directories_tree(upload_folder::get_upload_folders(), 'root');
		return static::$directories;
	}
	
	static protected function get_directories_tree($folders, $parent_id)  {
		foreach ($folders as $folder) {
			$folder_id = count(static::$directories);
			static::$directories[] = array(
					'id' => $folder_id,
					'name' => $folder['name'],
					'formatted_path_name' => $folder['formatted_path_name'],
					'formatted_path_id' => $folder['formatted_path_id'],
					'parent' => $parent_id
			);
			if (!empty($folder['sub_folders'])) {
				static::get_directories_tree($folder['sub_folders'], $folder_id);
			}
		}
	}

} // end of onto_common_datatype_ui