<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_schemes.class.php,v 1.3 2020/04/22 15:14:25 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/vedette/vedette_schemes.tpl.php');

class vedette_schemes {
	private static $schemes;
	
	private static $scheme_by_entity;
	
	/**
	 * Retourne le tableau des schémas disponibles
	 * @return array
	 */
	public static function get_schemes() {
		global $lang;
		if(!isset(self::$schemes)) {
			self::$schemes = array();
			$query = "select value, lang, authority_num from skos_fields_global_index where code_champ = 4 and code_ss_champ = 1";
			$last_values = array();
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_assoc($result)) {
					if ($row['lang'] == substr($lang,0,2)) {
						self::$schemes[$row['authority_num']] = $row['value'];
						break;
					}
					$last_values[$row['authority_num']] = $row['value'];
				}
				//pas de langue de l'interface trouvée
				foreach ($last_values as $scheme_id => $last_value) {
					if (!isset(self::$schemes[$scheme_id])) {
						self::$schemes[$scheme_id] = $last_value;
					}
				}
			}
		}
		return self::$schemes;
	}
	
	/**
	 * Retourne le formulaire d'association des schémas avec les entités PMB
	 * @return string
	 */
	public function get_scheme_by_entity_form() {
		global $vedette_scheme_by_entity_form, $vedette_scheme_by_entity_row;
		global $vedette_scheme_by_entity_selector, $vedette_scheme_by_entity_selector_option;
		global $charset, $msg;
		
		self::get_scheme_by_entity();
		
		$html = $vedette_scheme_by_entity_form;
		$vedette_scheme_by_entity_rows = '';
		
		$entities_labels = entities::get_entities_labels();
		$authpersos = authpersos::get_authpersos();
		foreach ($authpersos as $authperso) {
			$entities_labels['100' . $authperso['id']] = $authperso['name'];
		}
		unset($entities_labels[TYPE_AUTHPERSO]);
		asort($entities_labels);
		
		foreach ($entities_labels as $entity => $label) {
			$row = $vedette_scheme_by_entity_row;
			$row = str_replace('!!entity_name!!', htmlentities($label, ENT_QUOTES, $charset), $row);
			
			$schemes_selector = $vedette_scheme_by_entity_selector;
			$schemes_selector_options = '';
			
			// Option sans schéma
			$option = $vedette_scheme_by_entity_selector_option;
			$option = str_replace('!!scheme_selector_option_value!!', '0', $option);
			$option = str_replace('!!scheme_selector_option_label!!', $msg['skos_view_concept_no_scheme'], $option);
			$option = str_replace('!!selected!!', (empty(self::$scheme_by_entity[$entity]) ? 'selected="selected"' : ''), $option);
			$schemes_selector_options.= $option;
			
			foreach (static::get_schemes() as $scheme_id => $scheme_label) {
				$option = $vedette_scheme_by_entity_selector_option;
				$option = str_replace('!!scheme_selector_option_value!!', $scheme_id, $option);
				$option = str_replace('!!scheme_selector_option_label!!', $scheme_label, $option);
				
				$selected = '';
				if (!empty(self::$scheme_by_entity[$entity]) && ($scheme_id == self::$scheme_by_entity[$entity])) {
					$selected = 'selected="selected"';
				}
				$option = str_replace('!!selected!!', $selected, $option);
				
				$schemes_selector_options.= $option;
			}
			$schemes_selector = str_replace('!!scheme_selector_options!!', $schemes_selector_options, $schemes_selector);
			$schemes_selector = str_replace('!!scheme_selector_name!!', 'scheme_by_entity['.$entity.']', $schemes_selector);
			
			$row = str_replace('!!schemes_selector!!', $schemes_selector, $row);
			$vedette_scheme_by_entity_rows.= $row;
		}
		$html = str_replace('!!vedette_scheme_by_entity_rows!!', $vedette_scheme_by_entity_rows, $html);
		
		return $html;
	}
	
	/**
	 * Récupère les valeurs postée du formulaire d'association des schémas avec les entités PMB
	 */
	public function set_scheme_by_entity_from_form() {
		global $scheme_by_entity;
		
		self::$scheme_by_entity = $scheme_by_entity;
	}
	
	/**
	 * Sauvegarde l'association des schémas avec les entités PMB
	 */
	public function save_scheme_by_entity() {
		pmb_mysql_query('TRUNCATE TABLE vedette_schemes_by_entity');
		
		$values = array();
		foreach (self::$scheme_by_entity as $entity => $scheme) {
			$values[] = '('.$entity.', "'.$scheme.'")';
		}
		if (count($values)) {
			$query = 'INSERT INTO vedette_schemes_by_entity (entity_type, scheme) VALUES '.implode(',', $values);
			pmb_mysql_query($query);
		}
	}
	
	/**
	 * Retourne les schémas à utiliser par entité
	 * Si une entité est précisée, ne retourne que les schémas pour cette entité
	 * @param number $entity Constante d'entité de type TYPE_NOTICE, TYPE_AUTHOR, ...
	 * @return array
	 */
	public static function get_scheme_by_entity($entity = 0) {
		if (isset(self::$scheme_by_entity)) {
			if ($entity) {
				return self::$scheme_by_entity[$entity];
			}
			return self::$scheme_by_entity;
		}
		self::$scheme_by_entity = array();
		
		$query = 'SELECT entity_type, scheme FROM vedette_schemes_by_entity';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				self::$scheme_by_entity[$row['entity_type']] = $row['scheme'];
			}
		}
		if ($entity && isset(self::$scheme_by_entity[$entity])) {
			return self::$scheme_by_entity[$entity];
		}
		return self::$scheme_by_entity;
	}
}