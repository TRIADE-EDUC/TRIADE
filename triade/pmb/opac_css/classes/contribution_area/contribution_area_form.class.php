<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_form.class.php,v 1.35 2024/03/14 14:43:36 tsamson Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
    die("no access");
    
    // require_once ($include_path . '/templates/contribution_area/contribution_area.tpl.php');
    require_once ($class_path . '/contribution_area/contribution_area.class.php');
    require_once ($class_path . '/contribution_area/contribution_area_store.class.php');
    require_once ($class_path . '/rdf/ontology.class.php');
    require_once ($class_path . '/onto/onto_parametres_perso.class.php');
    require_once ($class_path . '/onto/onto_store_arc2_extended.class.php');
    require_once ($include_path.'/templates/contribution_area/contibution_area_no_form_available.tpl.php');
    require_once ($class_path . '/contribution_area/contribution_area_scenario.class.php');
    
    /**
     * class contribution_area_form
     * Représente un formulaire
     */
    class contribution_area_form {
        protected $id=0;
        protected $type = "";
        protected $uri = "" ;
        protected $availableProperties = array();
        protected $name="";
        protected $comment="";
        protected $parameters;
        protected $unserialized_parameters;
        protected $classname = "";
        protected $active_properties;
        protected $area_id;
        protected $form_uri;
        protected static $editing_start_scenarios = null;
        protected static $editing_start_equations = array();
        protected static $form_ids = array();
        
        /**
         * Formulaires liés à celui-ci
         * @var array
         */
        protected $linked_forms;
        
        /**
         * scenarios liés à celui-ci
         * @var array
         */
        protected $linked_scenarios;
        
        static protected $contribution_area_form = array();
        
        public function __construct($type, $id=0, $area_id = 0, $form_uri = '')	{
            $this->id =  intval($id);
            $this->type = $type;
            $this->area_id =  intval($area_id);
            if ($form_uri) {
                $this->form_uri = $form_uri;
            }
            $this->fetch_data();
        }
        
        public static function get_contribution_area_form($type, $id=0, $area_id = 0, $form_uri = '') {
            if (!isset(self::$contribution_area_form[$type])) {
                self::$contribution_area_form[$type] = array();
            }
            $key = '';
            $id = intval($id);
            if ($id) {
                $key = $id;
                $key.= ($area_id ? '_'.$area_id : '');
            }
            if (!$key) {
                return new contribution_area_form($type, $id, $area_id, $form_uri);
            }
            if (!isset(self::$contribution_area_form[$type][$key])) {
                self::$contribution_area_form[$type][$key] = new contribution_area_form($type, $id, $area_id, $form_uri);
            }
            return self::$contribution_area_form[$type][$key];
        }
        
        protected function fetch_data()	{
            global $msg;
            if($this->id){
                $query = 'select * from contribution_area_forms where id_form = "'.$this->id.'"';
                $result = pmb_mysql_query($query);
                if(pmb_mysql_num_rows($result)){
                    $params = pmb_mysql_fetch_object($result);
                    $this->parameters = $params->form_parameters;
                    $this->unserialized_parameters = json_decode($this->parameters);
                    $this->name = $params->form_title;
                    $this->comment = $params->form_comment;
                    $this->type = $params->form_type;
                }
            }
            $contribution_area_store = new contribution_area_store();
            $onto = $contribution_area_store->get_ontology();
            $classes = $onto->get_classes();
            foreach($classes as $class){
                if($class->pmb_name == $this->type){
                    $this->uri = $class->uri;
                    $properties = $onto->get_class_properties($this->uri);
                    for($i=0 ; $i<count($properties) ; $i++){
                        $property = $onto->get_property($this->uri, $properties[$i]);
                        if (!$property->is_undisplayed()) {
                            $this->availableProperties[$property->pmb_name] = $property;
                        }
                    }
                    if (is_array($class->sub_class_of)) {
                        foreach($class->sub_class_of as $parent_uri) {
                            $properties = $onto->get_class_properties($parent_uri);
                            for($i=0 ; $i<count($properties) ; $i++){
                                $property = $onto->get_property($parent_uri, $properties[$i]);
                                $this->availableProperties[$property->pmb_name] = $property;
                            }
                        }
                    }
                    break;
                }
            }
            
            $ontology = $contribution_area_store->get_ontology();
            $classes_array = $ontology->get_classes_uri();
            
            
            $classname = "";
            if (!empty($classes_array[$this->uri]) && !empty($classes_array[$this->uri]->name)) {
                $classname = $classes_array[$this->uri]->name;
                if (substr($classname,0,4) == "msg:") {
                    if (isset($msg[substr($classname,4)])) {
                        $classname = $msg[substr($classname,4)];
                    } else {
                        // si on trouve pas le message on met juste le code dans le label
                        $classname = substr($classname,4);
                    }
                }
            }
            
            $this->classname = $classname;
        }
        
        protected function get_saved_property($property)
        {
            if(isset($this->unserialized_parameters->$property)){
                return $this->unserialized_parameters->$property;
            }
            return '';
        }
        
        public function get_active_properties() {
            if (isset($this->active_properties)) {
                return $this->active_properties;
            }
            $this->active_properties = array();
            if ($this->unserialized_parameters) {
                foreach($this->unserialized_parameters as $key => $param){
                    if (isset($this->availableProperties[$key])) {
                        $uri = $this->availableProperties[$key]->uri;
                        $this->active_properties[$uri] = new stdClass();
                        $this->active_properties[$uri] = $this->unserialized_parameters->$key;
                        
                        $tab_default_value = $this->unserialized_parameters->$key->default_value;
                        //on uniformise toutes les valeurs sous forme de tableau
                        $this->active_properties[$uri]->default_value = array();
                        if (is_array($tab_default_value)) {
                            for ($j = 0; $j < count($tab_default_value); $j++) {
                                if (isset($tab_default_value[$j]->value) && !is_array($tab_default_value[$j]->value)) {
                                    $tab_default_value[$j]->value = array($tab_default_value[$j]->value);
                                }
                            }
                            $this->active_properties[$uri]->default_value = $tab_default_value;
                        }
                    }
                }
            }
            return $this->active_properties;
        }
        
        
        public function render() {
            global $base_path, $class_path, $nb_per_page, $action, $sub, $lvl;
            global $area_id, $form_id, $form_uri;
            
            // On redéfinit les globales
            // Elles sont utilisées plus loin
            $action = 'edit';
            $sub = $this->type;
            $lvl = 'contribution_area';
            $area_id = $this->area_id;
            $form_id = $this->id;
            $form_uri = $this->form_uri;

            $onto_store_config = array(
                /* db */
                'db_name' => DATA_BASE,
                'db_user' => USER_NAME,
                'db_pwd' => USER_PASS,
                'db_host' => SQL_SERVER,
                /* store */
                'store_name' => 'onto_contribution_form_' . $this->id,
                /* stop after 100 errors */
                'max_errors' => 100,
                'store_strip_mb_comp_str' => 0,
                'params' => $this->get_active_properties()
            );
            
            $params = new onto_param(array(
                'base_resource' => 'index.php',
                'lvl' => 'contribution_area',
                'sub' => $this->type,
                'action' => 'edit',
                'page' => '1',
                'nb_per_page' => (isset($nb_per_page) ? $nb_per_page : 20),
                'id' => '0',
                'area_id' => $this->area_id,
                'parent_id' => '',
                'form_id' => $this->id,
                'form_uri' => $this->form_uri,
                'item_uri' => '',
            ));
            
            $onto_store = new onto_store_arc2_extended($onto_store_config);
            $onto_store->set_namespaces(contribution_area_store::CONTRIBUTION_NAMESPACE);
            
            //chargement de l'ontologie dans son store
            $reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
            onto_parametres_perso::load_in_store($onto_store, $reset);
            $onto_ui = new onto_ui("", $onto_store, array(), "arc2", contribution_area_store::DATASTORE_CONFIG, contribution_area_store::CONTRIBUTION_NAMESPACE,'http://www.w3.org/2000/01/rdf-schema#label',$params);
            
            return $onto_ui->proceed();
        }
        
        public function get_name() {
            return $this->name;
        }
        
        public function get_comment() {
            return $this->comment;
        }
        
        public function get_type() {
            return $this->type;
        }
        
        public function get_linked_forms () {
            if (isset($this->linked_forms)) {
                return $this->linked_forms;
            }
            $contribution_area_store  = new contribution_area_store();
            $complete_form_uri = $contribution_area_store->get_uri_from_id($this->form_uri);
            $graph_store_datas = $contribution_area_store->get_attachment_detail($complete_form_uri, 'http://www.pmbservices.fr/ca/Area#'.$this->area_id,'','',1);
            $this->linked_forms = array();
            $this->linked_scenarios = [];
            for ($i = 0 ; $i < count($graph_store_datas); $i++) {
                if ($graph_store_datas[$i]['type'] == "form") {
                    $graph_store_datas[$i]['area_id'] = $this->area_id;
                    $this->linked_forms[] = $graph_store_datas[$i];
                } else {
                    $data_form = $contribution_area_store->get_attachment_detail($graph_store_datas[$i]['uri'], 'http://www.pmbservices.fr/ca/Area#'.$this->area_id,'','',1);
                    if ($graph_store_datas[$i]['type'] == "scenario") {
                        if (!isset($this->linked_scenarios[$graph_store_datas[$i]['id']])) {
                            $this->linked_scenarios[$graph_store_datas[$i]['id']] = [];
                        }
                        $this->linked_scenarios[$graph_store_datas[$i]['id']] = [
                            'propertyPmbName' => $graph_store_datas[$i]['propertyPmbName'],
                            'scenarioUri' => $graph_store_datas[$i]['uri'],
                            'attachmentId' => $graph_store_datas[$i]['attachmentId'] ?? 0,
                            'area_id' => $this->area_id,
                        ];
                    }
                    for ($j = 0 ; $j < count($data_form); $j++) {
                        if ($data_form[$j]['type'] == "form") {
                            $data_form[$j]['area_id'] = $this->area_id;
                            $data_form[$j]['propertyPmbName'] = $graph_store_datas[$i]['propertyPmbName'];
                            if ($graph_store_datas[$i]['type'] == "scenario") {
                                $data_form[$j]['scenarioUri'] = $graph_store_datas[$i]['uri'];
                                $data_form[$j]['scenarioId'] = $graph_store_datas[$i]['id'];
                            }
                            if (!empty($graph_store_datas[$i]['attachmentId'])) {
                                $data_form[$j]['attachmentId'] = $graph_store_datas[$i]['attachmentId'];
                            }
                            $this->linked_forms[] = $data_form[$j];
                        }
                    }
                }
            }
            return $this->linked_forms;
        }
        
        public function get_linked_scenarios() {
            if (!empty($this->linked_scenarios)) {
                return $this->linked_scenarios;
            }
            $contribution_area_store  = new contribution_area_store();
            $complete_form_uri = $contribution_area_store->get_uri_from_id($this->form_uri);
            $graph_store_datas = $contribution_area_store->get_attachment_detail($complete_form_uri, 'http://www.pmbservices.fr/ca/Area#'.$this->area_id,'','',1);
            $this->linked_scenarios = [];
            for ($i = 0 ; $i < count($graph_store_datas); $i++) {
                if ($graph_store_datas[$i]['type'] == "scenario") {
                    if (!isset($this->linked_scenarios[$graph_store_datas[$i]['id']])) {
                        $this->linked_scenarios[$graph_store_datas[$i]['id']] = [];
                    }
                    $this->linked_scenarios[$graph_store_datas[$i]['id']] = [
                        'propertyPmbName' => $graph_store_datas[$i]['propertyPmbName'],
                        'scenarioUri' => $graph_store_datas[$i]['uri'],
                        'attachmentId' => $graph_store_datas[$i]['attachmentId'] ?? 0,
                        'area_id' => $this->area_id,
                    ];
                }
            }
            return $this->linked_scenarios;
        }
        
        
        static public function get_editing_start_scenarios() {
            global $area_id;
            
            if (self::$editing_start_scenarios !== null) {
                return self::$editing_start_scenarios;
            }
            
            // On récupère l'espace de contribution pour la modification d'entité
            $area_id = contribution_area::get_editing_entity_area_id();
            self::$editing_start_scenarios = [];
            
            if (!empty($area_id)) {
                $area = new contribution_area($area_id);
                self::$editing_start_scenarios = $area->get_start_scenarios();
            }
            return self::$editing_start_scenarios;
        }
        
        /**
         * Retourn l'id du formulaire du scenario de départ en fonction du type
         *
         * @param string $type
         * @param int $entity_id
         * @return number
         */
        static public function get_default_form_id_by_type(string $type = "record", int $entity_id = 0)
        {
            global $scenario_uri, $form_uri;
            
            $scenarios_start = self::get_editing_start_scenarios();
            
            if (!empty($scenarios_start)) {
                foreach ($scenarios_start as $key => $scenario){
                    
                    if ($scenario['entityType'] == $type && $type == "docnum") {
                        $scenario_uri = $scenario['uri'];
                        break;
                    }
                    
                    if ($scenario['entityType'] === $type && !isset($scenario_uri)) {
                        $scenario_uri = $scenario['uri'];
                    }
                    
                    if ($type != "docnum") {
                        $search_field = "search_fields";
                        $field = 'notice_id';
                        $field_value = $entity_id;
                        
                        if ("bulletin" == $type) {
                            if (!empty($entity_id)) {
                                $query = "SELECT num_notice FROM bulletins WHERE bulletin_id = $entity_id";
                                $result = pmb_mysql_query($query);
                                if (pmb_mysql_num_rows($result)) {
                                    $field_value = pmb_mysql_result($result, 0, 0);
                                }
                            }
                        }else if ("record" != $type) {
                            $search_field = "search_fields_authorities";
                            $field = 'id_authority';
                            $type_id = self::get_const_type_object($type);
                            if (!empty($entity_id) && !empty($type_id)) {
                                $query = "SELECT id_authority FROM authorities WHERE num_object = $entity_id AND type_object = $type_id";
                                $result = pmb_mysql_query($query);
                                if (pmb_mysql_num_rows($result)) {
                                    $field_value = pmb_mysql_result($result, 0, 0);
                                }
                            }
                        }
                        if(empty(self::$editing_start_equations[$scenario['id']])){
                            $area_scenario = new contribution_area_scenario($scenario["id"]);
                            self::$editing_start_equations[$scenario['id']]['eq'] = $area_scenario->get_equation_query();
                            
                            $sc = new search($search_field);
                            $sc->unserialize_search(self::$editing_start_equations[$scenario['id']]['eq']);
                            $table_tempo = $sc->make_search("editing_start_equations" . $key);
                            self::$editing_start_equations[$scenario['id']]['table'] = $table_tempo;
                        }
                        if (!empty(self::$editing_start_equations[$scenario['id']]['eq'])) {
                            $query = "SELECT * FROM ". self::$editing_start_equations[$scenario['id']]['table'] ." WHERE $field = $field_value";
                            $result = pmb_mysql_query($query);
                            if ($scenario['entityType'] === $type && pmb_mysql_num_rows($result)) {
                                $scenario_uri = $scenario['uri'];
                                break;
                            }
                        }
                    }
                }
            }
            
            // On récupère le formulaire que l'on vas afficher
            if (!empty($scenario_uri)) {
                if (!empty( self::$form_ids[$scenario_uri])) {
                    return  self::$form_ids[$scenario_uri];
                }
                
                $graphstore = new contribution_area_store();
                $query = "SELECT ?form_uri ?form_id WHERE {
                      ?attachement_uri ca:attachmentSource <$scenario_uri> .
                      ?attachement_uri ca:attachmentDest ?dest_uri .
                      ?dest_uri ca:eltId ?form_id .
                      ?dest_uri ca:identifier ?form_uri .
                }";
                $success = $graphstore->get_graphstore()->query($query);
                if ($success) {
                    $results = $graphstore->get_graphstore()->get_result();
                    if (!empty($results)) {
                        $form_uri = $results[0]->form_uri;
                        self::$form_ids[$scenario_uri] = $results[0]->form_id;
                        return self::$form_ids[$scenario_uri];
                    } else {
                        return -1;
                    }
                }
            }
            return 0;
        }
        
        static public function get_form_entity_convert()
        {
            global $nb_per_page, $class_path, $lvl_redirect;
            global $entity_type, $entity_id, $id, $sub, $msg;
            global $sub_form, $scenario_uri, $area_id, $form_uri;
            global $form_id, $sub_tab, $pmb_entity_locked_time;
            global $entity_no_scenario_available, $entity_no_form_available, $entity;
            
            if (!$sub_tab) {
                // On ne vas pas rechercher un formulaire car pour les sous-formulaire
                // on dispose déjà des informations
                $form_id = self::get_default_form_id_by_type($entity_type, $entity_id);
            } else {
                
                $edit_entity = false;
                if (!empty($entity) && $entity) {
                    // on modifie une entité du fonds
                    $edit_entity = true;
                }
                
                $contribution_area_scenario = new contribution_area_scenario($scenario_uri, $area_id);
                $scenario_forms = $contribution_area_scenario->get_forms($edit_entity, $entity_id);
                
                if (count($scenario_forms) > 1 && 'convert' == $sub) {
                    print $contribution_area_scenario->sub_render();
                    return ;
                }
            }
            
            if (0==$form_id) {
                return $entity_no_scenario_available;
            } elseif (-1==$form_id){
                return $entity_no_form_available;
            }
            if (empty($entity_id) || empty($entity_type) || empty($form_id)) {
                $template = $msg['empr_contribution_area_unauthorized'];
                return $template;
            }
            
            if (!self::compute_access_rights()) {
                return false;
            }
            
            //Vérification si la contribution n'est pas modifié en gestion ou par un autre utilisateur
            if($entity_id && $pmb_entity_locked_time){
                $entity_locking = new entity_locking($entity_id, contribution_area_forms_controller::get_entity_const($entity_type));
                if($entity_locking->is_locked()){
                    return $entity_locking->get_locked_form();
                } else {
                    $entity_locking->lock_entity();
                }
            }
            
            $id = $entity_id;
            $sub = $entity_type;
            $params = new onto_param(array(
                'base_resource' => 'index.php',
                'lvl' => 'contribution_area',
                'sub' => $entity_type,
                'type' => $entity_type,
                'action' => 'edit_entity',
                'page' => '1',
                'nb_per_page' => (isset($nb_per_page) ? $nb_per_page : 20),
                'id' => $entity_id,
                'area_id' => $area_id,
                'parent_id' => '',
                'form_id' => $form_id,
                'form_uri' => $form_uri,
                'item_uri' => '',
            ));
            
            $form =  contribution_area_form::get_contribution_area_form($params->sub,$params->form_id,$params->area_id,$params->form_uri);
            $onto_store = contribution_area_store::get_formstore($form_id, $form->get_active_properties());
            
            // Ajouts des parametres perso dans le fichier ontologies_pmb_entities
            $reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
            onto_parametres_perso::load_in_store($onto_store, $reset);
            
            // On fait le rendu du formulaire
            $onto_ui = new onto_ui("", $onto_store, array(), "arc2", contribution_area_store::DATASTORE_CONFIG, contribution_area_store::CONTRIBUTION_NAMESPACE,'http://www.w3.org/2000/01/rdf-schema#label',$params);
            return $onto_ui->proceed();
        }
        
        public static function compute_access_rights() {
            // Droit d'acces
            global $gestion_acces_active, $gestion_acces_empr_contribution_area, $gestion_acces_empr_contribution_scenario;
            global $area_id, $scenario_uri;
            
            // Si on a l'identifier du scenario on vas chercher son uri
            if (is_numeric($scenario_uri)) {
                $contribution_area_store = new contribution_area_store();
                $scenario_uri = $contribution_area_store->get_uri_from_id($scenario_uri);
            }
            
            if (($gestion_acces_active == 1) && (($gestion_acces_empr_contribution_area == 1) || ($gestion_acces_empr_contribution_scenario == 1))) {
                $ac = new acces();
                if ($gestion_acces_empr_contribution_area == 1) {
                    $dom_4 = $ac->setDomain(4);
                }
                if ($gestion_acces_empr_contribution_scenario == 1) {
                    $dom_5 = $ac->setDomain(5);
                }
            }
            
            if (isset($dom_4) && !$dom_4->getRights($_SESSION['id_empr_session'], $area_id, 4)) {
                print $msg['empr_contribution_area_unauthorized'];
                return false;
            }
            
            if (isset($dom_5) && !$dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id($scenario_uri), 4)) {
                print $msg['empr_contribution_area_unauthorized'];
                return false;
            }
            return true;
        }
        
        public static function get_const_type_object($string_type_object) {
            switch ($string_type_object) {
                case  'author':
                case  'authors':
                    return AUT_TABLE_AUTHORS;
                case 'category':
                case 'categories':
                    return AUT_TABLE_CATEG;
                case 'publisher' :
                case 'publishers' :
                    return AUT_TABLE_PUBLISHERS;
                case 'collection' :
                case 'collections' :
                    return AUT_TABLE_COLLECTIONS;
                case 'subcollection' :
                case 'subcollections' :
                    return AUT_TABLE_SUB_COLLECTIONS;
                case 'serie':
                case 'series':
                    return AUT_TABLE_SERIES;
                case 'titre_uniforme' :
                case 'work' :
                case 'works' :
                    return AUT_TABLE_TITRES_UNIFORMES;
                case 'indexint' :
                    return AUT_TABLE_INDEXINT;
                case 'concept' :
                case 'concepts' :
                    return AUT_TABLE_CONCEPT;
                default :
                    if (strpos($string_type_object, "authperso") !== false) {
                        return AUT_TABLE_AUTHPERSO;
                    }
            }
        }
    }
    