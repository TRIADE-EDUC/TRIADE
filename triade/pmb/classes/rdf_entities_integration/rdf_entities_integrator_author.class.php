<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_author.class.php,v 1.6 2020/07/13 15:00:09 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator_authority.class.php');
require_once($class_path.'/author.class.php');

class rdf_entities_integrator_author extends rdf_entities_integrator_authority {
	
	protected $table_name = 'authors';
	
	protected $table_key = 'author_id';
	
	protected $ppersos_prefix = 'author';
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'http://www.pmbservices.fr/ontology#date' => 'author_date',
				'http://www.pmbservices.fr/ontology#author_type' => 'author_type',
				'http://www.pmbservices.fr/ontology#author_name' => 'author_name',
				'http://www.pmbservices.fr/ontology#author_first_name' => 'author_rejete',
				'http://www.pmbservices.fr/ontology#place' => 'author_lieu',
				'http://www.pmbservices.fr/ontology#town' => 'author_ville',
				'http://www.pmbservices.fr/ontology#country' => 'author_pays',
				'http://www.pmbservices.fr/ontology#subdivision' => 'author_subdivision',
				'http://www.pmbservices.fr/ontology#number' => 'author_numero',
				'http://www.pmbservices.fr/ontology#website' => 'author_web'
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
				'http://www.pmbservices.fr/ontology#author_see' => 'author_see'
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
								'type_object' => TYPE_AUTHOR
						)
				)
		));
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
		$this->special_fields = array_merge(parent::init_special_fields(), array(
		));
		return $this->special_fields;
	}
	
	protected function post_create($uri) {
		// Audit
		if ($this->integration_type && $this->entity_id) {
			$query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
			$query.= 'values ("'.AUDIT_AUTHOR.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.$this->create_audit_comment($uri).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
			// Indexation
			auteur::update_index($this->entity_id);
		}
	}
}