<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_subcollection.class.php,v 1.5 2018-06-26 14:48:14 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator_authority.class.php');
require_once($class_path.'/subcollection.class.php');

class rdf_entities_integrator_subcollection extends rdf_entities_integrator_authority {
	
	protected $table_name = 'sub_collections';
	
	protected $table_key = 'sub_coll_id';
	
	protected $ppersos_prefix = 'subcollection';
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'http://www.pmbservices.fr/ontology#website' => 'subcollection_web',
				'http://www.pmbservices.fr/ontology#label' => 'sub_coll_name',
				'http://www.pmbservices.fr/ontology#issn' => 'sub_coll_issn'
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
				'http://www.pmbservices.fr/ontology#has_collection' => 'sub_coll_parent'
		));
		return $this->foreign_fields;
	}
	
	protected function init_linked_entities() {
		$this->linked_entities = array_merge(parent::init_linked_entities(), array(
				'http://www.pmbservices.fr/ontology#has_concept' => array(
						'table' => 'index_concept',
						'reference_field_name' => 'num_object',
						'external_field_name' => 'num_concept',
						'other_fields' => array(
								'type_object' => TYPE_SUBCOLLECTION
						)
				)
		));
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
		$this->special_fields = array_merge(parent::init_special_fields(), array(
				'http://www.pmbservices.fr/ontology#has_publisher' => '',
		));
		return $this->special_fields;
	}
	
	protected function post_create($uri) {
		// Audit
		if ($this->integration_type && $this->entity_id) {
			$query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
			$query.= 'values ("'.AUDIT_SUB_COLLECTION.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.addslashes(json_encode(array("uri" => $uri))).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
			// Indexation
			subcollection::update_index($this->entity_id);
		}
	}
}