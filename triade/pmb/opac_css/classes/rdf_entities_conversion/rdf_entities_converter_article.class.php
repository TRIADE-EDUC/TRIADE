<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_article.class.php,v 1.2 2020/11/27 16:01:15 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter.class.php');

class rdf_entities_converter_article extends rdf_entities_converter {
	
	protected $table_name = 'cms_articles';
	
	protected $table_key = 'id_article';
	
	protected $ppersos_prefix = 'cms_editorial';
	
	protected $type_constant = TYPE_CMS_ARTICLE;
	
	protected $aut_table_constant = AUDIT_EDITORIAL_ARTICLE;
	
	protected function init_map_fields() {
	    $this->map_fields = array_merge(parent::init_map_fields(), array(
	        'article_title' => 'http://www.pmbservices.fr/ontology#title',
	        'article_resume' => 'http://www.pmbservices.fr/ontology#summary',
	        'article_contenu' => 'http://www.pmbservices.fr/ontology#content',
	        'article_logo' => 'http://www.pmbservices.fr/ontology#logo',
	        'article_publication_state' => 'http://www.pmbservices.fr/ontology#publication_state',
	        'article_start_date' => 'http://www.pmbservices.fr/ontology#start_date',
	        'article_end_date' => 'http://www.pmbservices.fr/ontology#end_date',
	        'article_creation_date' => 'http://www.pmbservices.fr/ontology#creation_date',
	        'article_update_timestamp' => 'http://www.pmbservices.fr/ontology#update_date',
	        'article_num_type' => 'http://www.pmbservices.fr/ontology#cms_article_type',
	        'num_section' => 'http://www.pmbservices.fr/ontology#has_cms_section',
	    ));
	    return $this->map_fields;
	}
	
	protected function init_linked_entities() {
	    $this->linked_entities = array_merge(parent::init_linked_entities(), array(
	        'http://www.pmbservices.fr/ontology#has_concept' => array(
	            'type' => 'concept',
	            'table' => 'index_concept',
	            'reference_field_name' => 'num_object',
	            'external_field_name' => 'num_concept',
	            'other_fields' => array(
	                'type_object' => $this->type_constant
	            )
	        ),
	    ));
	    return $this->linked_entities;
	}
	
	
	
	protected function init_foreign_fields() {
	    $this->foreign_fields = array_merge(parent::init_foreign_fields(), array());
	    return $this->foreign_fields;
	}
	
	
	protected function init_special_fields() {
	    $this->special_fields = array_merge(parent::init_special_fields(), array());
	    return $this->special_fields;
	}
	
}