<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_thumbnail.class.php,v 1.7.2.1 2024/09/16 13:52:30 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';
require_once $class_path.'/upload_folder.class.php';
require_once $class_path.'/explnum.class.php';


/**
 * class onto_common_datatype_small_text
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_contribution_datatype_thumbnail extends onto_common_datatype_file {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	
	public function check_value(){
	    if (is_string($this->value) && (strlen($this->value) < 131072)) return true;
		return false;
	}
	
	public static function get_values_from_form($instance_name, $property, $uri_item) {
		$var_name = $instance_name."_".$property->pmb_name;
		global $form_id, $area_id, $pmb_contribution_opac_docnum_directory;
		global ${$var_name};

		$file = array();
		if (isset($_FILES[$instance_name."_".$property->pmb_name]) && $_FILES[$instance_name."_".$property->pmb_name]["name"][0]["value"] != "" && $_FILES[$instance_name."_".$property->pmb_name]["tmp_name"][0]["value"] != "") {
			$file = $_FILES[$instance_name."_".$property->pmb_name];
		}
		//TODO: Revoir si on ajoute la suppression de la vignette
		if(count($file)) {
			
            $filename = "vign_".md5(microtime())."_".explnum::clean_explnum_file_name($file['name'][0]['value']);
			$upload_directory = new upload_folder($pmb_contribution_opac_docnum_directory);
			$rep_path = $upload_directory->repertoire_path;
			$path = "espace_".$area_id."/form_".$form_id."/thumbnail/";	
			// Vérifie si le répertoire existe :
			if (!is_dir($rep_path.$path)) {
			    mkdir($rep_path.$path, 0777, true);
			}
			$complete_path = $path.$filename;
            $image = imagecreatefromstring(file_get_contents($file['tmp_name'][0]['value']));
            imagepng($image, $rep_path.$complete_path);
			$values[] = array(
			    'value' => json_encode(["name"=>  $file['name'][0]['value'], "id_upload_directory" => $pmb_contribution_opac_docnum_directory, "path" => $complete_path]),
				'type' => $_POST[$var_name][0]['type']
			);
			${$var_name} = $values;
		} elseif (
		    isset(${$var_name}) &&
		    isset(${$var_name}[0]) &&
		    ${$var_name}[0]['value'] == "" &&
		    !empty($_POST[$var_name][0]['data'])
		) {
			$values[] = array(
			    'value' => urldecode($_POST[$var_name][0]['data']),
				'type' => $_POST[$var_name][0]['type']
			);
		    ${$var_name} = $values;
		}
		return parent::get_values_from_form($instance_name, $property, $uri_item);
	}
	
	public static function get_valid_file_path($file_path) {
		$file_path = str_replace('//', '/', $file_path);
		if (!file_exists($file_path)) {
			return $file_path;
		}
		$i = 1;
		$file_info = pathinfo($file_path);
		do {
			$file_path = $file_info['dirname'].'/'.$file_info['filename'].'_'.$i.'.'.$file_info['extension'];
			$i++;
		} while (file_exists($file_path));
		return $file_path;
	}
	
	public function get_value($all = false) {
	    switch(true){
	        case is_string($this->value):
	            $decoded = json_decode($this->value);
	            break;
	        case is_array($this->value):
	            return "";
	    }
	    
	    if ($all && $decoded) {
	        if (is_object($decoded) && isset($decoded->id_upload_directory) && isset($decoded->path)) {
	            $folder = new upload_folder($decoded->id_upload_directory);
	            $thumbnail_path = $folder->repertoire_path.$decoded->path;
	            if (is_file($thumbnail_path) && is_readable($thumbnail_path)) {
    	            $decoded->thumbnail = file_get_contents($thumbnail_path);
	            }
	        }
	        return $decoded;
	    } 
        return $decoded->name;
	}
	
	public function get_data() {
	    return $this->value;
	}
} // end of onto_common_datatype_small_text