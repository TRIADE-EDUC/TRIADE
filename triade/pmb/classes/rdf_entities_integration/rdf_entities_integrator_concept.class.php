<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_concept.class.php,v 1.2 2017-03-13 09:30:34 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator_authority.class.php');

class rdf_entities_integrator_concept extends rdf_entities_integrator_authority {
	
	
	// TODO Peut être tout dériver pour les concepts pour reprendre la mécanique du framework initial.
	// Tout dépend de ce qui est fait à l'OPAC pour la génération du formulaire
	
	
	
	
// 	protected $table_name = 'indexint';
	
// 	protected $table_key = 'indexint_id';
	
// 	protected function init_map_fields() {
// 		$this->map_fields = array_merge(parent::init_map_fields(), array(
// 				'http://www.pmbservices.fr/ontology#label' => 'indexint_name',
// 				'http://www.pmbservices.fr/ontology#comment' => 'indexint_comment'
// 		));
// 		return $this->map_fields;
// 	}
	
// 	protected function init_foreign_fields() {
// 		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
// 		));
// 		return $this->foreign_fields;
// 	}
	
// 	protected function init_linked_entities() {
// 		$this->linked_entities = array_merge(parent::init_linked_entities(), array(
// 				'http://www.pmbservices.fr/ontology#has_concept' => array(
// 						'table' => 'index_concept',
// 						'reference_field_name' => 'num_object',
// 						'external_field_name' => 'num_concept',
// 						'other_fields' => array(
// 								'type_object' => TYPE_INDEXINT
// 						)
// 				)
// 		));
// 		return $this->linked_entities;
// 	}
	
// 	protected function init_special_fields() {
// 		$this->special_fields = array_merge(parent::init_special_fields(), array(
// 		));
// 		return $this->special_fields;
// 	}
	
// 	protected function post_create($uri) {
// 		// Audit
// 		if ($this->integration_type && $this->entity_id) {
// 			$query = 'insert into audit (type_object, object_id, user_id, type_modif, info, type_user) ';
// 			$query.= 'values ("'.AUDIT_INDEXINT.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.json_encode(array()).'", "'.$this->contributor_type.'")';
// 			pmb_mysql_query($query);
	
// 			// Indexation
// 			indexint::update_index($this->entity_id);
// 		}
// 	}
}