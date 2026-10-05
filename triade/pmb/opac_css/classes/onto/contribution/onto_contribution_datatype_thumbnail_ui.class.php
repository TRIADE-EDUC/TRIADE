<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_thumbnail_ui.class.php,v 1.5 2021/12/20 15:53:01 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/contribution/onto_contribution_datatype_ui.tpl.php');

/**
 * class onto_common_datatype_small_text_ui
 * 
 */
class onto_contribution_datatype_thumbnail_ui extends onto_common_datatype_file_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param onto_common_property property la propriété concernée
	 * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété 
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
		global $ontology_tpl;

		$form = parent::get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag);
		$form.= $ontology_tpl['onto_contribution_datatype_docnum_file_script'];

		//recomposition des infos pour mettre dans le champ caché
		$data = [
		    "name"=>  "",
		    "id_upload_directory" => "",
		    "path" => ""
		];
		if (!empty($datas) && !empty($datas[0])) {
		    $info = $datas[0]->get_value(true);
		    if (!empty($info)) {
		        $data["name"] = $info->name;
		        $data["id_upload_directory"] = $info->id_upload_directory;
		        $data["path"] = $info->path;
		    }
		}
		$form = str_replace('!!onto_contribution_file_template!!', self::get_template($datas), $form);
		$form = str_replace('!!onto_row_content_file_data!!', urlencode(json_encode($data)), $form);
		$form = str_replace('!!instance_name!!', $instance_name, $form);
		$form = str_replace('!!property_name!!', $property->pmb_name, $form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
	} // end of member function get_form
	
	
	/**
	 * Retourne le template pour une ligne
	 *
	 * @param string $item_uri
	 * @param onto_common_property $property
	 * @param string $range
	 * @param string|int $order
	 * @param array $data
	 * @param string|boolean $is_draft
	 * @return mixed
	 */
	private static function get_template($datas)
	{
	    if (!empty($datas) && !empty($datas[0])) {
	        $info = $datas[0]->get_value(true);
	        if (!empty($info) && $info->thumbnail) {
	            return "<img src=data:image/png;base64,".$info->thumbnail."></img>";
	        }
	    }
	    return '';
	}
	
} // end of onto_common_datatype_ui