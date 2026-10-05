<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_concept.class.php,v 1.4 2022/01/18 09:44:35 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . '/rdf_entities_integration/rdf_entities_integrator_authority.class.php');
if (! class_exists("skos_concept")) {
    require_once $class_path . '/skos/skos_concept.class.php';
}

class rdf_entities_integrator_concept extends rdf_entities_integrator_authority
{
    
    /**
     *
     * @var skos_concept
     */
    protected $skos_concept;
    
    /**
     * URI du concept
     * @var string
     */
    protected $uri;
    
    /**
     * Prefixe associé aux champs persos de l'entité
     * @var string
     */
    protected $ppersos_prefix = 'skos';
    
    protected const IS_CHILDREN = array(
        'broaders',
        'narrowers',
        'related',
        'skos:exactMatch',
        'skos:closeMatch',
        'skos:mappingRelation',
        'skos:broadMatch',
        'skos:narrowMatch',
        'related_match',
        'skos:semanticRelation'
    );

    /**
     * Constructeur
     *
     * @param rdf_entities_store $store
     */
    public function __construct($store)
    {
        $this->init_skos_concept();
        parent::__construct($store);
    }

    public function init_skos_concept()
    {
        $this->skos_concept = new skos_concept();
    }

    protected function init_map_fields()
    {
        $this->map_fields = array_merge(parent::init_map_fields(), array());
        return $this->map_fields;
    }

    protected function init_foreign_fields()
    {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array());
        return $this->foreign_fields;
    }

    protected function init_linked_entities()
    {
        $this->linked_entities = array_merge(parent::init_linked_entities(), array());
        return $this->linked_entities;
    }

    protected function init_special_fields()
    {
        $this->special_fields = array_merge(parent::init_special_fields(), array(
            'http://www.pmbservices.fr/ontology#has_authority_status' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('pmb:has_authority_status')
            ),
            'http://www.pmbservices.fr/ontology#comment_multilingue' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:scopeNote')
            ),
            'http://www.pmbservices.fr/ontology#label_multilingue' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:prefLabel')
            ),
            'http://www.pmbservices.fr/ontology#note_multilingue' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:note')
            ),
            'http://www.pmbservices.fr/ontology#note_editable_multilingue' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:changeNote')
            ),
            'http://www.pmbservices.fr/ontology#editorialnote_multilingue' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:editorialNote')
            ),
            'http://www.pmbservices.fr/ontology#definition_multilingue' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:definition')
            ),
            'http://www.pmbservices.fr/ontology#exemple_multilingue' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:example')
            ),
            'http://www.pmbservices.fr/ontology#historynote_multilingue' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:historyNote')
            ),
            'http://www.pmbservices.fr/ontology#altlabel' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:altLabel')
            ),
            'http://www.pmbservices.fr/ontology#hiddenlabel' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:hiddenLabel')
            ),
            'http://www.pmbservices.fr/ontology#notation' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:notation')
            ),
            'http://www.pmbservices.fr/ontology#has_concept_scheme' => array(
                'method' => array($this, 'set_property'),
                'arguments' => array('schemes')
            ),
            'http://www.pmbservices.fr/ontology#has_broader' => array(
                'method' => array($this, 'set_property'),
                'arguments' => array('broaders')
            ),
            'http://www.pmbservices.fr/ontology#has_narrower' => array(
                'method' => array($this, 'set_property'),
                'arguments' => array('narrowers')
            ),
            'http://www.pmbservices.fr/ontology#has_related' => array(
                'method' => array($this, 'set_property'),
                'arguments' => array('related')
            ),
            'http://www.pmbservices.fr/ontology#has_exact_match' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:exactMatch')
            ),
            'http://www.pmbservices.fr/ontology#has_close_match' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:closeMatch')
            ),
            'http://www.pmbservices.fr/ontology#has_mapping_relation' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:mappingRelation')
            ),
            'http://www.pmbservices.fr/ontology#has_broad_match' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:broadMatch')
            ),
            'http://www.pmbservices.fr/ontology#has_narrow_match' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:narrowMatch')
            ),
            'http://www.pmbservices.fr/ontology#has_related_match' => array(
                'method' => array($this, 'set_property'),
                'arguments' => array('related_match')
            ),
            'http://www.pmbservices.fr/ontology#has_semantic_relation' => array(
                'method' => array($this, 'insert_field'),
                'arguments' => array('skos:semanticRelation')
            )
        ));
        return $this->special_fields;
    }

    /**
     * @param array $values
     * @param string $property_name
     * @return skos_concepts_list|array
     */
    private function format_data($values, $property_name = "") 
    {
        switch ($property_name) {
            case 'broaders':
            case 'narrowers':
            case 'related':
            case 'related_match':
                $skos_concepts_list = new skos_concepts_list();
                
                $length = count($values);
                for ($i = 0; $i < $length; $i ++) {
                    $result = array();
                    if (in_array($property_name, self::IS_CHILDREN)) {
                        $entity_data = $this->add_children_concept($values[$i]['value']);
                        $identifier = intval($entity_data['id']) ?? 0;
                    } else {
                        $result = $this->store->get_property($values[$i]['value'], 'pmb:identifier');
                        $identifier = intval($result[0]['value']) ?? 0;
                    }
                    if ($identifier != 0) {                        
                        $uri = onto_common_uri::get_uri($identifier);
                        if (!empty($uri)) {
                            $skos_concepts_list->add_concept(new skos_concept(0, $uri));
                        }
                    }
                }
                
                return $skos_concepts_list;
                
            case 'schemes':
                $schemes = array();
                $length = count($values);
                for ($i = 0; $i < $length; $i ++) {
                    $query = "SELECT value FROM skos_fields_global_index WHERE code_champ = 100 AND code_ss_champ = 1 AND id_item = ".$values[$i]['value'];
                    $result = pmb_mysql_query($query);
                    if (pmb_mysql_num_rows($result)) {
                        $row = pmb_mysql_fetch_assoc($result);
                        $schemes[$values[$i]['value']] = $row['value'];
                    }
                }
                return $schemes;
            default:
                $results = array();
                $length = count($values);
                for ($i = 0; $i < $length; $i ++) {
                    $value = $values[$i]['value'] ?? "";
                    if (empty($value)) {
                        continue;
                    }
                    
                    switch ($values[$i]['type']) {
                        case "literal":
                            $value = '"' . addslashes($value) . '"';
                            if (array_key_exists('lang', $values[$i])) {
                                $lang = $values[$i]['lang'] ?? "no";
                                $value .= "@".$lang;
                            }
                            break;
                            
                        case "uri":
                            if (in_array($property_name, self::IS_CHILDREN)) {                                
                                $entity_data = $this->add_children_concept($value);
                                $identifier = intval($entity_data['id']) ?? 0;
                            } else {
                                $result = $this->store->get_property($value, 'pmb:identifier');
                                $identifier = intval($result[0]['value']) ?? 0;
                            }
                            
                            if ($identifier != 0) {
                                $uri = onto_common_uri::get_uri($identifier);
                                if (!empty($uri)) {
                                    $value = $uri;
                                }
                            }
                                                          
                            if (substr($value, 0) != "<") {
                                $value = "<".$value;
                            }
                            if (substr($value, -1) != ">") {
                                $value .= ">";
                            }
                            break;
                    }
                    
                    $results[] = $value;
                }
                return $results;
        }
    }
    
    /**
     * @param string $predicate
     * @param array $values
     */
    protected function insert_field($predicate, $values)
    {        
        $this->skos_concept->set_infos($predicate, $this->format_data($values, $predicate));
    }
    
    /**
     * @param string $uri URI de l'entité dans le store de contribution
     * @return array
     */
    protected function add_children_concept($uri) 
    {
        $rdf_entities_integrator_concept = new rdf_entities_integrator_concept($this->store);
        $rdf_entities_integrator_concept->subform = true;
        $entity_data = $rdf_entities_integrator_concept->integrate_itself($uri);
        $this->entity_data['children'][] = $entity_data;
        return $entity_data;
    }
    
    /**
     * @param string $property_name
     * @param array $values
     */
    protected function set_property($property_name, $values)
    {
        $this->skos_concept->{$property_name} = $this->format_data($values, $property_name);
    }
    
    /**
     * @return int Identifiant de l'entité
     */
    protected function execute_base_query() {
        $this->integration_type = static::INTEGRATION_TYPE_UPDATE;
        
        if (empty($this->entity_id)) {
            $this->integration_type = static::INTEGRATION_TYPE_INSERT;
            $this->uri = onto_common_uri::get_new_uri($this->skos_concept->get_concept_base_uri());
            $this->skos_concept->uri = $this->uri;
            $this->entity_id = onto_common_uri::get_id($this->uri);
        }
        
        $this->skos_concept->id = $this->entity_id;
        
        if ($this->integration_type == static::INTEGRATION_TYPE_UPDATE) {
            $this->uri = $this->skos_concept->get_uri();
        }
        
        return $this->entity_id;
    }
    
    /**
     * @param string $values
     */
    protected function post_create($uri)
    {
        // On sauvegarde le concept
        $this->skos_concept->save_from_contribution();
        
        if ($this->integration_type && $this->entity_id) {
            // Audit
            $query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
            $query .= 'values ("' . AUDIT_CONCEPT . '", "' . $this->entity_id . '", "' . $this->contributor_id . '", "' . $this->integration_type . '", "' . $this->create_audit_comment($uri) . '", "' . $this->contributor_type . '")';
            pmb_mysql_query($query);
        }
    }
}