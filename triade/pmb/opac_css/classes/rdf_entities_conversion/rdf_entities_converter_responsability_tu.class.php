<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_responsability_tu.class.php,v 1.3 2022/05/25 08:55:18 qvarin Exp $

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
    
    protected function init_special_fields() {
        $this->special_fields = array_merge(parent::init_special_fields(), array(
            'qualification_author' => [
                'method' => [$this, 'get_qualification'],
                'arguments' => []
            ]
        ));
        
        return $this->special_fields;
    }
    
    public function get_qualification($args = []) {
        $vedette_id = 0;
        
        $types_object = [TYPE_TU_RESPONSABILITY, TYPE_TU_RESPONSABILITY_INTERPRETER];
        $query = "SELECT num_vedette AS id FROM vedette_link WHERE num_object = $this->entity_id AND type_object in (" . implode(',', $types_object) . ")";
        $res = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($res)) {
            $row = pmb_mysql_fetch_object($res);
            $vedette_id = $row->id;
        }
        
        $vedette_composee = new vedette_composee($vedette_id, 'tu_authors');
        
        $vedette = [
            'apercu_vedette' => $vedette_composee->get_label(),
            'type' => '',
            'id' => $vedette_composee->get_id(),
            'grammar' => $vedette_composee->get_config_filename()
        ];
        
        $elements = $vedette_composee->get_elements();
        foreach ($elements as $role => $subelements) {
            foreach ($subelements as $element) {
                $vedette['elements'][$role][] = [
                    'label' => $element->get_isbd(),
                    'id' => $element->get_id(),
                    'type' => $element->get_type(),
                    'available_field_num' => $element->get_num_available_field()
                ];
                
            }
        }
        
        return new onto_assertion($this->uri, 'http://www.pmbservices.fr/ontology#author_qualification', encoding_normalize::json_encode($vedette), '', ['type' => 'literal']);
    }
}