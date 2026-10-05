<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_work.class.php,v 1.3 2022/06/02 14:04:35 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_authority.class.php');
require_once($class_path.'/titre_uniforme.class.php');
require_once($class_path.'/marc_table.class.php');

class rdf_entities_converter_work extends rdf_entities_converter_authority {
	
	protected $table_name = 'titres_uniformes';
	
	protected $table_key = 'tu_id';
	
	protected $ppersos_prefix = 'tu';
	
	protected $type_constant = TYPE_TITRE_UNIFORME;

	protected $aut_table_constant = AUT_TABLE_TITRES_UNIFORMES;
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'tu_id' => 'http://www.pmbservices.fr/ontology#identifier',
				'tu_date' => 'http://www.pmbservices.fr/ontology#date',
				'tu_name' => 'http://www.pmbservices.fr/ontology#label',
				'tu_oeuvre_type' => 'http://www.pmbservices.fr/ontology#work_type',
				'tu_oeuvre_nature' => 'http://www.pmbservices.fr/ontology#work_nature',
				'tu_forme' => 'http://www.pmbservices.fr/ontology#shape',
				'tu_forme_marclist' => 'http://www.pmbservices.fr/ontology#has_shape',
				'tu_lieu' => 'http://www.pmbservices.fr/ontology#place',
				'tu_sujet' => 'http://www.pmbservices.fr/ontology#subject',
				'tu_completude' => 'http://www.pmbservices.fr/ontology#intended_termination',
				'tu_public' => 'http://www.pmbservices.fr/ontology#targeted_audience',
				'tu_histoire' => 'http://www.pmbservices.fr/ontology#story',
				'tu_contexte' => 'http://www.pmbservices.fr/ontology#context',
				'tu_tonalite' => 'http://www.pmbservices.fr/ontology#tone',
				'tu_tonalite_marclist' => 'http://www.pmbservices.fr/ontology#has_tone',
				'tu_coordonnees' => 'http://www.pmbservices.fr/ontology#coord',
				'tu_equinoxe' => 'http://www.pmbservices.fr/ontology#equinox',
				'tu_caracteristique' => 'http://www.pmbservices.fr/ontology#other_feature'
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array());
		return $this->foreign_fields;
	}
	
	protected function init_linked_entities() {
		$this->linked_entities = array_merge(parent::init_linked_entities(), array(
    		    'http://www.pmbservices.fr/ontology#has_responsability_author' => array(
        		        'type' => 'responsability_tu',
        		        'table' => 'responsability_tu',
        		        'reference_field_name' => 'responsability_tu_num',
        		        'external_field_name' => 'id_responsability_tu',
        		        'other_fields' => array(
        		            'responsability_tu_type' => '0'
        		        ),
        		        'abstract_entity' => '1'
    		    ),
    		    'http://www.pmbservices.fr/ontology#has_responsability_performer' => array(
        		        'type' => 'responsability_tu',
        		        'table' => 'responsability_tu',
        		        'reference_field_name' => 'responsability_tu_num',
        		        'external_field_name' => 'id_responsability_tu',
        		        'other_fields' => array(
        		            'responsability_tu_type' => '1'
        		        ),
        		        'abstract_entity' => '1'
    		    ),
				'http://www.pmbservices.fr/ontology#has_concept' => array(
				        'type' => 'concept',
						'table' => 'index_concept',
						'reference_field_name' => 'num_object',
						'external_field_name' => 'num_concept',
						'other_fields' => array(
								'type_object' => TYPE_TITRE_UNIFORME
						)
				),
    		    'http://www.pmbservices.fr/ontology#expression_of' => array(
                        'type' => 'linked_work',
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_from',
						'external_field_name' => 'oeuvre_link_to',
						'other_fields' => array(
								'oeuvre_link_expression' => '0',
								'oeuvre_link_other_link' => '0'
						),
    		              'abstract_entity' => '1'
				),
				'http://www.pmbservices.fr/ontology#has_expression' => array(
				        'type' => 'linked_work',
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_from',
						'external_field_name' => 'oeuvre_link_to',
						'other_fields' => array(
								'oeuvre_link_expression' => '1',
								'oeuvre_link_other_link' => '0'
						),
				        'abstract_entity' => '1'
				),
				'http://www.pmbservices.fr/ontology#has_other_link' => array(
                        'type' => 'linked_work',
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_from',
						'external_field_name' => 'oeuvre_link_to',
						'other_fields' => array(
								'oeuvre_link_expression' => '0',
								'oeuvre_link_other_link' => '1'
						),
				    'abstract_entity' => '1'
				),
				'http://www.pmbservices.fr/ontology#has_event' => array(
				        'type' => 'work',
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
				        'type' => 'record',
						'table' => 'notices_titres_uniformes',
						'reference_field_name' => 'ntu_num_tu',
						'external_field_name' => 'ntu_num_notice'
				),
//     		    'http://www.pmbservices.fr/ontology#has_linked_authority' => array(
//         		        'type' => 'linked_authority',
//         		        'table' => 'aut_link',
//         		        'reference_field_name' => 'aut_link_from_num',
//         		        'external_field_name' => 'aut_link_to_num',
//         		        'other_fields' => array(
//         		            'aut_link_from' => AUT_TABLE_TITRES_UNIFORMES,
//         		        ),
//                         'abstract_entity' => '1'
//     		    ),
		));
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
	    $this->special_fields = array_merge(parent::init_special_fields(), array(
	        'http://www.pmbservices.fr/ontology#thumbnail_url' => array(
	            "method" => array($this,"get_thumbnail_url"),
	            "arguments" => array($this->aut_table_constant)
	        )
	    ));
	    return $this->special_fields;
	}
}