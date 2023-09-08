<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_ontologies_ui.class.php,v 1.3 2015-08-11 10:22:43 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/vedette/vedette_ontologies.tpl.php");

class vedette_ontologies_ui extends vedette_element_ui{
	
	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params = array()){
		global $vedette_ontologies_tpl;
		$tpl = $vedette_ontologies_tpl["vedette_ontologies_selector"];
		$tpl = str_replace("!!ontology_label!!", $params['label'],$tpl);
		$tpl = str_replace("!!ontology_id!!", $params['id_ontology'],$tpl);
		$tpl = str_replace("!!ontology_num!!", $params['num'],$tpl);
		$tpl = str_replace("!!ontology_pmbname!!", $params['pmbname'],$tpl);
		return $tpl;
	}
	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 *
	 * @return string
	 */
	public static function get_create_box_js($params= array()){
		global $vedette_ontologies_tpl;
		$tpl = $vedette_ontologies_tpl["vedette_ontologies_script"];
		$tpl = str_replace("!!ontology_label!!", $params['label'],$tpl);
		$tpl = str_replace("!!ontology_id!!", $params['id_ontology'],$tpl);
		$tpl = str_replace("!!ontology_num!!", $params['num'],$tpl);
		$tpl = str_replace("!!ontology_pmbname!!", $params['pmbname'],$tpl);
		return $tpl;
	}
	
	/**
	 * Renvoie le nom de la classe JS
	 *
	 * @return string
	 */
	public static function get_js_class_name($params= array()){
		return "vedette_ontologies".$params['num'];
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
