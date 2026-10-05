<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_authpersos_ui.class.php,v 1.5 2023/01/03 15:11:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/vedette/vedette_authpersos.tpl.php");

class vedette_authpersos_ui extends vedette_element_ui{
	
	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params = array(), $suffix = "") {
		global $vedette_authpersos_tpl;
		
		$tpl = $vedette_authpersos_tpl["vedette_authpersos_selector" . $suffix];
		$tpl = str_replace("!!authperso_label!!", $params['label'],$tpl);
		$tpl = str_replace("!!authperso_id!!", $params['id_authority'],$tpl);
		$tpl = str_replace("!!id_authority!!", $params['id_authority'],$tpl);
		return $tpl;
	}
	
	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 *
	 * @return string
	 */
	public static function get_create_box_js($params= array(), $suffix = ""){
		global $vedette_authpersos_tpl;
		if(!in_array('vedette_authpersos_script'.$suffix, parent::$created_boxes)){
			parent::$created_boxes[] = 'vedette_authpersos_script'.$suffix;
			return $vedette_authpersos_tpl["vedette_authpersos_script".$suffix];;
		}
		return '';
	}
	
	/**
	 * Renvoie les données (id objet, type)
	 *
	 * @return void
	 * @access public
	 */
	public static function get_from_form($params = array()){
	
	}
}
