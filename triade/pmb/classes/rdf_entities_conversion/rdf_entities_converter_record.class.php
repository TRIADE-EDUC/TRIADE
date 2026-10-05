<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_record.class.php,v 1.4 2024/03/14 14:43:36 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter.class.php');
require_once($class_path.'/notice.class.php');
require_once($class_path.'/acces.class.php');

class rdf_entities_converter_record extends rdf_entities_converter {
	
	protected $table_name = 'notices';
	
	protected $table_key = 'notice_id';
	
	protected $ppersos_prefix = 'notices';
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
		    "notice_id" => "http://www.pmbservices.fr/ontology#identifier",
		    "niveau_biblio" => "http://www.pmbservices.fr/ontology#bibliographical_lvl",
		    "niveau_hierar" => "http://www.pmbservices.fr/ontology#hierarchical_lvl",
		    "typdoc" => "http://www.pmbservices.fr/ontology#doctype",
		    "tit1" => "http://www.pmbservices.fr/ontology#tit1",
		    "tit2" => "http://www.pmbservices.fr/ontology#tit2",
		    "tit3" => "http://www.pmbservices.fr/ontology#tit3",
		    "tit4" => "http://www.pmbservices.fr/ontology#tit4",
		    "tnvol" => "http://www.pmbservices.fr/ontology#tnvol",
		    "nocoll" => "http://www.pmbservices.fr/ontology#nocoll",
		    "year" => "http://www.pmbservices.fr/ontology#has_date",
		    "mention_edition" => "http://www.pmbservices.fr/ontology#publishing_notice",
		    "code" => "http://www.pmbservices.fr/ontology#isbn",
		    "npages" => "http://www.pmbservices.fr/ontology#nb_pages",
		    "ill" => "http://www.pmbservices.fr/ontology#illustration",
		    "size" => "http://www.pmbservices.fr/ontology#size",
		    "prix" => "http://www.pmbservices.fr/ontology#price",
		    "accomp" => "http://www.pmbservices.fr/ontology#accompanying_material",
		    "n_gen" => "http://www.pmbservices.fr/ontology#general_note",
		    "n_contenu" => "http://www.pmbservices.fr/ontology#content_note",
		    "n_resume" => "http://www.pmbservices.fr/ontology#resume_note",
		    "index_l" => "http://www.pmbservices.fr/ontology#keywords",
		    "lien" => "http://www.pmbservices.fr/ontology#url",
		    "eformat" => "http://www.pmbservices.fr/ontology#eformat",
		    "indexation_lang" => "http://www.pmbservices.fr/ontology#record_language",
		    "notice_is_new" => "http://www.pmbservices.fr/ontology#new_record",
		    "commentaire_gestion" => "http://www.pmbservices.fr/ontology#comment",
		    "thumbnail_url" => "http://www.pmbservices.fr/ontology#thumbnail_url",
		    "statut" => "http://www.pmbservices.fr/ontology#has_record_status"
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
			'tparent_id' => array(
			    'type' => 'record',
                'property' => 'http://www.pmbservices.fr/ontology#tparent'
			),
		    'ed1_id' => array(
		        'type' => 'publisher',
		        'property' => 'http://www.pmbservices.fr/ontology#has_publisher',
            ),
	        'ed2_id' => array(
	            'type' => 'publisher',
	            'property' => 'http://www.pmbservices.fr/ontology#has_other_publisher',
	        ),
            'coll_id' => array(
                'type' => 'collection',
                'property' => 'http://www.pmbservices.fr/ontology#has_collection',
            ),
            'subcoll_id' => array(
                'type' => 'subcollection',
                'property' => 'http://www.pmbservices.fr/ontology#has_subcollection',
            ),    
            'indexint' => array(
                'type' => 'indexint',
                'property' => 'http://www.pmbservices.fr/ontology#has_indexint'
            ),
		));
		return $this->foreign_fields;
	}
	
	protected function init_linked_entities() {
	    $this->linked_entities = array_merge(parent::init_linked_entities(), array(
    	        'http://www.pmbservices.fr/ontology#has_concept' => array(
        	            'type' => 'concept',
        	            'table' => 'index_concept',
        	            'reference_field_name' => 'num_object',
        	            'external_field_name' => 'num_concept',
        	            'other_fields' => array(
        	                'type_object' => TYPE_NOTICE
        	            )
    	        ),
				'http://www.pmbservices.fr/ontology#has_category' => array(
				        'type' => 'category',
						'table' => 'notices_categories',
						'reference_field_name' => 'notcateg_notice',
						'external_field_name' => 'num_noeud'
				),
				'http://www.pmbservices.fr/ontology#has_work' => array(
				        'type' => 'work',
						'table' => 'notices_titres_uniformes',
						'reference_field_name' => 'ntu_num_notice',
						'external_field_name' => 'ntu_num_tu'
				),
				'http://www.pmbservices.fr/ontology#publication_language' => array(
				        'type' => 'language',
						'table' => 'notices_langues',
						'reference_field_name' => 'num_notice',
						'external_field_name' => 'code_langue',
						'other_fields' => array(
								'type_langue' => '0'
						)
				),
				'http://www.pmbservices.fr/ontology#original_language' => array(
				        'type' => 'language',
						'table' => 'notices_langues',
						'reference_field_name' => 'num_notice',
						'external_field_name' => 'code_langue',
						'other_fields' => array(
								'type_langue' => '1'
						)
				),
				'http://www.pmbservices.fr/ontology#has_main_author' => array(
				        'type' => 'responsability',
						'table' => 'responsability',
						'reference_field_name' => 'responsability_notice',
						'external_field_name' => 'id_responsability',
						'other_fields' => array(
								'responsability_type' => '0'
						),
				        'abstract_entity' => '1'
				),
				'http://www.pmbservices.fr/ontology#has_other_author' => array(
				        'type' => 'responsability',
						'table' => 'responsability',
						'reference_field_name' => 'responsability_notice',
						'external_field_name' => 'id_responsability',
						'other_fields' => array(
								'responsability_type' => '1'
						),
				        'abstract_entity' => '1'
				),
				'http://www.pmbservices.fr/ontology#has_secondary_author' => array(
				        'type' => 'responsability',
						'table' => 'responsability',
						'reference_field_name' => 'responsability_notice',
						'external_field_name' => 'id_responsability',
						'other_fields' => array(
								'responsability_type' => '2'
						),
				        'abstract_entity' => '1'
				),
				'http://www.pmbservices.fr/ontology#has_linked_record' => array(
				        'type' => 'linked_record',
						'table' => 'notices_relations',
						'reference_field_name' => 'num_notice',
						'external_field_name' => 'id_notices_relations',
				        'abstract_entity' => '1'
				),
				'http://www.pmbservices.fr/ontology#has_docnum' => array(
				        'type' => 'docnum',
						'table' => 'explnum',
						'reference_field_name' => 'explnum_notice',
						'external_field_name' => 'explnum_id',
				),
				'http://www.pmbservices.fr/ontology#has_expl' => array(
				        'type' => 'expl',
						'table' => 'exemplaires',
						'reference_field_name' => 'expl_notice',
						'external_field_name' => 'expl_id',
				),
                'http://www.pmbservices.fr/ontology#has_bulletin' => array(
                        'type' => 'bulletin',
                        'table' => 'analysis',
                        'reference_field_name' => 'analysis_notice',
                        'external_field_name' => 'analysis_bulletin'
                ),
		));
	    
	    //auth perso
	    $entities_linked = onto_pmb_entities_mapping::get_entity_rdf_linked_entities_mapping('record');
	    
	    foreach ($entities_linked as $link_name => $entity) {
	        if ($entity['type'] == TYPE_AUTHPERSO) {
	            $this->linked_entities ['http://www.pmbservices.fr/ontology#'.$link_name] = array(
	                'type' => 'authperso',
	                'table' => 'notices_authperso',
	                'reference_field_name' => 'notice_authperso_notice_num',
	                'external_field_name' => 'notice_authperso_authority_num',
	                'other_fields' => array(
	                    'notice_authperso_authority_num' => $entity['arguments'][0],
	                )
	            );
	        }
	    }
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
	
	public function insert_bulletin($values){
		if($values[0]['value'] && $this->entity_id){
			$bull_id = $this->store->get_property($values[0]["value"],"pmb:identifier");
			if($bull_id){
				$hierarchical_lvl = $this->store->get_property($this->entity_data['uri'], 'pmb:hierarchical_lvl');
				$bibliographical_lvl = $this->store->get_property($this->entity_data['uri'], 'pmb:bibliographical_lvl');
				if ((!empty($hierarchical_lvl[0]['value']) && ($hierarchical_lvl[0]['value'] == '2')) && 
					(!empty($bibliographical_lvl[0]['value']) && ($bibliographical_lvl[0]['value'] == 'a'))) {
					$query = "insert into analysis (analysis_bulletin, analysis_notice)
					values ('".$bull_id[0]["value"]."', '".$this->entity_id."')";
					pmb_mysql_query($query);
				}	
			}
		}
	}
}