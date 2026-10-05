<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_work.class.php,v 1.18 2022/03/22 08:43:16 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator_authority.class.php');
require_once($class_path.'/titre_uniforme.class.php');
require_once($class_path.'/marc_table.class.php');

class rdf_entities_integrator_work extends rdf_entities_integrator_authority {
	
	protected $table_name = 'titres_uniformes';
	
	protected $table_key = 'tu_id';
	
	protected $ppersos_prefix = 'tu';
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'http://www.pmbservices.fr/ontology#date' => 'tu_date',
				'http://www.pmbservices.fr/ontology#label' => 'tu_name',
				'http://www.pmbservices.fr/ontology#work_type' => 'tu_oeuvre_type',
				'http://www.pmbservices.fr/ontology#work_nature' => 'tu_oeuvre_nature',
				'http://www.pmbservices.fr/ontology#shape' => 'tu_forme',
				'http://www.pmbservices.fr/ontology#has_shape' => 'tu_forme_marclist',
				'http://www.pmbservices.fr/ontology#place' => 'tu_lieu',
				'http://www.pmbservices.fr/ontology#subject' => 'tu_sujet',
				'http://www.pmbservices.fr/ontology#intended_termination' => 'tu_completude',
				'http://www.pmbservices.fr/ontology#targeted_audience' => 'tu_public',
				'http://www.pmbservices.fr/ontology#story' => 'tu_histoire',
				'http://www.pmbservices.fr/ontology#context' => 'tu_contexte',
				'http://www.pmbservices.fr/ontology#tone' => 'tu_tonalite',
				'http://www.pmbservices.fr/ontology#has_tone' => 'tu_tonalite_marclist',
				'http://www.pmbservices.fr/ontology#coord' => 'tu_coordonnees',
				'http://www.pmbservices.fr/ontology#equinox' => 'tu_equinoxe',
				'http://www.pmbservices.fr/ontology#other_feature' => 'tu_caracteristique'
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
								'type_object' => TYPE_TITRE_UNIFORME
						)
				),
				'http://www.pmbservices.fr/ontology#expression_of' => array(
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_from',
						'external_field_name' => 'oeuvre_link_to',
						'other_fields' => array(
								'oeuvre_link_expression' => '0',
								'oeuvre_link_other_link' => '0'
						)
				),
				'http://www.pmbservices.fr/ontology#has_expression' => array(
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_to',
						'external_field_name' => 'oeuvre_link_from',
						'other_fields' => array(
								'oeuvre_link_expression' => '1',
								'oeuvre_link_other_link' => '0'
						)
				),
				'http://www.pmbservices.fr/ontology#has_other_link' => array(
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_from',
						'external_field_name' => 'oeuvre_link_to',
						'other_fields' => array(
								'oeuvre_link_expression' => '0',
								'oeuvre_link_other_link' => '1'
						)
				),
				'http://www.pmbservices.fr/ontology#has_event' => array(
						'table' => 'tu_oeuvres_events',
						'reference_field_name' => 'oeuvre_event_tu_num',
						'external_field_name' => 'oeuvre_event_authperso_authority_num'
				),
				'http://www.pmbservices.fr/ontology#music_distribution' => array(
						'table' => 'tu_distrib',
						'reference_field_name' => 'distrib_num_tu',
						'external_field_name' => 'distrib_name'
				),
				'http://www.pmbservices.fr/ontology#subdivision_shape' => array(
						'table' => 'tu_subdiv',
						'reference_field_name' => 'subdiv_num_tu',
						'external_field_name' => 'subdiv_name'
				),
				'http://www.pmbservices.fr/ontology#has_record' => array(
						'table' => 'notices_titres_uniformes',
						'reference_field_name' => 'ntu_num_tu',
						'external_field_name' => 'ntu_num_notice'
				),
		));
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
		$this->special_fields = array_merge(parent::init_special_fields(), array(
            'http://www.pmbservices.fr/ontology#has_responsability_author' => array(
                "method" => array($this,"insert_responsability_tu"),
                "arguments" => array(0)
            ),
		    'http://www.pmbservices.fr/ontology#has_responsability_performer' => array(
		        "method" => array($this,"insert_responsability_tu"),
		        "arguments" => array(1)
		    ),
		    'http://www.pmbservices.fr/ontology#expression_of' => array(
		        "method" => array($this,"insert_linked_work"),
		        "arguments" => array()
		    ),
		    'http://www.pmbservices.fr/ontology#has_expression' => array(
		        "method" => array($this,"insert_linked_work"),
		        "arguments" => array()
		    ),
		    'http://www.pmbservices.fr/ontology#has_other_link' => array(
		        "method" => array($this,"insert_linked_work"),
		        "arguments" => array()
		    ),
		    'http://www.pmbservices.fr/ontology#thumbnail_url' => array(
		        "method" => array($this,"insert_thumbnail_url"),
		        "arguments" => array(AUT_TABLE_TITRES_UNIFORMES)
		    ),
		));
		return $this->special_fields;
	}
	
	protected function init_cataloging_entities() {
		$this->cataloging_entities = array_merge(parent::init_cataloging_entities(), array(
				'http://www.pmbservices.fr/ontology#has_responsability_author' => array(
						'table' => 'responsability_tu',
						'reference_field_name' => 'responsability_tu_num',
						'external_field_name' => 'responsability_tu_author_num',
						'other_fields' => array(
								'responsability_tu_type' => 0,
								'responsability_tu_fonction' => '070'
						)
				),
				'http://www.pmbservices.fr/ontology#has_responsability_performer' => array(
						'table' => 'responsability_tu',
						'reference_field_name' => 'responsability_tu_num',
						'external_field_name' => 'responsability_tu_author_num',
						'other_fields' => array(
								'responsability_tu_type' => 1,
								'responsability_tu_fonction' => '070'
						)
				),
				'http://www.pmbservices.fr/ontology#has_event' => array(
						'table' => 'tu_oeuvres_events',
						'reference_field_name' => 'oeuvre_event_tu_num',
						'external_field_name' => 'oeuvre_event_authperso_authority_num',
						'other_fields' => array()						
				),
				'http://www.pmbservices.fr/ontology#has_expression' => array(
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_to',
						'external_field_name' => 'oeuvre_link_from',
						'other_fields' => array(
								'oeuvre_link_expression' => '1',
								'oeuvre_link_other_link' => '0'
						),
						'callable' => array(
									'method'=> array($this, 'cataloging_insert_reversed_link_work'),
									'arguments'=> array(1,0),
						),
				),
				'http://www.pmbservices.fr/ontology#expression_of' => array(
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_from',
						'external_field_name' => 'oeuvre_link_to',
						'other_fields' => array(
								'oeuvre_link_expression' => '0',
								'oeuvre_link_other_link' => '0'
						),
						'callable' => array(
									'method'=> array($this, 'cataloging_insert_reversed_link_work'),
									'arguments'=> array(0,0),
						),
				),
				'http://www.pmbservices.fr/ontology#has_other_link' => array(
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_from',
						'external_field_name' => 'oeuvre_link_to',
						'other_fields' => array(
								'oeuvre_link_expression' => '0',
								'oeuvre_link_other_link' => '1'
						),
						'callable' => array(
									'method'=> array($this, 'cataloging_insert_reversed_link_work'),
									'arguments'=> array(0,1),
						),
				),
		));
	}
	
	protected function post_create($uri) {
		// Audit
		if ($this->integration_type && $this->entity_id) {
			$query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
			$query.= 'values ("'.AUDIT_TITRE_UNIFORME.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.$this->create_audit_comment($uri).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
			// Indexation
			titre_uniforme::update_index($this->entity_id);
		}
	}
	
	public function cataloging_insert_reversed_link_work($expression = 0, $other_link = 0, $to = 0, $from = 0, $type = '') {
	    
	    $to = intval($to);
	    $from = intval($from);
		
	    $oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
		if(!isset($oeuvre_link->inverse_of[$type])){
			return;
		}
		
		$query_select = "SELECT oeuvre_link_type FROM tu_oeuvres_links WHERE oeuvre_link_from = '".$to."'";
		$query_select .= " AND oeuvre_link_to = '".$from."' AND oeuvre_link_type = '".$oeuvre_link->inverse_of[$type]."'";
		
		$result = pmb_mysql_query($query_select);
		if (pmb_mysql_num_rows($result) > 0) {
			return;
		}
		
		$query_max = 'SELECT MAX(oeuvre_link_order) FROM tu_oeuvres_links WHERE oeuvre_link_from = "'.$to.'"';
		$result = pmb_mysql_query($query_max);
		$max_order = intval(pmb_mysql_result($result, 0, 0));
		
		$query_insert = "INSERT INTO tu_oeuvres_links (oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link, oeuvre_link_order)";
		$query_insert .= " VALUES ('".$to."', '".$from."', '".$oeuvre_link->inverse_of[$type]."', '".$expression."', '".$other_link."', '".($max_order+1)."')";
		pmb_mysql_query($query_insert);
		
		return true;
	}
	
	public function insert_responsability_tu($responsability_tu_type, $values) {
	    
	    $query = "	DELETE FROM responsability_tu
					WHERE responsability_tu_num = '".$this->entity_id."'
					AND responsability_tu_type = '".$responsability_tu_type."'";
	    pmb_mysql_query($query);
	    
	    $query_values = "";
	    foreach ($values as $value) {
	        $responsability_function = $this->store->get_property($value["value"], "pmb:author_function");
	        $author_uri = $this->store->get_property($value["value"], "pmb:has_author");
	        $author = $this->integrate_entity($author_uri[0]['value'], true);
	        $this->entity_data['children'][] = $author;
	        if ($query_values) {
	            $query_values .= ',';
	        }
	        
    	    $query = "	INSERT INTO responsability_tu (responsability_tu_author_num, responsability_tu_num, responsability_tu_type, responsability_tu_fonction)
    					VALUES ('".$author["id"]."', '".$this->entity_id."', $responsability_tu_type, '".$responsability_function[0]['value']."')";
    	    pmb_mysql_query($query);
    	    
    	    $json_vedette = $this->store->get_property($value["value"], "pmb:author_qualification")[0];
    	    $vedette_value = json_decode($json_vedette['value']);
    	    
    	    $this->insert_vedette($vedette_value, pmb_mysql_insert_id());
	    }
	}
	
	public function insert_linked_work($values) {
	    foreach ($values as $value) {
	        
	        $relation_type_work = $this->store->get_property($value["value"], "pmb:relation_type_work");
	        $work_uri = $this->store->get_property($value["value"], "pmb:has_work");
	        $work = $this->integrate_entity($work_uri[0]['value'], true);
	        if(empty($work['id'])){
	            continue;
	        }
	        $this->entity_data['children'][] = $work;
	        $oeuvre_link_type = $relation_type_work[0]['value'];
	        
	        $query_select = "SELECT oeuvre_link_type FROM tu_oeuvres_links WHERE ";
	        $query_select .= " oeuvre_link_from = " . $this->entity_id;
	        $query_select .= " AND oeuvre_link_to = " . $work['id'];
	        $query_select .= " AND oeuvre_link_type = $oeuvre_link_type";
	        
	        $result = pmb_mysql_query($query_select);
	        if (pmb_mysql_num_rows($result) > 0) {
	            continue;
	        }
	        
	        $oeuvre_link = marc_list_collection::get_instance('oeuvre_link');
            $oeuvre_link_other_link = 0;
	        $oeuvre_link_expression = 0;
	        
	        $query_max_order = "SELECT MAX(oeuvre_link_order) FROM tu_oeuvres_links WHERE oeuvre_link_from = " . $this->entity_id;
	        $result = pmb_mysql_query($query_max_order);
	        $max_order = pmb_mysql_result($result, 0, 0);
	        
	        $oeuvre_link_order = ( intval($max_order) + 1);
	        	        
	        
	        if (!empty($oeuvre_link->attributes[$oeuvre_link_type])) {
               
    	        $oeuvre_link_attributes = $oeuvre_link->attributes[$oeuvre_link_type];
    	        switch($oeuvre_link_attributes['GROUP']){
    	            case "other_link":
    	                $oeuvre_link_other_link = 1;
    	                $oeuvre_link_expression = 0;
    	                $reverse_oeuvre_link_expression = 0;
    	                break;
                    case "expression_of":
                        $oeuvre_link_other_link = 0;
                        $oeuvre_link_expression = 0;
                        $reverse_oeuvre_link_expression = 1;
                        break;
                    case "have_expression":
                        $oeuvre_link_other_link = 0;
                        $oeuvre_link_expression = 1;
                        $reverse_oeuvre_link_expression = 0;
                        break;
    	        }
    	        $query_insert = "INSERT INTO tu_oeuvres_links (oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link, oeuvre_link_order)";
    	        $query_insert .= " VALUES ('".$this->entity_id."', '".$work['id']."', '".$oeuvre_link_type."', '".$reverse_oeuvre_link_expression."', '".$oeuvre_link_other_link."', '".$oeuvre_link_order."')";
    	        
    	        pmb_mysql_query($query_insert);
    	        
    	        $this->cataloging_insert_reversed_link_work($oeuvre_link_expression, $oeuvre_link_other_link, $work['id'], $this->entity_id, $oeuvre_link_type);
	        }
	    }
	}
}