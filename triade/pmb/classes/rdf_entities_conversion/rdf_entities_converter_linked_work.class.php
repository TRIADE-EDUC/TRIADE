<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_linked_work.class.php,v 1.2 2022/05/25 08:24:24 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter.class.php');

class rdf_entities_converter_linked_work extends rdf_entities_converter {
    protected $table_name = 'tu_oeuvres_links';
    
    protected $table_key = 'oeuvre_link_to';
    
    public $abstract_entity = true;
    
    protected function init_foreign_fields() {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
            'oeuvre_link_to' => array(
                'type' => 'work',
                'property' => 'http://www.pmbservices.fr/ontology#has_work'
            ),
        ));
        return $this->foreign_fields;
    }
    
    protected function init_special_fields() {
        $this->special_fields = array_merge(parent::init_special_fields(), array(
            'http://www.pmbservices.fr/ontology#relation_type_work' => array(
                "method" => array($this, "get_relation_type"),
                "arguments" => array(   )
            )
        ));
        return $this->special_fields;
    }
    
    protected function get_relation_type()
    {
        global $entity_id;
        $query = "SELECT oeuvre_link_type FROM tu_oeuvres_links WHERE oeuvre_link_to = '{$this->entity_id}' AND oeuvre_link_from = '$entity_id'";
        $result = pmb_mysql_query($query);
        $type = pmb_mysql_result($result, 0,0);
        return new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#relation_type_work", $type, "http://www.w3.org/2000/01/rdf-schema#Literal", array('type'=>"literal"));
    }
}