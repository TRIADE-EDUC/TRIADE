<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: form_mapper.class.php,v 1.4 2017/11/23 11:30:22 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class form_mapper {
  
	public function __construct(){

	}	
	
	/**
	 * Fonction retournant l'instance de form_mapper associé au type passé en paramètre
	 * @param String $source
	 * @return form_mapper|boolean
	 */
	public static function getMapper($source){
		global $pmb_authority_mapping_folder, $class_path;
		if($pmb_authority_mapping_folder){
			if(is_dir($class_path.'/form_mapper/'.$pmb_authority_mapping_folder)){
				if(file_exists($class_path.'/form_mapper/'.$pmb_authority_mapping_folder.'/'.$source.'_form_mapper.class.php')){
					require_once($class_path.'/form_mapper/'.$pmb_authority_mapping_folder.'/'.$source.'_form_mapper.class.php');
					$class = $source.'_form_mapper';
					return new $class();
				}
			}
		}
		return false;
	}
	
	/**
	 * Fonction redérivée dans les classes enfants
	 */
	public function getMapping($dest){
		//fonction dérivée dans les classes enfants
	}
	
	/**
	 * Fonction redérivée dans les classes enfants
	 */
	public function getDestinations(){
		//fonction dérivée dans les classes enfants
	}
	
	//L'appel à isMapped est faitt dans le template de l'entité dest (tu dans notre cas)
	//donc on a pas de mapper tu pour le cd44, dinc ça ne marche pas..
    //
	public static function isMapped($dest){
		global $pmb_authority_mapping_folder, $class_path;
		if($pmb_authority_mapping_folder){
			$directory = $class_path.'/form_mapper/'.$pmb_authority_mapping_folder; 
			if(is_dir($directory)){
				$destinations = array();
				$handle = opendir($directory);
				while(false !== ($filename = readdir($handle))){
					$fullPath = $directory.'/'.$filename;
					if(is_file($fullPath)){
						require_once($fullPath);
						$class = str_replace('.class.php', '', $filename);
						$mapper = new $class();
						$destinations = array_merge($destinations,$mapper->getDestinations());
					}
				}
				if(in_array($dest, $destinations)){
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Fonction générant les boutons de création
	 */
	public static function get_action_button($source, $id) {
		$button = '';
	    $mapper = form_mapper::getMapper($source);
		if($mapper) {
			$mapper->setId($id);
			$destinations = $mapper->getDestinations();
			$i = 0;
			foreach($destinations as $destination){
			    $profile = $mapper->getProfiles($destination);
				if($profile){
					if($profile[0]['is_dropdown']){
						$button.= '<input type="button" id="dropDown_'.$i.'" class="bouton" value="'.$profile[0]['label'].'"/>';
						$button.= '<div data-dojo-type="dijit/Tooltip" data-dojo-props="connectId:\'dropDown_'.$i.'\', position:[\'below\']">';
						foreach($profile[0]['profiles'] as $profile_dropdown){
							$button.= '<input class="bouton" type="button" onclick="window.open(\''.$profile_dropdown['url'].'\', \'_blank\')" value="'.$profile_dropdown['label'].'"><br/>';
						}
						$button.= '</div>';
					}else{
						$button.= '<input type="button" class="bouton" value="'.$profile[0]['profiles']['label'].'" onclick="window.open(\''.$profile[0]['profiles']['url'].'\', \'_blank\')" />&nbsp;';
					}
				}
			}
		}
		return $button;
	}
}


