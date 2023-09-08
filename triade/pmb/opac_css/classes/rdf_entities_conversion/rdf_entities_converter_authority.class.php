<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_authority.class.php,v 1.1 2018-09-24 13:39:21 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter.class.php');

class rdf_entities_converter_authority extends rdf_entities_converter {
    protected $type_constant = null;
    protected $aut_table_constant = null;
    
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
            'http://www.pmbservices.fr/ontology#has_linked_authority' => array(
                'type' => 'linked_authority',
                'table' => 'aut_link',
                'reference_field_name' => 'aut_link_from_num',
                'external_field_name' => 'CONCAT(aut_link_to,"_",aut_link_to_num)',
                'other_fields' => array(
                    'aut_link_from' => $this->aut_table_constant,
                ),
                'abstract_entity' => '1'
            ),
        ));
        return $this->linked_entities;
    }
}