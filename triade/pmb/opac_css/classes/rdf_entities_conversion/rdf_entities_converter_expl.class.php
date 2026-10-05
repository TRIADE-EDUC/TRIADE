<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_expl.class.php,v 1.2 2022/03/10 12:56:56 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/explnum.class.php');

class rdf_entities_converter_expl extends rdf_entities_converter {
    
    protected $table_name = 'exemplaires';
    
    protected $table_key = 'expl_id';
    
    protected $ppersos_prefix = 'expl';
    
    protected $type_constant = TYPE_EXPL;
    
    protected $aut_table_constant = AUDIT_EXPL;
    
    protected function init_map_fields() {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
            'expl_cb' => 'http://www.pmbservices.fr/ontology#cb',
            'expl_typdoc' => 'http://www.pmbservices.fr/ontology#typdoc',
            'expl_cote' => 'http://www.pmbservices.fr/ontology#cote',
            'expl_section' => 'http://www.pmbservices.fr/ontology#docs_section',
            'expl_statut' => 'http://www.pmbservices.fr/ontology#has_expl_status',
        ));
    }
    
    protected function init_foreign_fields() {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
            'expl_notice' => array(
                'type' => 'record',
                'property' => 'http://www.pmbservices.fr/ontology#has_record',
            ),
        ));
        return $this->foreign_fields;
    }
    
    protected function init_linked_entities() {
        $this->linked_entities = array_merge(parent::init_linked_entities(), array(
            'http://www.pmbservices.fr/ontology#expl_location' => array(
                'table' => 'docs_location',
                'external_field_name' => 'num_location',
                'reference_field_name' => 'idlocation'
            ),
            'http://www.pmbservices.fr/ontology#owner' => array(
                'table' => 'lenders',
                'reference_field_name' => 'expl_owner',
                'external_field_name' => 'idlender'
            )
        ));
        return $this->linked_entities;
    }
}