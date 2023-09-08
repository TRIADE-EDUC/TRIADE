<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_record.class.php,v 1.20 2018-09-05 15:27:30 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator.class.php');
require_once($class_path.'/notice.class.php');
require_once($class_path.'/acces.class.php');

class rdf_entities_integrator_record extends rdf_entities_integrator {
	
	protected $table_name = 'notices';
	
	protected $table_key = 'notice_id';
	
	protected $ppersos_prefix = 'notices';
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'http://www.pmbservices.fr/ontology#bibliographical_lvl' => 'niveau_biblio',
				'http://www.pmbservices.fr/ontology#hierarchical_lvl' => 'niveau_hierar',
				'http://www.pmbservices.fr/ontology#doctype' => 'typdoc',
				'http://www.pmbservices.fr/ontology#tit1' => 'tit1',
				'http://www.pmbservices.fr/ontology#tit2' => 'tit2',
				'http://www.pmbservices.fr/ontology#tit3' => 'tit3',
				'http://www.pmbservices.fr/ontology#tit4' => 'tit4',
				'http://www.pmbservices.fr/ontology#tnvol' => 'tnvol',
				'http://www.pmbservices.fr/ontology#nocoll' => 'nocoll',
				'http://www.pmbservices.fr/ontology#has_date' => 'year',
				'http://www.pmbservices.fr/ontology#publishing_notice' => 'mention_edition',
				'http://www.pmbservices.fr/ontology#isbn' => 'code',
				'http://www.pmbservices.fr/ontology#nb_pages' => 'npages',
				'http://www.pmbservices.fr/ontology#illustration' => 'ill',
				'http://www.pmbservices.fr/ontology#size' => 'size',
				'http://www.pmbservices.fr/ontology#price' => 'prix',
				'http://www.pmbservices.fr/ontology#accompanying_material' => 'accomp',
				'http://www.pmbservices.fr/ontology#general_note' => 'n_gen',
				'http://www.pmbservices.fr/ontology#content_note' => 'n_contenu',
				'http://www.pmbservices.fr/ontology#resume_note' => 'n_resume',
				'http://www.pmbservices.fr/ontology#keywords' => 'index_l',
				'http://www.pmbservices.fr/ontology#url' => 'lien',
				'http://www.pmbservices.fr/ontology#eformat' => 'eformat',
				'http://www.pmbservices.fr/ontology#record_language' => 'indexation_lang',
				'http://www.pmbservices.fr/ontology#new_record' => 'notice_is_new',
				'http://www.pmbservices.fr/ontology#comment' => 'commentaire_gestion',
				'http://www.pmbservices.fr/ontology#thumbnail_url' => 'thumbnail_url',
				'http://www.pmbservices.fr/ontology#thumbnail' => 'thumbnail',
				'http://www.pmbservices.fr/ontology#has_record_status' => 'statut'
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
				'http://www.pmbservices.fr/ontology#tparent' => 'tparent_id',
				'http://www.pmbservices.fr/ontology#has_publisher' => 'ed1_id',
				'http://www.pmbservices.fr/ontology#has_other_publisher' => 'ed2_id',
				'http://www.pmbservices.fr/ontology#has_collection' => 'coll_id',
				'http://www.pmbservices.fr/ontology#has_subcollection' => 'subcoll_id',
				'http://www.pmbservices.fr/ontology#has_indexint' => 'indexint'
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
    	                'type_object' => TYPE_NOTICE
    	            )
    	        ),
				'http://www.pmbservices.fr/ontology#has_category' => array(
						'table' => 'notices_categories',
						'reference_field_name' => 'notcateg_notice',
						'external_field_name' => 'num_noeud'
				),
				'http://www.pmbservices.fr/ontology#has_work' => array(
						'table' => 'notices_titres_uniformes',
						'reference_field_name' => 'ntu_num_notice',
						'external_field_name' => 'ntu_num_tu'
				),
				'http://www.pmbservices.fr/ontology#publication_language' => array(
						'table' => 'notices_langues',
						'reference_field_name' => 'num_notice',
						'external_field_name' => 'code_langue',
						'other_fields' => array(
								'type_langue' => '0'
						)
				),
				'http://www.pmbservices.fr/ontology#original_language' => array(
						'table' => 'notices_langues',
						'reference_field_name' => 'num_notice',
						'external_field_name' => 'code_langue',
						'other_fields' => array(
								'type_langue' => '1'
						)
				),
		));
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
		$this->special_fields = array_merge(parent::init_special_fields(), array(
				'http://www.pmbservices.fr/ontology#has_main_author' => array(
						"method" => array($this,"insert_responsability"),
						"arguments" => array(0)
				),
				'http://www.pmbservices.fr/ontology#has_other_author' => array(
						"method" => array($this,"insert_responsability"),
						"arguments" => array(1)
				),
				'http://www.pmbservices.fr/ontology#has_secondary_author' => array(
						"method" => array($this,"insert_responsability"),
						"arguments" => array(2)
				),
				'http://www.pmbservices.fr/ontology#has_linked_record' => array(
						"method" => array($this,"insert_linked_record"),
						"arguments" => array('up',0)
				),
				'http://www.pmbservices.fr/ontology#has_date' => array(
						"method" => array($this, "insert_parution_date"),
						"arguments" => array()
				),
				'http://www.pmbservices.fr/ontology#has_bulletin' => array(
						"method" => array($this, "insert_bulletin"),
						"arguments" => array()
				),
				'http://www.pmbservices.fr/ontology#has_concept' => array(
						"method" => array($this, "insert_concept"),
						"arguments" => array()
				),
		));
		return $this->special_fields;
	}
	
	protected function init_cataloging_entities() {
		$this->cataloging_entities = array_merge(parent::init_cataloging_entities(), array(
				'http://www.pmbservices.fr/ontology#has_main_author' => array(
						'table' => 'responsability',
						'reference_field_name' => 'responsability_notice',
						'external_field_name' => 'responsability_author',
						'other_fields' => array(
								'responsability_type' => 0,
								'responsability_fonction' => '070'
						)
				),
				'http://www.pmbservices.fr/ontology#has_other_author' => array(
						'table' => 'responsability',
						'reference_field_name' => 'responsability_notice',
						'external_field_name' => 'responsability_author',
						'other_fields' => array(
								'responsability_type' => 1,
								'responsability_fonction' => '070'
						)
				),
				'http://www.pmbservices.fr/ontology#has_secondary_author' => array(
						'table' => 'responsability',
						'reference_field_name' => 'responsability_notice',
						'external_field_name' => 'responsability_author',
						'other_fields' => array(
								'responsability_type' => 2,
								'responsability_fonction' => '070'
						)
				),
		));
		
		//auth perso
		$entities_linked = onto_pmb_entities_mapping::get_entity_rdf_linked_entities_mapping('record');
		
		foreach ($entities_linked as $link_name => $entity) {
			if ($entity['type'] == TYPE_AUTHPERSO) {
				$this->cataloging_entities['http://www.pmbservices.fr/ontology#'.$link_name] = array(
						'table' => 'notices_authperso',
						'reference_field_name' => 'notice_authperso_notice_num',
						'external_field_name' => 'notice_authperso_authority_num',
				);
			}
		}
		
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
	
	protected function post_create($uri) {
		global $gestion_acces_active, $gestion_acces_user_notice, $gestion_acces_empr_notice;
		
		if ($this->integration_type && $this->entity_id) {
			// Audit
			$query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
			$query.= 'values ("'.AUDIT_NOTICE.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.addslashes(json_encode(array("uri" => $uri))).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
			if ($gestion_acces_active == 1) {
				$ac = new acces();
				//traitement des droits acces user_notice
				if ($gestion_acces_user_notice==1) {
					$dom_1 = $ac->setDomain(1);
					$dom_1->applyRessourceRights($this->entity_id);
				}
				//traitement des droits acces empr_notice
				if ($gestion_acces_empr_notice==1) {
					$dom_2 = $ac->setDomain(2);
					$dom_2->applyRessourceRights($this->entity_id);
				}
			}
			// Indexation
			notice::majNoticesTotal($this->entity_id);
		}
	}
	
	public function insert_responsability($responsability_type, $values) {
		$query = "	DELETE FROM responsability
					WHERE responsability_notice = '".$this->entity_id."'
					AND responsability_type = '".$responsability_type."'";
		pmb_mysql_query($query);

		$query_values = "";
		foreach($values as $value) {
			$author = $this->integrate_entity($value["value"]);
			$this->entity_data['children'][] = $author;
			if ($query_values) {
				$query_values .= ',';
			}
			// On fixe la fonction à auteur en attendant de trouver une solution pour les responsabilités
			$query_values .= "('".$author["id"]."', '".$this->entity_id."', '070', '".$responsability_type."')";
		}
		$query = "	INSERT INTO responsability (responsability_author, responsability_notice, responsability_fonction, responsability_type) 
					VALUES ".$query_values;
		pmb_mysql_query($query);
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
	
	public function insert_linked_record($direction, $num_reverse_link, $values) {
		$query = "	DELETE FROM notices_relations
					WHERE num_notice = '".$this->entity_id."'
					AND direction = '".$direction."'
					AND num_reverse_link = '".$num_reverse_link."'";
		pmb_mysql_query($query);
		
		$query_values = "";
		$rank = 1;
		foreach($values as $value) {
			$record = $this->store->get_property($value["value"],"pmb:has_record");
			$relation_type = $this->store->get_property($value["value"],"pmb:relation_type");
			$record = $this->integrate_entity($record[0]["value"]);
			$this->entity_data['children'][] = $record;
			if ($query_values) {
				$query_values .= ',';
			}
			$query_values .= "('".$this->entity_id."', '".$record["id"]."', '".$relation_type[0]["value"]."', '".$rank."', '".$direction."', '".$num_reverse_link."')";
			$rank++;
		}
		$query = "	INSERT INTO notices_relations (num_notice, linked_notice, relation_type, rank, direction, num_reverse_link)
					VALUES ".$query_values;
		pmb_mysql_query($query);
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