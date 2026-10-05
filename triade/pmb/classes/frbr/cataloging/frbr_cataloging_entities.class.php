<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_entities.class.php,v 1.3 2018/01/22 09:16:28 tsamson Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/onto/onto_pmb_entities_store.class.php');
require_once($class_path.'/frbr/cataloging/frbr_cataloging_entity.class.php');
require_once($class_path.'/encoding_normalize.class.php');

class frbr_cataloging_entities {
	
	protected static $entities;
	
	protected static $entities_labels;
	
	static public function get_entities() {
		if (!empty(static::$entities)) {
			return static::$entities;
		}
		static::$entities = array();
		$query = 'select * where {
				?entity rdfs:subClassOf pmb:entity .
				?entity pmb:name ?name .
		}';
		onto_pmb_entities_store::query($query);
		if (onto_pmb_entities_store::num_rows()) {
			$results = onto_pmb_entities_store::get_result();
			foreach ($results as $result) {
				static::$entities[$result->name] = new frbr_cataloging_entity($result->entity, $result->name); 
			}
		}
		return $results;
	}
	
	static public function get_json_entities() {
		static::get_entities();
		$return = array();
		foreach (static::$entities as $entity) {
			$return[] = array(
					'name' => $entity->get_name(),
					'label' => $entity->get_label()
			);
		}
		return encoding_normalize::json_encode($return);
	}
	
	static public function get_label($entity_uri) {
		global $lang;
		
		if (!isset(static::$entities_labels)) {			
			$query = '
				select * where {
					?entity rdfs:label ?label .
					?entity rdfs:subClassOf pmb:entity
				}
			';
			onto_pmb_entities_store::query($query);
			if (onto_pmb_entities_store::num_rows()) {
				$results = onto_pmb_entities_store::get_result();
				foreach ($results as $result) {
					if (!isset(static::$entities_labels[$result->entity])) {
						static::$entities_labels[$result->entity] = array();
					}
					static::$entities_labels[$result->entity][$result->label_lang] = $result->label;
				}
			}
		}

		$sub_lang = substr($lang, 0, 2);
		
		if (isset(static::$entities_labels[$entity_uri][$sub_lang])) {
			return static::$entities_labels[$entity_uri][$sub_lang];
		}
		if (isset(static::$entities_labels[$entity_uri][''])) {
			return static::$entities_labels[$entity_uri][''];
		}
		if (isset(static::$entities_labels[$entity_uri]['fr'])) {
			return static::$entities_labels[$entity_uri]['fr'];
		}
	}
}