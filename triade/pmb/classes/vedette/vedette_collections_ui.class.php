<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_collections_ui.class.php,v 1.4 2015-12-10 11:18:08 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/vedette/vedette_collections.tpl.php");

class vedette_collections_ui extends vedette_element_ui{

	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params = array()){
		global $vedette_collections_tpl;
		return $vedette_collections_tpl["vedette_collections_selector"];
	}
	
	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 *
	 * @return string
	 */
	public static function get_create_box_js($params = array()){
		global $vedette_collections_tpl;
		if(!in_array('vedette_collections_script', parent::$created_boxes)){
			array_push(parent::$created_boxes, 'vedette_collections_script');
			return $vedette_collections_tpl["vedette_collections_script"];
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
