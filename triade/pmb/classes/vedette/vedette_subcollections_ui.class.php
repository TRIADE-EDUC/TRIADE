<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_subcollections_ui.class.php,v 1.6 2020/12/11 16:20:49 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/vedette/vedette_subcollections.tpl.php");

class vedette_subcollections_ui extends vedette_element_ui{

	
	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params = array(), $suffix = "") {
		global $vedette_subcollections_tpl;
		
		return $vedette_subcollections_tpl["vedette_subcollections_selector" . $suffix];
	}
	
	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 *
	 * @return string
	 */
	public static function get_create_box_js($params = array(), $suffix = ""){
		global $vedette_subcollections_tpl;
		if(!in_array('vedette_subcollections_script'.$suffix, parent::$created_boxes)){
		    parent::$created_boxes[] = 'vedette_subcollections_script'.$suffix;
		    return $vedette_subcollections_tpl["vedette_subcollections_script".$suffix];
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
