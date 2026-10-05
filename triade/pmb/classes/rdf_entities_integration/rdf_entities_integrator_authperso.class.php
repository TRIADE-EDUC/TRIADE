<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_authperso.class.php,v 1.9 2021/07/29 14:26:46 qvarin Exp $


if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class rdf_entities_integrator_authperso extends rdf_entities_integrator_authority {
	
	protected $table_name = 'authperso_authorities';
	
	protected $table_key = 'id_authperso_authority';
	
	protected $ppersos_prefix = 'authperso';
	
	protected $authperso_num;
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
		    
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
	                'type_object' => TYPE_AUTHPERSO
	            )
	        )
		));
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
		$this->special_fields = array_merge(parent::init_special_fields(), array(
		    'http://www.pmbservices.fr/ontology#has_responsability_authperso' => array(
		        "method" => array($this,"insert_responsability"),
		        "arguments" => array()
		    ),
		));
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
	
	protected function execute_base_query() {
	    $this->integration_type = 1;
	    $query = 'insert into '.$this->table_name.' set ';
	    $query_clause = '';
	    if ($this->entity_id) {
	        $this->integration_type = 2;
	        $query = 'update '.$this->table_name.' set ';
	        $query_clause = ' where '.$this->table_key.' = '.$this->entity_id;
	    }
	    if ($this->authperso_num) {
	        $query.= "authperso_authority_authperso_num = $this->authperso_num";
	    }
	    pmb_mysql_query($query.$query_clause);
	    if (!$this->entity_id) {
	        $this->entity_id = pmb_mysql_insert_id();
	    }
	    return $this->entity_id;
	}
	
	public function insert_responsability($values) {
	    $query = "DELETE FROM responsability_authperso WHERE responsability_authperso_num = '".($this->entity_id)."'";
	    pmb_mysql_query($query);
	    
	    foreach($values as $value) {
	        $responsability_function = $this->store->get_property($value["value"], "pmb:author_function");
	        $author_uri = $this->store->get_property($value["value"], "pmb:has_author");
	        $author = $this->integrate_entity($author_uri[0]['value'], true);
	        $this->entity_data['children'][] = $author;
	        
	        // On fixe la fonction à auteur en attendant de trouver une solution pour les responsabilités
    	    $query = "	INSERT INTO responsability_authperso (responsability_authperso_author, responsability_authperso_num, responsability_authperso_fonction)
    					VALUES ('".$author["id"]."', '".$this->entity_id."', '".$responsability_function[0]['value']."')";
    	    pmb_mysql_query($query);
    	    
    	    $json_vedette = $this->store->get_property($value["value"], "pmb:author_qualification")[0];
    	    $vedette_value = json_decode($json_vedette['value']);
    	    
    	    $this->insert_vedette($vedette_value, pmb_mysql_insert_id());
	    }
	}
}