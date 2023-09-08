<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_responsability_tu.class.php,v 1.1 2018-09-24 13:39:21 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter.class.php');
require_once($class_path.'/author.class.php');

class rdf_entities_converter_responsability_tu extends rdf_entities_converter {
    protected $table_name = 'responsability_tu';
    
    protected $table_key = 'id_responsability_tu';
    
    protected function init_map_fields() {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
            'responsability_tu_fonction' => 'http://www.pmbservices.fr/ontology#author_function',
        ));
        return $this->map_fields;
    }
    
    protected function init_foreign_fields() {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
            'responsability_tu_author_num' => array(
                'type' => 'author',
                'property' => 'http://www.pmbservices.fr/ontology#has_author'
            ),
        ));
        return $this->foreign_fields;
    }
}