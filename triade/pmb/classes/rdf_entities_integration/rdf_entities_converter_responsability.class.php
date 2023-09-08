<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_responsability.class.php,v 1.1 2018-09-11 11:33:09 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_converter.class.php');
require_once($class_path.'/author.class.php');

class rdf_entities_converter_responsability extends rdf_entities_converter {
    protected $table_name = 'responsability';
    
    protected $table_key = 'id_responsability';
    
    protected function init_map_fields() {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
            'responsability_fonction' => 'http://www.pmbservices.fr/ontology#author_function',
        ));
        return $this->map_fields;
    }
    
    protected function init_foreign_fields() {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
            'responsability_author' => array(
                'type' => 'author',
                'property' => 'http://www.pmbservices.fr/ontology#has_author'
            ),
        ));
        return $this->foreign_fields;
    }
}