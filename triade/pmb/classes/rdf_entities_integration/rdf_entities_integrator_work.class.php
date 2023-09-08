<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_work.class.php,v 1.10 2018-09-07 11:47:33 tsamson Exp $

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
								'oeuvre_link_expression' => '1',
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
				'http://www.pmbservices.fr/ontology#has_responsability_author',
				'http://www.pmbservices.fr/ontology#has_responsability_performer',
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
			$query.= 'values ("'.AUDIT_TITRE_UNIFORME.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.addslashes(json_encode(array("uri" => $uri))).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
			// Indexation
			titre_uniforme::update_index($this->entity_id);
		}
	}
	
	public function cataloging_insert_reversed_link_work($expression = 0, $other_link = 0, $to = 0, $from = 0, $type = '') {
		$to+=0;
		$from+=0;
		$oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
		if(!isset($oeuvre_link->inverse_of[$type])){
			return;
		}
		$select = 'select oeuvre_link_type from tu_oeuvres_links where oeuvre_link_from = "'.$to.'" and oeuvre_link_to= "'.$from.'" and oeuvre_link_type = "'.$oeuvre_link->inverse_of[$type].'" ';
		$result = pmb_mysql_query($select);
		if (pmb_mysql_num_rows($result) > 0) {
			return;
		}
		$max_query = 'select max(oeuvre_link_order) from tu_oeuvres_links where oeuvre_link_from = "'.$to.'"';
		$result = pmb_mysql_query($max_query);
		$max_order = pmb_mysql_result($result, 0, 0);
		$query = 'insert into tu_oeuvres_links (oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link, oeuvre_link_order) VALUES ("'.$to.'","'.$from.'","'.$oeuvre_link->inverse_of[$type].'", '.$expression.', '.$other_link.', "'.($max_order+1).'")';
		pmb_mysql_query ($query);
		return true;
	}
}