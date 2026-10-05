<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_linked_record.class.php,v 1.1.14.1 2024/06/25 09:46:21 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter.class.php');
require_once($class_path.'/author.class.php');

class rdf_entities_converter_linked_record extends rdf_entities_converter {
    protected $table_name = 'notices_relations';
    
    protected $table_key = 'id_notices_relations';
    
    public $abstract_entity = true;
    
    protected function init_map_fields() {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
            'relation_type' => 'http://www.pmbservices.fr/ontology#relation_type',
            'direction' => 'http://www.pmbservices.fr/ontology#direction',
            'num_reverse_link' => 'http://www.pmbservices.fr/ontology#num_reverse_link',
        ));
        return $this->map_fields;
    }
    
    protected function init_foreign_fields() {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
            'linked_notice' => array(
                'type' => 'record',
                'property' => 'http://www.pmbservices.fr/ontology#has_record'
            ),
        ));
        return $this->foreign_fields;
    }

    protected function init_special_fields() {
        $this->special_fields = array_merge(parent::init_special_fields(), array(
            'add_reverse_link' => [
                'method' => [$this, 'add_reverse_link'],
                'arguments' => []
            ]
        ));
        return $this->special_fields;
    }

    public function add_reverse_link($args = []) {
        $add_reverse_link = 0;
        $query = "SELECT num_reverse_link  FROM notices_relations WHERE id_notices_relations = $this->entity_id";
        $res = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($res)) {
            $row = pmb_mysql_fetch_assoc($res);
            $add_reverse_link = $row["num_reverse_link"] ? 1 : 0;
        }
        return new onto_assertion($this->uri, 'http://www.pmbservices.fr/ontology#add_reverse_link', $add_reverse_link, '', ['type' => 'literal']);
    }
}