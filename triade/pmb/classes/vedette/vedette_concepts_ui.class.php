<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_concepts_ui.class.php,v 1.7 2020/12/11 16:20:48 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/vedette/vedette_concepts.tpl.php");

class vedette_concepts_ui extends vedette_element_ui{
	
	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params = array(), $suffix = "") {
		global $vedette_concepts_tpl;
		
		$html = $vedette_concepts_tpl["vedette_concepts_selector" . $suffix];
		$html = str_replace('!!concept_scheme!!', (!empty($params['concept_scheme']) ? $params['concept_scheme'] : 0), $html);
		
		return $html;
	}
	
	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 *
	 * @return string
	 */
	public static function get_create_box_js($params = array(), $suffix = ""){
		global $vedette_concepts_tpl;
		if(!in_array('vedette_concepts_script'.$suffix, parent::$created_boxes)){
			parent::$created_boxes[] = 'vedette_concepts_script'.$suffix;
			return $vedette_concepts_tpl["vedette_concepts_script".$suffix];
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
