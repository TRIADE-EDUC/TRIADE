<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_bulletin.class.php,v 1.3 2024/03/18 14:10:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class rdf_entities_converter_bulletin extends rdf_entities_converter {
	
	protected $table_name = 'bulletins';
	
	protected $table_key = 'bulletin_id';
	
	protected $ppersos_prefix = '';
	
	protected function init_map_fields() {
	    $this->map_fields = array_merge(parent::init_map_fields(), array(
	        'bulletin_titre' => 'http://www.pmbservices.fr/ontology#tit1',
	        'date_date' => 'http://www.pmbservices.fr/ontology#publication_date',
	        'mention_date' => 'http://www.pmbservices.fr/ontology#has_date',
	        'bulletin_numero' => 'http://www.pmbservices.fr/ontology#number',
	        'num_notice' => 'http://www.pmbservices.fr/ontology#num_notice',
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
			'bulletin_notice' => array(
			    'type' => 'record',
                'property' => 'http://www.pmbservices.fr/ontology#has_serial'
			),
		));
		return $this->foreign_fields;
	}
	
	protected function init_linked_entities() {
	    $this->linked_entities = array_merge(parent::init_linked_entities(), array(
    	        'http://www.pmbservices.fr/ontology#has_docnum' => array(
    	            'type' => 'docnum',
    	            'table' => 'explnum',
    	            'reference_field_name' => 'explnum_bulletin',
    	            'external_field_name' => 'explnum_id',
    	        ),
		));
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
		$this->special_fields = array_merge(parent::init_special_fields(), array());
		return $this->special_fields;
	}				
	
	protected function init_base_query_elements() {
		// On définit les valeurs par défaut
		$this->base_query_elements = parent::init_base_query_elements();
		if (!$this->entity_id) {
			$this->base_query_elements = array_merge($this->base_query_elements, array(
					'create_date' => date('Y-m-d H:i:s')
			));
		}
	}
	
	protected function post_create() {
	    
	}
	
	public function insert_concept($values) {
		$index_concept = new index_concept($this->entity_id, TYPE_NOTICE);
		if (is_array($values)) {
			foreach($values as $value) {
				$concept = $this->integrate_entity($value["value"]);
				$this->entity_data['children'][] = $concept;
				$index_concept->add_concept(new concept($concept['id']));
			}
		}
		$index_concept->save(false);
	}
	
	public function get_linked_record($direction, $num_reverse_link) {
	    $linked_records = array();
		$query = "	SELECT id_notices_relations FROM notices_relations
					WHERE num_notice = '".$this->entity_id."'
					AND direction = '".$direction."'
					AND num_reverse_link = '".$num_reverse_link."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
		    while ($row = pmb_mysql_fetch_assoc($result)) {
		        $linked_records[] = $row['id_notices_relations'];
		    }
		}
		return $linked_records;
	}
	
	public function insert_parution_date($values) {
		$date_parution_notice = notice::get_date_parution($values[0]['value']);
		$query = 'update '.$this->table_name.' set date_parution = "'.$date_parution_notice.'" where '.$this->table_key.' = "'.$this->entity_id.'"';
		pmb_mysql_query($query);
	}
	
	public function get_assertions() {
	    parent::get_assertions();
	    if (!empty($this->assertions)) {
	        $record_assertions = [];
	        $properties_list = [];
	        foreach ($this->assertions as $assertion) {
	            $properties_list[] = $assertion->get_predicate();
	            if ($assertion->get_predicate() == 'http://www.pmbservices.fr/ontology#num_notice' && !empty($assertion->get_object())) {
	                $rdf_converter = new rdf_entities_converter_record(intval($assertion->get_object()), "record");
	                $record_assertions = $rdf_converter->get_assertions();
	            }
	        }
	        foreach ($record_assertions as $record_assertion) {
	            if (!in_array($record_assertion->get_predicate(), $properties_list)) {
	                $this->assertions[] = $record_assertion;
	                
	            }
	        }
	    }
	    return $this->assertions;
	}
}