<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_linked_work.class.php,v 1.1 2018-09-24 13:39:22 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter.class.php');

class rdf_entities_converter_linked_work extends rdf_entities_converter {
    protected $table_name = 'tu_oeuvres_links';
    
    protected $table_key = 'oeuvre_link_from';
    
    public $abstract_entity = true;
    
    protected function init_map_fields() {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
            'oeuvre_link_type' => 'http://www.pmbservices.fr/ontology#relation_type_work',
        ));
        return $this->map_fields;
    }
    
    protected function init_foreign_fields() {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
            'oeuvre_link_from' => array(
                'type' => 'work',
                'property' => 'http://www.pmbservices.fr/ontology#has_work'
            ),
        ));
        return $this->foreign_fields;
    }
}