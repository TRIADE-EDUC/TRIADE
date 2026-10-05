<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_grammars.class.php,v 1.2 2020/12/22 16:55:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/vedette/vedette_grammars.tpl.php');

class vedette_grammars {
	private static $grammars;
	
	private static $grammars_by_entity;
	
	/**
	 * Retourne le tableau des grammaires disponibles
	 * @return array
	 */
	public static function get_grammars() {
		global $include_path;
		if(!isset(self::$grammars)) {
			self::$grammars = array();
			$dh = opendir($include_path.'/vedette');
			while(($file = readdir($dh)) !== false){
				if($file == '.' || $file=='..' || $file =='CVS'){
					continue;
				}
				$grammar = basename(basename($file,".xml"),"_subst");
				if(!in_array($grammar,self::$grammars)){
					self::$grammars[] = $grammar;
				}
			}
			closedir($dh);
		}
		return self::$grammars;
	}
	
	/**
	 * Retourne le formulaire d'association des grammaires avec les entités PMB
	 * @return string
	 */
	public function get_grammars_by_entity_form() {
		global $vedette_grammars_by_entity_form, $vedette_grammars_by_entity_row;
		global $vedette_grammars_by_entity_selector, $vedette_grammars_by_entity_selector_option;
		global $charset;
		
		self::get_grammars_by_entity();
		
		$html = $vedette_grammars_by_entity_form;
		$vedette_grammars_by_entity_rows = '';
		
		$entities_labels = entities::get_entities_labels();
		asort($entities_labels);
		
		foreach ($entities_labels as $entity => $label) {
			$row = $vedette_grammars_by_entity_row;
			$row = str_replace('!!entity_name!!', htmlentities($label, ENT_QUOTES, $charset), $row);
			
			$grammars_selector = $vedette_grammars_by_entity_selector;
			$grammars_selector_options = '';
			foreach (static::get_grammars() as $grammar) {
				$option = $vedette_grammars_by_entity_selector_option;
				$option = str_replace('!!grammar_selector_option_value!!', $grammar, $option);
				$option = str_replace('!!grammar_selector_option_label!!', $grammar, $option);
				
				$selected = '';
				if (!empty(self::$grammars_by_entity[$entity]) && in_array($grammar, self::$grammars_by_entity[$entity])) {
					$selected = 'selected="selected"';
				}
				$option = str_replace('!!selected!!', $selected, $option);
				
				$grammars_selector_options.= $option;
			}
			$grammars_selector = str_replace('!!grammar_selector_options!!', $grammars_selector_options, $grammars_selector);
			$grammars_selector = str_replace('!!grammar_selector_name!!', 'grammars_by_entity['.$entity.'][]', $grammars_selector);
			
			$row = str_replace('!!grammars_selector!!', $grammars_selector, $row);
			$vedette_grammars_by_entity_rows.= $row;
		}
		$html = str_replace('!!vedette_grammars_by_entity_rows!!', $vedette_grammars_by_entity_rows, $html);
		
		return $html;
	}
	
	/**
	 * Récupère les valeurs postée du formulaire d'association des grammaires avec les entités PMB
	 */
	public function set_grammars_by_entity_from_form() {
		global $grammars_by_entity;
		
		self::$grammars_by_entity = $grammars_by_entity;
	}
	
	/**
	 * Sauvegarde l'association des grammaires avec les entités PMB
	 */
	public function save_grammars_by_entity() {
		pmb_mysql_query('TRUNCATE TABLE vedette_grammars_by_entity');
		
		$values = array();
		foreach (self::$grammars_by_entity as $entity => $grammars) {
			foreach ($grammars as $grammar) {
				$values[] = '('.$entity.', "'.$grammar.'")';
			}
		}
		if (count($values)) {
			$query = 'INSERT INTO vedette_grammars_by_entity (entity_type, grammar) VALUES '.implode(',', $values);
			pmb_mysql_query($query);
		}
	}
	
	/**
	 * Retourne les grammaires à utiliser par entité
	 * Si une entité est précisée, ne retourne que les grammaires pour cette entité
	 * @param number $entity Constante d'entité de type TYPE_NOTICE, TYPE_AUTHOR, ...
	 * @return array
	 */
	public static function get_grammars_by_entity($entity = 0) {
		if (isset(self::$grammars_by_entity)) {
			return self::$grammars_by_entity;
		}
		self::$grammars_by_entity = array();
		
		$query = 'SELECT entity_type, grammar FROM vedette_grammars_by_entity';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				if (!isset(self::$grammars_by_entity[$row['entity_type']])) {
					self::$grammars_by_entity[$row['entity_type']] = array();
				}
				self::$grammars_by_entity[$row['entity_type']][] = $row['grammar'];
			}
		}
		return self::$grammars_by_entity;
	}
}