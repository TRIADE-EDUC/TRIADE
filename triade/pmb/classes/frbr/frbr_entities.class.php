<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entities.class.php,v 1.4 2022/04/15 12:16:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/frbr/frbr_entities_parser.class.php");

class frbr_entities {
	
	/**
	 * Liste des entités
	 */
	protected static $entities;
	
	/**
	 * Constructeur
	 */
	public function __construct() {
	}
	
	/**
	 * Sélecteur des entités
	 */
	public static function get_selector($name, $selected = '', $onchange = '') {
		global $charset;

		$entities_parser = new frbr_entities_parser();
		$managed_entities = $entities_parser->get_managed_entities();
		$selector = "<select name='".$name."' onchange=\"".$onchange."\">";
		foreach($managed_entities as $directory_name=>$managed_entity){
			$selector .= "<option value='".$directory_name."' ".($selected == $directory_name ? "selected='selected'" : "").">".htmlentities($managed_entity['name'], ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	public static function get_hidden_field($name, $selected = '') {
		global $charset;
		
		$entities_parser = new frbr_entities_parser();
		$managed_entities = $entities_parser->get_managed_entities();
		$hidden_field = "<input type='hidden' name='".$name."' value='".htmlentities($selected, ENT_QUOTES, $charset)."' />";
		$hidden_field .= htmlentities($managed_entities[$selected]['name'], ENT_QUOTES, $charset);
		return $hidden_field;
	}
}