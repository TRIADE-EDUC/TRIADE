<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_authors_ui.class.php,v 1.5 2015-12-14 09:29:09 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/vedette/vedette_authors.tpl.php");

class vedette_authors_ui extends vedette_element_ui{

	
	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params=array()){
		global $vedette_authors_tpl;
		return $vedette_authors_tpl["vedette_authors_selector"];
	}
	
	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 *
	 * @return string
	 */
	public static function get_create_box_js($params=array()){
		global $vedette_authors_tpl;
		if(!in_array('vedette_authors_script', parent::$created_boxes)){
			array_push(parent::$created_boxes, 'vedette_authors_script');
			return $vedette_authors_tpl["vedette_authors_script"];
		}
	}
	
	/**
	 * Renvoie les données (id objet, type)
	 *
	 * @return void
	 * @access public
	 */
	public static function get_from_form(){
	
	}
}
