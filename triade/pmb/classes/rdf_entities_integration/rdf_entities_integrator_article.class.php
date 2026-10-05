<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_article.class.php,v 1.2 2020/07/13 15:00:07 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/author.class.php');

class rdf_entities_integrator_article extends rdf_entities_integrator {
	
	protected $table_name = 'cms_articles';
	
	protected $table_key = 'id_article';
	
	protected $ppersos_prefix = 'cms_editorial';
	
	protected $cms_type;
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'http://www.pmbservices.fr/ontology#title' => 'article_title',
				'http://www.pmbservices.fr/ontology#summary' => 'article_resume',
				'http://www.pmbservices.fr/ontology#content' => 'article_contenu',
				'http://www.pmbservices.fr/ontology#logo' => 'article_logo',
				'http://www.pmbservices.fr/ontology#publication_state' => 'article_publication_state',
				'http://www.pmbservices.fr/ontology#start_date' => 'article_start_date',
				'http://www.pmbservices.fr/ontology#end_date' => 'article_end_date',
				'http://www.pmbservices.fr/ontology#creation_date' => 'article_creation_date',
				'http://www.pmbservices.fr/ontology#update_date' => 'article_update_timestamp',
				'http://www.pmbservices.fr/ontology#cms_article_type' => 'article_num_type',
				'http://www.pmbservices.fr/ontology#has_cms_section' => 'num_section',
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
								'type_object' => TYPE_CMS_ARTICLE
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
			$query.= 'values ("'.AUDIT_EDITORIAL_ARTICLE.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.$this->create_audit_comment($uri).'", "'.$this->contributor_type.'")';
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
				'article_num_type' => $this->cms_type,
		));
	}
}