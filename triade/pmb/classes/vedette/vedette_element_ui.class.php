<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_element_ui.class.php,v 1.5 2020/12/11 16:20:49 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

abstract class vedette_element_ui {

	
	protected static $created_boxes = array();
	
	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params = array(), $suffix = "") {
		
	}

	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 * 
	 * @return string
	 */
	public static function get_create_box_js($params=array(), $suffix = ""){
		
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
