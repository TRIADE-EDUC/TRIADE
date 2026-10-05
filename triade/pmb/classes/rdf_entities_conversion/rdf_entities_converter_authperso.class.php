<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_authperso.class.php,v 1.3 2022/06/02 14:04:35 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_authority.class.php');

class rdf_entities_converter_authperso extends rdf_entities_converter_authority {
	
	protected $table_name = 'authperso_authorities';
	
	protected $table_key = 'id_authperso_authority';
	
	protected $ppersos_prefix = 'authperso';
	
	private $authperso_num;
	
	protected function init_map_fields() {
	    $this->map_fields = array_merge(parent::init_map_fields(), array(
	        'id_authperso_authority' => 'http://www.pmbservices.fr/ontology#identifier',
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
		    
		));
		return $this->foreign_fields;
	}
	
	protected function init_linked_entities()
	{
	    $this->linked_entities = array_merge(parent::init_linked_entities(), array(
	        'http://www.pmbservices.fr/ontology#has_responsability_authperso' => array(
	            'type' => 'responsability',
	            'table' => 'responsability_authperso',
	            'reference_field_name' => 'responsability_authperso_num',
	            'external_field_name' => 'id_responsability_authperso',
	            'other_fields' => array(
	                'responsability_authperso_type' => '0'
	            ),
	            'abstract_entity' => '1',
	            'converter' => 'responsability_authperso'
	        )	        
	    ));
	    return $this->linked_entities;
	}
	
	protected function init_special_fields() {
		$this->special_fields = array_merge(parent::init_special_fields(), array());
		return $this->special_fields;
	}
	
	protected function post_create($uri) {
		// Audit
		if ($this->integration_type && $this->entity_id) {
		    $authperso_type = $this->authperso_num + 1000;
			$query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
			$query.= 'values ("'.$authperso_type.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.$this->create_audit_comment($uri).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
		}
		if ($this->entity_id) {			
			// Indexation
			$authperso = new authperso($this->authperso_num);
			$authperso->update_global_index($this->entity_id);
		}
	}
	
	public function set_authperso_num($authperso_num) {
	    $authperso_num = intval($authperso_num);
	    $this->authperso_num = $authperso_num;
	}
}