<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_entities_ui.class.php,v 1.1.2.6 2024/10/17 08:37:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_entities_ui extends list_ui {
	
    protected $table_authorities = [];
    
	protected function fetch_data() {
		$this->objects = array();
		$this->_init_entities();
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	protected function get_number_from_query($query) {
	    if ($query) {
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            return pmb_mysql_result($result,0,0);
	        }
	    }
	    return 0;
	}
	
	protected function get_numbers_from_query($query) {
	    $all_results = [];
	    if ($query) {
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            while($row = pmb_mysql_fetch_array($result)){
	                $all_results[$row[1]] = $row[0];
	            }
	        }
	    }
	    return $all_results;
	}
	
	public function add_entity($name, $type=0) {
	    global $msg;
	    
        $entity = new stdClass();
        $entity->name = $name;
        $entity->type = $type;
        $entity->entity_label = $msg[$name];
        $this->add_object($entity);
	}
	
	public function add_authperso_entity($id, $name) {
	    global $msg;
	    
	    $entity = new stdClass();
	    $entity->name = 'authperso';
	    $entity->type = TYPE_AUTHPERSO;
	    $entity->entity_label = $msg['authperso'].' : '.$name;
	    $entity->id_authperso = $id;
	    $this->add_object($entity);
	}
	
	protected function _init_entities() {
	    $this->add_entity('notices', TYPE_NOTICE);
	    $this->add_entity('authors', TYPE_AUTHOR);
	    $this->add_entity('categories', TYPE_CATEGORY);
	    $this->add_entity('publishers', TYPE_PUBLISHER);
	    $this->add_entity('collections', TYPE_COLLECTION);
	    $this->add_entity('subcollections', TYPE_SUBCOLLECTION);
	    $this->add_entity('series', TYPE_SERIE);
	    $this->add_entity('titres_uniformes', TYPE_TITRE_UNIFORME);
	    $this->add_entity('indexint', TYPE_INDEXINT);
	    $this->add_entity('concepts', TYPE_CONCEPT);
	    
	    $query = 'select id_authperso, authperso_name from authperso';
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        while($row = pmb_mysql_fetch_object($result)){
	            $this->add_authperso_entity($row->id_authperso, $row->authperso_name);
	        }
	    }
	}
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters['main_fields'] = array();
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
				        'entity_label' => 'indexation_entity_type',
    				    'table_base' => 'indexation_table_base',
    				    'table_authorities' => 'indexation_table_authorities',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	
	protected function init_default_columns() {
	    $this->add_column('entity_label');
	    $this->add_column('table_base');
	    $this->add_column('table_authorities');
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('entity_label');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('entity_label', 'align', 'left');
	}
	
	protected function _get_object_property_table_base($object) {
	    if(!isset($object->table_base)) {
	        switch($object->name){
	            case 'concepts' :
	                $object->table_base = $this->getNbConcept();
	                break;
	            case 'authperso':
	                $object->table_base = $this->get_number_from_query(entities::get_query_count($object->name, $object->id_authperso));
	                break;
	            default:
	                $object->table_base = $this->get_number_from_query(entities::get_query_count($object->name));
	                break;
	        }
	    }
	    return $object->table_base;
	}
	
	protected function _get_query_table_authorities($object) {
	    switch($object->name){
	        case 'notices' :
	            return '';
	        case 'authors' :
	        case 'publishers' :
	        case 'collections' :
	        case 'subcollections' :
	        case 'series' :
	        case 'titres_uniformes' :
	        case 'indexint' :
	            $type_table = authority::$type_table[$object->type];
	            return 'select count(distinct(id_authority)) as nb from authorities where type_object = '.$type_table;
	        case 'categories':
	            $type_table = authority::$type_table[$object->type];
	            return 'select count(distinct(id_authority)) as nb from authorities join noeuds on noeuds.id_noeud = authorities.num_object and autorite != "TOP" where type_object = '.$type_table;
	        case 'authperso':
	            return 'select count(distinct(id_authority)) as nb from authorities join authperso_authorities on id_authperso_authority = num_object where type_object = 9 and authperso_authority_authperso_num = '.$object->id_authperso;
	        case 'concepts' :
	            return '';
	    }
	}
	
	protected function _init_table_authorities() {
	    if (empty($this->table_authorities)) {
	        $query = 'select count(distinct(id_authority)) as nb, type_object from authorities where type_object != 9 group by type_object';
	        $this->table_authorities = $this->get_numbers_from_query($query);
	    }
	}
	
	protected function _get_object_property_table_authorities($object) {
	    if(!isset($object->table_authorities)) {
	        $this->_init_table_authorities();
	        $type_table = authority::$type_table[$object->type] ?? '';
	        if($type_table != AUT_TABLE_CATEG && isset($this->table_authorities[$type_table])) {
	            $object->table_authorities = $this->table_authorities[$type_table];
	        } else {
	            //On passe ici pour les categories pour ne pas sortir les TOP
	            $object->table_authorities = $this->get_number_from_query($this->_get_query_table_authorities($object));
	        }
	    }
	    return $object->table_authorities;
	}
	
	protected function getNbConcept() {
	    global $base_path;
	    
	    $onto_store_config = array(
	        /* db */
	        'db_name' => DATA_BASE,
	        'db_user' => USER_NAME,
	        'db_pwd' => USER_PASS,
	        'db_host' => SQL_SERVER,
	        /* store */
	        'store_name' => 'ontology',
	        /* stop after 100 errors */
	        'max_errors' => 100,
	        'store_strip_mb_comp_str' => 0,
	        'cache_enabled' => true
	    );
	    $data_store_config = array(
	        /* db */
	        'db_name' => DATA_BASE,
	        'db_user' => USER_NAME,
	        'db_pwd' => USER_PASS,
	        'db_host' => SQL_SERVER,
	        /* store */
	        'store_name' => 'rdfstore',
	        /* stop after 100 errors */
	        'max_errors' => 100,
	        'store_strip_mb_comp_str' => 0,
	        
	        'cache_enabled' => true
	    );
	    
	    $tab_namespaces=array(
	        "skos"	=> "http://www.w3.org/2004/02/skos/core#",
	        "dc"	=> "http://purl.org/dc/elements/1.1",
	        "dct"	=> "http://purl.org/dc/terms/",
	        "owl"	=> "http://www.w3.org/2002/07/owl#",
	        "rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
	        "rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
	        "xsd"	=> "http://www.w3.org/2001/XMLSchema#",
	        "pmb"	=> "http://www.pmbservices.fr/ontology#"
	    );
	    
	    $onto_index = onto_index::get_instance('skos');
	    $onto_index->load_handler($base_path."/classes/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel');
	    $onto_index->init();
	    $query = "select ?item where {
    		?item <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .
    		?item rdf:type ?type .
    		filter(";
    	    $i=0;
    	    foreach($onto_index->infos as $uri => $infos){
    	        if($i) $query.=" || ";
    	        $query.= "?type=<".$uri.">";
    	        $i++;
    	    }
    	    $query.=")
    	} group by ?item";
	    $onto_index->handler->data_query($query);
	    $count = $onto_index->handler->data_num_rows();
	    return $count;
    }
}