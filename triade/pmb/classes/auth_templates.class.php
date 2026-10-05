<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: auth_templates.class.php,v 1.5 2022/12/06 11:27:32 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
include_once($include_path."/templates/auth_templates.tpl.php");

class auth_templates {
	
	public static function show_form(){
		global $auth_template_form;
 		return str_replace('!!options!!', static::get_directories_options(), $auth_template_form);
	}
	
	public static function save_form(){
		global $auth_tpl_folder_choice, $opac_authorities_templates_folder;
		if(isset($auth_tpl_folder_choice) && '' !== $auth_tpl_folder_choice){
			$auth_tpl_folder_choice = addslashes($auth_tpl_folder_choice);

			//Update directement le parametre sur le nom
			$requete = 'update parametres set ';
			$requete .= 'valeur_param="'.$auth_tpl_folder_choice.'" ';
			$requete .= "where type_param='opac' ";
			$requete .= "and sstype_param='authorities_templates_folder'";
			$res = pmb_mysql_query($requete);
			
			if($res){
				$opac_authorities_templates_folder = $auth_tpl_folder_choice;
				return true;
			}
			return false;
		}
		return false;
	}
	
	public static function get_directories_options($selected = '') {
		global $msg, $opac_authorities_templates_folder;
		
		if (!$selected) {
			$selected = $opac_authorities_templates_folder;
		}
		$dirs = array_filter(glob('./opac_css/includes/templates/authorities/*'), 'is_dir');
		$tpl = "";
		foreach($dirs as $dir){
			if(basename($dir) != "CVS"){
				$tpl.= "<option ".(basename($dir) == basename($selected) ? "selected='selected'" : "")." value='".basename($dir)."'>
				".(basename($dir) == "common" ? basename($dir)." (".$msg['proc_options_default_value'].")" : basename($dir))."</option>";
			}
		}
		return $tpl;
	}
	
	public static function get_directories() {
		$result = array();
		$dirs = array_filter(glob('./opac_css/includes/templates/authorities/*'), 'is_dir');
		foreach($dirs as $dir){
			if(basename($dir) != "CVS"){
				$result[] = basename($dir);
			}
		}
		return $result;
	}
}