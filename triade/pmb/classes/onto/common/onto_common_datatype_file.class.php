<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_file.class.php,v 1.2 2017/09/13 12:38:32 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';
require_once $class_path.'/storages/storages.class.php';
require_once $class_path.'/ontology.class.php';
require_once $class_path.'/onto/onto_files.class.php';

/**
 * class onto_common_datatype_small_text
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_common_datatype_file extends onto_common_datatype {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	
	public function check_value(){
// 		if (is_string($this->value) && (strlen($this->value) < 256)) return true;
		if ($this->value*1) return true;
		return false;
	}
	
	public static function get_values_from_form($instance_name, $property, $uri_item) {
		$field_name = $instance_name."_".$property->pmb_name;
		$params = $property->get_framework_params();
		if (!isset($params->ontology_id)) {
			return parent::get_values_from_form($instance_name, $property, $uri_item);
		}
		$ontology_id = $params->ontology_id;
		$ontology = new ontology($ontology_id);
		$storage_id = $ontology->get_storage_id();
		$object_id = onto_common_uri::get_id($uri_item);
		
		global ${$field_name};
		if($storage_id){ //On a une méthode de stockage -> Upload du document possible
			/* @var $storage storage */
			$storage = storages::get_storage_class($storage_id);
			if($storage){
				$existing_documents = onto_files::get_existing_documents_from_object("ontology".$ontology_id, $object_id);
				$filenames = $storage->upload_process(false, $field_name);
				if($filenames){
					$values = array();
					foreach($filenames as $i => $filename){
						if (!$filename && $_POST[$field_name][$i]['onto_file_id']) {
							if (in_array($_POST[$field_name][$i]['onto_file_id'], $existing_documents)) {
								unset($existing_documents[array_search($_POST[$field_name][$i]['onto_file_id'], $existing_documents)]);
							}
							$values[$i]['value'] = $_POST[$field_name][$i]['onto_file_id'];
						}
						if (!$values[$i]['value']) {
							$values[$i]['value'] = self::add_onto_file($storage->get_uploaded_fileinfos($filename), "ontology".$ontology_id, $object_id);
						}
						$values[$i]['type'] = $property->range[0];
					}
					${$field_name} = $values;
				}
				// On supprime les documents qui ne sont plus dans le formulaire
				foreach ($existing_documents as $document_id) {
					$onto_file = new onto_files($document_id);
					$onto_file->delete();
				}
			}
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
	
	public static function add_onto_file($infos, $ontology_name, $object_id){
		$document = new onto_files();
		$document->set_title($infos['title'])
			->set_filename($infos['filename'])
			->set_mimetype($infos['mimetype'])
			->set_filesize($infos['filesize'])
			->set_vignette($infos['vignette'])
			->set_url($infos['url'])
			->set_path($infos['path'])
			->set_create_date($infos['create_date'])
			->set_num_storage($infos['num_storage'])
			->set_type_object($ontology_name)
			->set_num_object($object_id);
		$document->save();
		return $document->get_id();
	}
} // end of onto_common_datatype_small_text
