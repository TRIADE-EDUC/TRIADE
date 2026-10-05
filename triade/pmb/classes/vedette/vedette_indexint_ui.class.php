<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_indexint_ui.class.php,v 1.5 2020/12/11 16:20:49 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/vedette/vedette_indexint.tpl.php");

class vedette_indexint_ui extends vedette_element_ui{

	
	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params = array(), $suffix = "") {
		global $vedette_indexint_tpl;
		
		return $vedette_indexint_tpl["vedette_indexint_selector" . $suffix];
	}
	
	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 *
	 * @return string
	 */
	public static function get_create_box_js($params = array(), $suffix = ""){
		global $vedette_indexint_tpl;
		if(!in_array('vedette_indexint_script'.$suffix, parent::$created_boxes)){
		    parent::$created_boxes[] = 'vedette_indexint_script'.$suffix;
		    return $vedette_indexint_tpl["vedette_indexint_script".$suffix];
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
