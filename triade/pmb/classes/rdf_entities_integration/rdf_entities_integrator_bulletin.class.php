<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_bulletin.class.php,v 1.6.2.2 2024/09/19 08:48:11 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

require_once($class_path . '/rdf_entities_integration/rdf_entities_integrator_record.class.php');

class rdf_entities_integrator_bulletin extends rdf_entities_integrator
{
    protected $table_name = 'bulletins';
        
    protected $table_key = 'bulletin_id';
        
    protected $ppersos_prefix = 'notices';
    protected $num_notice = 0;
    
    protected function init_map_fields()
    {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
            'http://www.pmbservices.fr/ontology#tit1' => 'bulletin_titre',
            'http://www.pmbservices.fr/ontology#publication_date' => 'date_date',
            'http://www.pmbservices.fr/ontology#has_date' => 'mention_date',
            'http://www.pmbservices.fr/ontology#number' => 'bulletin_numero',
        ));
        return $this->map_fields;
    }
    
    
    
    protected function init_foreign_fields()
    {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
            'http://www.pmbservices.fr/ontology#has_serial' => 'bulletin_notice',
        ));
        return $this->foreign_fields;
    }
    
    protected function init_base_query_elements()
    {
        // ajout de la notice 
        $record_integrator = new rdf_entities_integrator_record($this->store);
        if (!empty($this->num_notice)) {
            $record_integrator->set_entity_id($this->num_notice);
        }
        $record_integrator_data = $record_integrator->integrate_itself($this->entity_data["uri"]);
        $this->num_notice = $record_integrator_data["id"];
        // On définit les valeurs par défaut
        $this->base_query_elements = array_merge(parent::init_base_query_elements(), array(
            'num_notice' => $this->num_notice
        ));
    }
    
    protected function post_create($uri)
    {
        if ($this->integration_type && $this->entity_id) {
            // Audit
            $query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
            $query .= 'values ("' . AUDIT_BULLETIN . '", "' . $this->entity_id . '", "' . $this->contributor_id . '", "' . $this->integration_type . '", "' . $this->create_audit_comment($uri) . '", "' . $this->contributor_type . '")';
            pmb_mysql_query($query);
        }
        if (!empty($this->num_notice) && !empty($this->entity_id)) {
            //update des documents numeriques (pas tres propre...)
            //ça a ete fait dans l'urgence
            pmb_mysql_query("UPDATE explnum SET explnum_bulletin = " . $this->entity_id . ", explnum_notice  = 0 WHERE explnum_notice = " . $this->num_notice);
            
            //relation avec le perio
            $result = pmb_mysql_query("SELECT bulletin_notice FROM bulletins WHERE bulletin_id = ".$this->entity_id);
            if (pmb_mysql_num_rows($result)) {
                $serial_id = pmb_mysql_result($result, 0, 0);
                if (!empty($serial_id)) {
                    notice_relations::insert($this->num_notice, $serial_id, 'b', 1, 'up', false);
                }
            }
            $this->update_record_title();
        }
    }
    
    /**
     * retourne l'identifiant d'une entité en fonction de son URI
     * @param string $uri
     */
    protected function get_id_from_uri($uri) {
        $identifier_property = $this->store->get_property($uri, 'pmb:identifier');
        if (!empty($identifier_property[0])) {
            $bull_id = intval($identifier_property[0]['value']);
            $this->num_notice = $this->get_num_notice_from_id($bull_id);
            return  $bull_id;
        }
        return 0;
    }
    
    private function get_num_notice_from_id($bulletin_id) {
        $bulletin_id = intval($bulletin_id);
        $query = "SELECT num_notice FROM bulletins WHERE bulletin_id = ".$bulletin_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            return $row->num_notice;
        }
        return 0;
    }
    
    private function update_record_title() {
        if (!empty($this->entity_id) && !empty($this->num_notice)) {
            pmb_mysql_query("
                UPDATE notices
                JOIN bulletins ON num_notice = notice_id
                SET tit1 = CONCAT(bulletins.bulletin_numero,IF(bulletins.mention_date <>'', CONCAT(' - ', bulletins.mention_date), ''),IF(bulletins.bulletin_titre <>'', CONCAT(' - ', bulletins.bulletin_titre), ''))
                WHERE notice_id = $this->num_notice");
        }
    }
}