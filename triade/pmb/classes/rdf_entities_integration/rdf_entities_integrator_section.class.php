<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_section.class.php,v 1.2 2020/07/13 15:00:07 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/author.class.php');

class rdf_entities_integrator_section extends rdf_entities_integrator {
	
	protected $table_name = 'cms_sections';
	
	protected $table_key = 'id_section';
	
	protected $ppersos_prefix = 'cms_editorial';
	
	protected $cms_type;
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'http://www.pmbservices.fr/ontology#title' => 'section_title',
				'http://www.pmbservices.fr/ontology#summary' => 'section_resume',
				'http://www.pmbservices.fr/ontology#logo' => 'section_logo',
				'http://www.pmbservices.fr/ontology#publication_state' => 'section_publication_state',
				'http://www.pmbservices.fr/ontology#start_date' => 'section_start_date',
				'http://www.pmbservices.fr/ontology#end_date' => 'section_end_date',
				'http://www.pmbservices.fr/ontology#creation_date' => 'section_creation_date',
				'http://www.pmbservices.fr/ontology#update_date' => 'section_update_timestamp',
				'http://www.pmbservices.fr/ontology#has_cms_section' => 'section_num_parent',
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
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
								'type_object' => TYPE_CMS_SECTION
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
			$query.= 'values ("'.AUDIT_EDITORIAL_SECTION.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.$this->create_audit_comment($uri).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
		}
		if ($this->entity_id) {			
			// Indexation
			auteur::update_index($this->entity_id);
		}
	}
	
	public function set_cms_type($cms_type){
		// On définit les valeurs par défaut
		$this->cms_type = $cms_type;
	}
	
	protected function init_base_query_elements() {
		// On définit les valeurs par défaut
		$this->base_query_elements = parent::init_base_query_elements();
		$this->base_query_elements = array_merge($this->base_query_elements, array(
				'section_num_type' => $this->cms_type,
		));
	}
}