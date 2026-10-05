<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ontology.class.php,v 1.4 2023/02/07 15:31:40 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . "/storages/storages.class.php");

class ontology
{

    protected $id = 0;

    protected $name = "";

    protected $description = "";

    protected $storage_id = 0;

    protected $pmb_name = "";

    protected $creation_date;

    protected $ontopmbstore_params = array(/* db */
		'db_name' => DATA_BASE,
        'db_user' => USER_NAME,
        'db_pwd' => USER_PASS,
        'db_host' => SQL_SERVER,
		/* store */
		'store_name' => 'ontology_pmb',
		/* stop after 100 errors */
		'max_errors' => 100,
        'store_strip_mb_comp_str' => 0
    );

    protected $ontostore_params = array();

    protected $datastore_params = array();

    protected $namespaces = array(
        "dc" => "http://purl.org/dc/elements/1.1",
        "dct" => "http://purl.org/dc/terms/",
        "owl" => "http://www.w3.org/2002/07/owl#",
        "rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
        "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
        "xsd" => "http://www.w3.org/2001/XMLSchema#",
        "pmb" => "http://www.pmbservices.fr/ontology#",
        "pmb_onto" => "http://www.pmbservices.fr/ontology_description#"
    );

    protected $ontopmbnamespaces = array(
        "dc" => "http://purl.org/dc/elements/1.1",
        "dct" => "http://purl.org/dc/terms/",
        "owl" => "http://www.w3.org/2002/07/owl#",
        "rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
        "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
        "xsd" => "http://www.w3.org/2001/XMLSchema#",
        "pmb" => "http://www.pmbservices.fr/ontology#",
        "pmb_onto" => "http://www.pmbservices.fr/ontology_description#"
    );

    protected $ontology_base_uri = "";

    protected $handler = null;

    public function __construct($id = 0)
    {
        $this->id = intval($id);
        $this->fetch_datas();
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_base_uri()
    {
        return $this->ontology_base_uri;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_handler()
    {
        if ($this->handler !== null) {
            return $this->handler;
        }
        $this->handler = new onto_handler(null, "arc2", $this->ontostore_params, "arc2", $this->datastore_params, $this->namespaces, 'http://www.w3.org/2000/01/rdf-schema#label');
        return $this->handler;
    }

    private function fetch_datas()
    {
        global $opac_url_base;
        if ($this->id) {
            $query = "select id_ontology, ontology_name, ontology_description, ontology_creation_date, ontology_storage_id, ontology_pmb_name from ontologies where id_ontology = " . $this->id;
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_object($result);
                $this->id = $row->id_ontology;
                $this->name = $row->ontology_name;
                $this->description = $row->ontology_description;
                $this->creation_date = DateTime::createFromFormat("Y-m-d H:i:s", $row->ontology_creation_date);
                $this->storage_id = $row->ontology_storage_id;
                $this->pmb_name = $row->ontology_pmb_name;
            }
            $this->ontostore_params = array(
				/* db */
				'db_name' => DATA_BASE,
                'db_user' => USER_NAME,
                'db_pwd' => USER_PASS,
                'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'ontology_' . $this->id,
				/* stop after 100 errors */
				'max_errors' => 100,
                'store_strip_mb_comp_str' => 0
            );
            $this->datastore_params = array(
				/* db */
				'db_name' => DATA_BASE,
                'db_user' => USER_NAME,
                'db_pwd' => USER_PASS,
                'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'rdfstore',
				/* stop after 100 errors */
				'max_errors' => 100,
                'store_strip_mb_comp_str' => 0
            );
            $this->ontology_base_uri = $opac_url_base . "ontologies/" . $this->id . "";
        }
        if (! $this->creation_date) {
            $this->creation_date = new DateTime();
        }
    }

    public function get_form()
    {
        global $msg, $charset, $ontology_form;
        $form = str_replace("!!id!!", htmlentities($this->id, ENT_QUOTES, $charset), $ontology_form);
        $form = str_replace("!!name!!", htmlentities($this->name, ENT_QUOTES, $charset), $form);
        $form = str_replace("!!description!!", htmlentities($this->description, ENT_QUOTES, $charset), $form);
        $storages = new storages();
        $form = str_replace("!!onto_upload_directory!!", $storages->get_item_form($this->storage_id), $form);

        if ($this->id) {
            $form = str_replace("!!form_title!!", htmlentities($msg['ontologies_edit'], ENT_QUOTES, $charset), $form);
            $form = str_replace("!!delete_btn!!", confirmation_delete("./modelling.php?categ=ontologies&sub=general&act=delete&ontology_id=") . "<input class='bouton' type='button' value='$msg[supprimer]' onClick=\"javascript:confirmation_delete(" . $this->id . ",'" . $this->name . "')\" />", $form);
        } else {
            $form = str_replace("!!form_title!!", htmlentities($msg['ontologies_add'], ENT_QUOTES, $charset), $form);
            $form = str_replace("!!delete_btn!!", "", $form);
        }
        return $form;
    }

    public function get_values_from_form()
    {
        global $ontology_id, $ontology_name, $ontology_description, $storage_method;
        $this->id = intval($ontology_id);
        $this->name = stripslashes($ontology_name);
        $this->description = stripslashes($ontology_description);
        $this->storage_id = intval($storage_method);
        // Pour l'instant, ca marche comme ça !
        $this->pmb_name = 'ontology' . $this->id;
    }

    public function save()
    {
        global $opac_url_base;
        if ($this->id) {
            $query = "update ontologies set ";
            $where = " where id_ontology = " . $this->id;
        } else {
            $query = "insert into ontologies set 
					ontology_creation_date = '" . $this->creation_date->format("Y-m-d H:i:s") . "',";
            $where = "";
        }
        $query .= "
			ontology_name = '" . addslashes($this->name) . "',
			ontology_description = '" . addslashes($this->description) . "',
			ontology_storage_id = '" . addslashes($this->storage_id) . "',
		    ontology_pmb_name = '" . addslashes($this->pmb_name) . "'";

        pmb_mysql_query($query . $where);
        if (! $this->id) {
            $this->id = pmb_mysql_insert_id();
            $this->pmb_name = "ontology" . $this->id;
            $query = "update ontologies set ontology_pmb_name = '" . addslashes($this->pmb_name) . "' where id_ontology = " . $this->id;
            pmb_mysql_query($query);
            $this->ontostore_params = array(
				/* db */
				'db_name' => DATA_BASE,
                'db_user' => USER_NAME,
                'db_pwd' => USER_PASS,
                'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'ontology_' . $this->id,
				/* stop after 100 errors */
				'max_errors' => 100,
                'store_strip_mb_comp_str' => 0
            );
            $this->datastore_params = array(
				/* db */
				'db_name' => DATA_BASE,
                'db_user' => USER_NAME,
                'db_pwd' => USER_PASS,
                'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'rdfstore',
				/* stop after 100 errors */
				'max_errors' => 100,
                'store_strip_mb_comp_str' => 0
            );
            $this->ontology_base_uri = $opac_url_base . "ontologies/" . $this->id . "";
            $this->init_indexes_tables();
        }
        $this->update_ontology();
        return $this->id;
    }

    protected function init_indexes_tables()
    {
        // Tables de mots
        $query = "create table if not exists ontology" . $this->id . "_words_global_index(
			id_item int unsigned not null default 0,
			code_champ int unsigned not null default 0,
			code_ss_champ int unsigned not null default 0,
			num_word int unsigned not null default 0,
			pond int unsigned not null default 100,
			position int unsigned not null default 1,
			field_position int unsigned not null default 1,
			primary key (id_item,code_champ,num_word,position,code_ss_champ),
			index code_champ(code_champ),
			index i_id_mot(num_word,id_item),
			index i_code_champ_code_ss_champ_num_word(code_champ,code_ss_champ,num_word))";
        pmb_mysql_query($query);

        // Table de champs
        $query = "create table if not exists ontology" . $this->id . "_fields_global_index(
			id_item int unsigned not null default 0,
			code_champ int unsigned not null default 0,
			code_ss_champ int unsigned not null default 0,
			ordre int(4) unsigned not null default 0,
			value text not null,
			pond int(4) unsigned not null default 100,
			lang varchar(10) not null default '',
			authority_num varchar(50) not null default 0,
			primary key(id_item,code_champ,code_ss_champ,lang,ordre),
			index i_value(value(300)),
			index i_code_champ_code_ss_champ(code_champ,code_ss_champ))";
        pmb_mysql_query($query);
    }

    public function delete()
    {
        // TODO les quelques vérifications qui s'impose...

        // Desctruction du store de données de l'ontologie
        $this->init_ontostore();
        $this->ontostore->drop();
        // TODO, desctruction des données?
        $query = "delete from ontologies where id_ontology=" . $this->id;
        $result = pmb_mysql_query($query);
        if (! $result) {
            return false;
        }
        return true;
    }

    protected function update_ontology()
    {
        global $opac_biblio_name;
        $this->init_ontostore();
        $this->ontostore->set_namespaces($this->ontopmbnamespaces);

        $query = "delete { 
 			<" . $this->ontology_base_uri . "> ?p ?o	
 		}";
        $this->ontostore->query($query, $this->ontopmbnamespaces);
        $query = "insert into <pmb> {
			<" . $this->ontology_base_uri . "> rdf:type owl:Ontology .
			<" . $this->ontology_base_uri . "> pmb:name '" . $this->pmb_name . "' .
			<" . $this->ontology_base_uri . "> dct:title '" . addslashes(clean_string($this->name)) . "' .
			<" . $this->ontology_base_uri . "> dct:creation '" . addslashes(clean_string($opac_biblio_name)) . "' .
			<" . $this->ontology_base_uri . "> dct:modified '" . date("c") . "' .
			<" . $this->ontology_base_uri . "> dct:date '" . $this->creation_date->format("c") . "' .";

        if ($this->description) {
            $query .= "
			<" . $this->ontology_base_uri . "> dct:description '" . addslashes(clean_string($this->description)) . "' .";
        }

        $query .= "
		}";
        if ($this->ontostore->query($query, $this->ontopmbnamespaces)) {} else {
            $errs = $this->ontostore->get_errors();
            print "<br>Erreurs: <br>";
            print "<pre>";
            print_r($errs);
            print "</pre><br>";
            print "flop";
        }
    }

    public function exec_onto_framework($base_resource = "modelling.php")
    {
        global $include_path;
        $params = new onto_param(array(
            'categ' => 'ontologies',
            'sub' => 'class',
            'action' => 'list',
            'page' => '1',
            'nb_per_page' => '20',
            'id' => '',
            'parent_id' => '',
            'user_input' => '',
            'ontology_id' => $this->id,
            'item_uri' => "",
            'onto_name' => $this->name,
            'base_resource' => $base_resource
        ));
        $onto_ui = new onto_ui($include_path . "/ontologies/ontologies_pmb.rdf", "arc2", $this->ontopmbstore_params, "arc2", $this->ontostore_params, $this->ontopmbnamespaces, 'http://www.w3.org/2000/01/rdf-schema#label', $params);
        $onto_ui->proceed();
    }

    public function exec_onto_query($query)
    {
        $store = new onto_store_arc2($this->ontostore_params);
        $store->set_namespaces($this->ontopmbnamespaces);
        $store->query($query);
        if ($store->num_rows() > 0) {
            return $store->get_result();
        }
        return false;
    }

    public function exec_data_query($query)
    {
        $store = new onto_store_arc2($this->datastore_params);
        $store->set_namespaces($this->ontopmbnamespaces);
        $store->query($query);
        if ($store->num_rows() > 0) {
            return $store->get_result();
        }
        return false;
    }

    public function exec_onto_selector_framework($params = array())
    {
        global $include_path;
        if (! $params) {
            $params = new onto_param(array(
                'categ' => 'ontologies',
                'sub' => 'class',
                'action' => 'list',
                'page' => '1',
                'nb_per_page' => '20',
                'id' => '',
                'parent_id' => '',
                'user_input' => '',
                'ontology_id' => $this->id,
                'item_uri' => "",
                'base_resource' => "modelling.php"
            ));
        }
        $onto_ui = new onto_ui($include_path . "/ontologies/ontologies_pmb.rdf", "arc2", $this->ontopmbstore_params, "arc2", $this->ontostore_params, $this->ontopmbnamespaces, 'http://www.w3.org/2000/01/rdf-schema#label', $params);
        $onto_ui->proceed();
    }

    public function exec_data_selector_framework($params = array())
    {
        global $include_path;
        if (! $params) {
            $params = new onto_param(array(
                'categ' => 'ontologies',
                'sub' => 'class',
                'action' => 'list',
                'page' => '1',
                'nb_per_page' => '20',
                'id' => '',
                'parent_id' => '',
                'user_input' => '',
                'ontology_id' => $this->id,
                'item_uri' => "",
                'base_resource' => "semantic.php"
            ));
        }
        $onto_ui = new onto_ui(null, "arc2", $this->ontostore_params, "arc2", $this->datastore_params, $this->namespaces, 'http://www.w3.org/2000/01/rdf-schema#label', $params);
        $onto_ui->proceed();
    }

    public function exec_data_framework($params = array())
    {
        if (! $params) {
            $params = new onto_param(array(
                'categ' => '',
                'sub' => '',
                'action' => 'see',
                'page' => '1',
                'nb_per_page' => '20',
                'id' => '',
                'parent_id' => '',
                'user_input' => '',
                'ontology_id' => $this->id,
                'item_uri' => "",
                'base_resource' => "index.php?lvl=onto_see&ontology_id=" . $this->id
            ));
        }

        $onto_ui = new onto_ui(null, "arc2", $this->ontostore_params, "arc2", $this->datastore_params, $this->namespaces, 'http://www.w3.org/2000/01/rdf-schema#label', $params);
        $onto_ui->proceed();
    }

    public function print_onto_rdf()
    {
        $this->init_ontostore();
        $this->ontostore->set_namespaces($this->ontopmbnamespaces);
        print $this->ontostore->get_RDF(true);
    }

    public function print_datas_rdf()
    {
        $this->init_datastore();
        $this->datastore->set_namespaces($this->namespaces);
        print $this->datastore->get_RDF(true);
    }

    public function get_data_endpoint()
    {
        foreach ($this->namespaces as $key => $uri) {
            $prefix .= "PREFIX " . $key . ": <" . $uri . ">";
        }
        if (isset($_POST['query'])) {
            $_POST['query'] = $prefix . "
			" . $_POST['query'];
        }

        $onto_store_config = $this->datastore_params;
        $onto_store_config['endpoint_features'] = array(
            'select',
            'insert'
        );
        $store = ARC2::getStoreEndpoint($onto_store_config);
        $store->go(true);
    }

    public function get_onto_endpoint()
    {
        foreach ($this->namespaces as $key => $uri) {
            $prefix .= "PREFIX " . $key . ": <" . $uri . ">";
        }
        if (isset($_POST['query'])) {
            $_POST['query'] = $prefix . "
			" . $_POST['query'];
        }

        $onto_store_config = $this->ontostore_params;

        $onto_store_config['endpoint_features'] = array(
            'select'
        );
        $store = ARC2::getStoreEndpoint($onto_store_config);
        $store->go(true);
    }

    public function draw_onto()
    {
        $onto_store_config = $this->ontostore_params;
        $store = ARC2::getStore($onto_store_config);
        /* configuration */
        $config = array(
			/* path to dot */
			'graphviz_path' => 'dot',
			/* tmp dir (default: '/tmp/') */
			'graphviz_temp' => '/tmp/',
			/* pre-defined namespace prefixes (optional) */
			'ns' => $this->namespaces
        );

        /* instantiation */
        $viz = ARC2::getComponent('TriplesVisualizerPlugin', $config);
        foreach ($this->namespaces as $key => $uri) {
            $prefix .= "PREFIX " . $key . ": <" . $uri . ">";
        }
        if (isset($_POST['query'])) {
            $_POST['query'] = $prefix . "
			" . $_POST['query'];
        }
        $query = "SELECT * WHERE {
			?s rdf:type owl:Class .
			?s ?p ?o .
			FILTER(!(?p=<http://www.pmbservices.fr/ontology#displayLabel>)) 
			FILTER(!(?p=<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>)) 	
		}";
        $classes = $store->query($query, 'rows');

        $query = "SELECT * WHERE {
			?s rdf:type owl:ObjectProperty .
			?s ?p ?o .
			FILTER(!(?p=<http://www.pmbservices.fr/ontology#displayLabel>))
			FILTER(!(?p=<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>))
			FILTER(!(?p=<http://www.pmbservices.fr/ontology#datatype>))
		}";
        $properties = $store->query($query, 'rows');
        /* display an svg image */
        $svg = $viz->draw(array_merge($classes, $properties), 'svg', 'raw');
        print $svg;
    }

    public function get_classes()
    {
        $this->init_ontostore();
        $query = "select ?class ?label ?displayLabel {
 			?class rdf:type owl:Class .
			?class rdfs:label ?label .
			?class pmb:displayLabel ?displayLabel
 		}";
        $classes = array();
        if ($this->ontostore->query($query, $this->ontopmbnamespaces)) {
            $results = $this->ontostore->get_result();
            foreach ($results as $result) {
                $classes[$result->class] = $result->label;
            }
        }
        return $classes;
    }

    public function get_display_label_property($uri)
    {
        $this->init_ontostore();
        $query = "select ?displayLabel  {
 			<" . $uri . "> pmb:displayLabel ?displayLabel 
 		}";
        if ($this->ontostore->query($query, $this->ontopmbnamespaces)) {
            $results = $this->ontostore->get_result();
            foreach ($results as $result) {
                return $result->displayLabel;
            }
        }
        return "";
    }

    public function get_classes_for_concepts()
    {
        global $lang;
        $classes = array();
        $this->init_ontostore();
        $query = "select * where {
 			?useInConcept pmb_onto:useInConcept ?prop .
			?useInConcept rdf:type owl:Class .
			?useInConcept rdfs:label ?label .
			?useInConcept pmb:name ?pmbname
 		}";
        if ($this->ontostore->query($query, $this->ontopmbnamespaces)) {
            $results = $this->ontostore->get_result();
            foreach ($results as $result) {
                $found = false;
                for ($i = 0; $i < count($classes); $i ++) {
                    if ($classes[$i]['id'] == onto_common_uri::get_id($result->useInConcept)) {
                        $found = true;
                        if ($result->label_lang == substr($lang, 0, 2)) {
                            $classes[$i]['name'] = $result->label . " (" . $this->get_name() . ")";
                        }
                    }
                }
                if (! $found) {
                    $classes[] = array(
                        'ontology_id' => $this->get_id(),
                        'id' => onto_common_uri::get_id($result->useInConcept),
                        'name' => $result->label . " (" . $this->get_name() . ")",
                        'pmbname' => $result->pmbname
                    );
                }
            }
        }
        return $classes;
    }

    public function get_instance_label($uri)
    {
        $this->init_datastore();

        $query = "select ?type where {
			<" . $uri . "> rdf:type ?type .
		}";
        if ($this->datastore->query($query, $this->ontopmbnamespaces)) {
            $results = $this->datastore->get_result();
            $query = "select ?label where{
				<" . $uri . "> <" . $this->get_display_label_property($results[0]->type) . "> ?label .	
			}";
            if ($this->datastore->query($query, $this->ontopmbnamespaces)) {
                $results = $this->datastore->get_result();
                return $results[0]->label;
            }
        }
    }

    public function get_storage_id()
    {
        return $this->storage_id;
    }

    public function set_storage_id($id)
    {
        $this->storage_id = intval($id);
    }

    private function init_datastore()
    {
        if (! $this->datastore) {
            $this->datastore = new onto_store_arc2($this->datastore_params);
        }
    }

    private function init_ontostore()
    {
        if (! isset($this->ontostore) || ! $this->ontostore) {
            $this->ontostore = new onto_store_arc2($this->ontostore_params);
        }
    }

    public function get_available_segments()
    {
        $segments = [];
        foreach ($this->get_classes() as $uri => $class) {
            $segments[10000 + intval(onto_common_uri::get_id($uri))] = $class;
        }
        return $segments;
    }

    public function get_onto()
    {
        return $this->get_handler()->get_ontology();
    }
}