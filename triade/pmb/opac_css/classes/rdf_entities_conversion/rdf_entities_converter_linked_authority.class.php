<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_linked_authority.class.php,v 1.4 2023/07/26 12:49:31 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter.class.php');

class rdf_entities_converter_linked_authority extends rdf_entities_converter {
    protected $table_name = 'aut_link';

    protected $table_key = 'aut_link_from_num';

    private $authority_type;

    private $authority_id;

    public $abstract_entity = true;

    public function __construct($entity_id, $entity_type, $depth = 1) {
        $this->entity_id = $entity_id;
        if (strpos($entity_id, "_") !== false) {
            $explode_id = explode('_', $entity_id);
            $this->authority_type = $explode_id[0] * 1;
            $this->authority_id = $explode_id[1] * 1;
        }
        $this->entity_type = $entity_type;
        if (!is_numeric($depth*1)) {
            $this->depth = -1;
        }
    }

    protected function init_map_fields() {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
            'aut_link_type' => 'http://www.pmbservices.fr/ontology#relation_type_authority',
        ));
        return $this->map_fields;
    }

    protected function init_special_fields() {
        $this->special_fields = array_merge(parent::init_special_fields(), array(
            'http://www.pmbservices.fr/ontology#has_authority' => array(
                "method" => array($this,"get_assertions_from_authority"),
                "arguments" => array()
            ),
            'http://www.pmbservices.fr/ontology#relation_type_authority' => array(
                "method" => array($this,"get_relation_type_authority"),
                "arguments" => array()
            ),
        ));
        return $this->special_fields;
    }

    public function get_assertions_from_authority() {
        $type = $this->get_string_from_type_object($this->authority_type);
        $subject = $this->prefix_rdf."/".$this->entity_type."#".$this->entity_id;
        $object = onto_common_uri::get_new_uri($this->prefix_rdf.$this->entity_type.'#');
        $object_type = $this->get_object_type_from_type($type);
        $object_properties = $this->get_object_properties($this->authority_id, $type);
        $object_properties['assertions'] = rdf_entities_converter_controller::convert($this->authority_id, $type, $object, $this->depth);
        $assertion = new onto_assertion($subject, "http://www.pmbservices.fr/ontology#has_authority", $object , $object_type, $object_properties);
        return $assertion;
    }

    public function get_relation_type_authority() {
        $subject = $this->prefix_rdf."/".$this->entity_type."#".$this->entity_id;
        $query = "
            SELECT aut_link_type
            FROM aut_link
            WHERE aut_link_to_num = ".$this->authority_id."
            AND aut_link_to = ".$this->authority_type;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_array($result);
        }
        $assertion = new onto_assertion($subject, "http://www.pmbservices.fr/ontology#relation_type_authority", $row[0] , 'http://www.w3.org/2000/01/rdf-schema#Literal', array('type' => 'literal'));
        return $assertion;
    }

    private function get_string_from_type_object($type_object) {
        switch ($type_object) {
            case AUT_TABLE_AUTHORS :
                return 'author';
            case AUT_TABLE_CATEG :
                return 'category';
            case AUT_TABLE_PUBLISHERS :
                return 'publisher';
            case AUT_TABLE_COLLECTIONS :
                return 'collection';
            case AUT_TABLE_SUB_COLLECTIONS :
                return 'subcollection';
            case AUT_TABLE_SERIES :
                return 'serie';
            case AUT_TABLE_TITRES_UNIFORMES :
                return 'work';
            case AUT_TABLE_INDEXINT :
                return 'indexint';
            case AUT_TABLE_CONCEPT :
                return 'concept';
            case AUT_TABLE_AUTHPERSO :
                return 'authperso';
            default :
                return "";
        }
    }

    public static function get_entity_isbd($id, $type) {
        $isbd = parent::get_entity_isbd($id, $type);
        $isbd = sprintf(static::get_prefixe_entity($type, $id) ,$isbd);
        return $isbd;
    }

    private static function get_prefixe_entity($type_object, $id = '') {
        global $msg;
        switch($type_object){
            case 'author' :
            case (string) AUT_TABLE_AUTHORS :
                return $msg['aut_link_author'];
            case 'category' :
            case (string) AUT_TABLE_CATEG :
                return $msg['aut_link_categ'];
            case 'publisher' :
            case (string) AUT_TABLE_PUBLISHERS :
                return $msg['aut_link_publisher'];
            case 'collection' :
            case (string) AUT_TABLE_COLLECTIONS :
                return $msg['aut_link_coll'];
            case 'subcollection' :
            case (string) AUT_TABLE_SUB_COLLECTIONS :
                return $msg['aut_link_subcoll'];
            case 'serie' :
            case (string) AUT_TABLE_SERIES :
                return $msg['aut_link_serie'];
            case 'work' :
            case (string) AUT_TABLE_TITRES_UNIFORMES :
                return $msg['aut_link_tu'];
            case 'indexint' :
            case (string) AUT_TABLE_INDEXINT :
                return $msg['aut_link_indexint'];
            case 'concept' :
            case (string) AUT_TABLE_CONCEPT :
                return $msg['aut_link_concept'];
            case 'authperso' :
            case (string) AUT_TABLE_AUTHPERSO :
                $authperso = new authperso($id - 1000);
                return '['.$authperso->info['name'].'] %1s';
            default :
                return '%1s';
        }
    }
}