<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_authority.class.php,v 1.5 2023/04/06 15:28:54 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator.class.php');
require_once($class_path.'/marc_table.class.php');
require_once($class_path.'/aut_link.class.php');
require_once($class_path.'/authority.class.php');

class rdf_entities_integrator_authority extends rdf_entities_integrator {
    protected function init_special_fields() {
        $this->special_fields = array_merge(parent::init_special_fields(), array(
            'http://www.pmbservices.fr/ontology#has_linked_authority' => array(
                "method" => array($this,"insert_aut_link"),
                "arguments" => array()
            ),
        ));
        return $this->special_fields;
    }
    
    public function insert_thumbnail_url($authority_type, $values) 
    {
        if (empty($values[0]['value'])) {
            return false;
        }
        
        $query = 'SELECT 1 FROM authorities WHERE type_object = ' . $authority_type .' AND num_object = ' . $this->entity_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $query = 'UPDATE authorities SET thumbnail_url = "' . $values[0]['value'] .'" WHERE type_object = ' . $authority_type .' AND num_object = ' . $this->entity_id;
        } else {
            $query = 'INSERT INTO authorities (thumbnail_url, type_object, num_object) VALUES ("' . $values[0]['value'] .'", ' . $authority_type .', ' . $this->entity_id . ')';
        }
        pmb_mysql_query($query);
    }
    
    public function insert_aut_link($values) {
        $from_type = authority::get_const_type_object($this->ppersos_prefix);
        if(!empty($this->authperso_num)) {
            $from_type = 1000 + $this->authperso_num;
        }
        $from_type = intval($from_type);
        if (empty($from_type)) {
            return;
        }
        
        $aut_link = new aut_link($from_type, $this->entity_id);
        $aut_link->delete();
        
        $relations = new marc_list("aut_link");
        $i = 0;
        foreach ($values as $value) {
            //a voir pour la reciprocite des liens
            $reciproq = true;
            $authority_uri = $this->store->get_property($value["value"], "pmb:has_authority");
            $authority_id = $this->store->get_property($authority_uri[0]["value"], "pmb:identifier");
            $authority_id = $authority_id[0]["value"];
            $aut_link_type = $this->store->get_property($value["value"], "pmb:authority_type");
            $aut_link_type = $aut_link_type [0]["value"] ?? 0;
            if (empty($authority_id)) {
                //ajout via un sous formulaire
                $authority = $this->integrate_entity($authority_uri[0]['value'], true);
                if (empty($authority['id'])) {
                    continue;
                }
                $authority_id = $authority['id'];
                $this->entity_data['children'][] = $authority;
            }
            $aut_link_relation_type = $this->store->get_property($value["value"], "pmb:relation_type_authority");
            $aut_link_relation_type = $aut_link_relation_type[0]["value"] ?? "";
            $aut_link_comment = $this->store->get_property($value["value"], "pmb:comment");
            $aut_link_comment = $aut_link_comment[0]["value"] ?? "";
            $aut_link_string_start_date = $this->store->get_property($value["value"], "pmb:start_date");
            $aut_link_string_start_date = $aut_link_string_start_date[0]["value"] ?? "";
            $aut_link_string_end_date = $this->store->get_property($value["value"], "pmb:end_date");
            $aut_link_string_end_date = $aut_link_string_end_date[0]["value"] ?? "";
            $aut_link_start_date = $aut_link_string_start_date ? detectFormatDate($aut_link_string_start_date) : "0000-00-00";
            $aut_link_end_date = $aut_link_string_end_date ? detectFormatDate($aut_link_string_end_date, "max") : "0000-00-00";
            
            $direction = "up";
            if (array_key_exists($aut_link_type, $relations->table['descendant'])) {
                $direction = 'down';
            }	
            $query="INSERT INTO aut_link SET
                        aut_link_from='" . $from_type . "',
                        aut_link_from_num='" . $this->entity_id . "',
                        aut_link_to='" . $aut_link_type . "',
                        aut_link_to_num='" . $authority_id . "',
                        aut_link_type='" . $aut_link_relation_type . "',
                        aut_link_comment='" . $aut_link_comment . "',
                        aut_link_string_start_date='" . $aut_link_string_start_date . "',
                        aut_link_string_end_date='" . $aut_link_string_end_date . "',
                        aut_link_start_date='" . $aut_link_start_date . "',
                        aut_link_end_date='" . $aut_link_end_date . "',
                        aut_link_rank='" . $i . "',
                        aut_link_direction='" . $direction . "'
                    ";
            pmb_mysql_query($query);
            $last_id = pmb_mysql_insert_id();
            
            if ($reciproq) {
                $type = $relations->inverse_of[$aut_link_relation_type];
                if ($direction === "up") {
                    $direction = "down";
                } else {
                    $direction = "up";
                }
                $query="INSERT INTO aut_link SET
                        aut_link_from='" . $aut_link_type . "',
                        aut_link_from_num='" . $authority_id . "',
                        aut_link_to='" . $from_type . "',
                        aut_link_to_num='" . $this->entity_id . "',
                        aut_link_type='" . $type . "',
                        aut_link_comment='" . $aut_link_comment . "',
                        aut_link_string_start_date='" . $aut_link_string_start_date . "',
                        aut_link_string_end_date='" . $aut_link_string_end_date . "',
                        aut_link_start_date='" . $aut_link_start_date . "',
                        aut_link_end_date='" . $aut_link_end_date . "',
                        aut_link_rank='" . $i . "',
                        aut_link_direction='" . $direction . "',
                        aut_link_reverse_link_num='" . $last_id . "'
                    ";
                pmb_mysql_query($query);
                $reciproc_id = pmb_mysql_insert_id();
                $query = "UPDATE aut_link SET aut_link_reverse_link_num=" . $reciproc_id . " WHERE id_aut_link=" . $last_id;
                pmb_mysql_query($query);
            }
            $i++;
        }
    }
    
}