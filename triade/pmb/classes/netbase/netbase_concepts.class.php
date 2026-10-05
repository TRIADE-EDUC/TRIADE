<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase_concepts.class.php,v 1.1.2.8 2024/11/20 14:20:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/explnum.class.php");

class netbase_concepts {
	
	protected static $indexation_concepts;
	
	protected static $indexation_by_fields = false;
	
	protected static $step_position = 0;
	
	protected static $onto_index;
	
	public static function get_onto_index() {
	    global $base_path;
	    
	    if(!isset(static::$onto_index)) {
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
    	        'store_strip_mb_comp_str' => 0
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
    	        'store_strip_mb_comp_str' => 0
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
    	    $onto_index->set_netbase(true);
    	    static::$onto_index = $onto_index;
	    }
	    return static::$onto_index;
	}
	
	public static function index_from_query($query) {
	    $indexation_concepts = static::get_indexation_concepts();
	    $indexation_concepts->handler->data_query($query);
	    $nb_indexed = $indexation_concepts->handler->data_num_rows();
	    if($nb_indexed) {
	        $indexation_concepts->set_deleted_index(true);
			$results = $indexation_concepts->handler->data_result();
	        foreach($results as $row){
	            $indexation_concepts->maj(0,$row->item);
	        }
	    }
	    return $nb_indexed;
	}
	
	public static function raz_index() {
	    if(static::$indexation_by_fields) {
    	    $indexation_concepts = static::get_indexation_concepts();
    	    $indexation_concepts->raz_fields_table();
    	    $indexation_concepts->raz_words_table();
    	    $indexation_concepts->disable_fields_table_keys();
    	    $indexation_concepts->disable_words_table_keys();
    	    netbase_entities::clean_files($indexation_concepts->get_directory_files());
	    } else {
	        //remise a zero de la table au début
	        pmb_mysql_query("TRUNCATE skos_words_global_index");
	        pmb_mysql_query("ALTER TABLE skos_words_global_index DISABLE KEYS");
	        
	        pmb_mysql_query("TRUNCATE skos_fields_global_index");
	        pmb_mysql_query("ALTER TABLE skos_fields_global_index DISABLE KEYS");
	    }
	}
	
	public static function get_query_base() {
	    $indexation_concepts = static::get_indexation_concepts();
	    $indexation_concepts->init();
	    $query = "select * where {
    		?item <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .
    		?item rdf:type ?type .
    		filter(";
	    $i=0;
	    foreach($indexation_concepts->infos as $uri => $infos){
	        if($i) $query.=" || ";
	        $query.= "?type=<".$uri.">";
	        $i++;
	    }
	    $query.=")
    	}";
	    return $query;
	}
	
	public static function get_index_query_count() {
	    return static::get_query_base();
	}
	
	public static function get_count_index() {
	    $indexation_concepts = static::get_indexation_concepts();
	    $query = static::get_index_query_count();
	    $indexation_concepts->handler->data_query($query);
	    return $indexation_concepts->handler->data_num_rows();
	}
	
	public static function get_lot($count) {
        return REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php
	}
	
	public static function get_index_query($start, $lot) {
	    $start = intval($start);
	    $lot = intval($lot);
	    $query = static::get_query_base();
	    $query.= " order by asc(?label)";
	    if ($lot) {
	       $query.= " limit ".$lot." offset ".$start;
	    }
	    return $query;
	}
	
	public static function get_objects_ids_index($start, $lot) {
	    $objects_ids = [];
	    $indexation_concepts = static::get_indexation_concepts();
	    $query = static::get_index_query($start, $lot);
	    $indexation_concepts->handler->data_query($query);
	    if($indexation_concepts->handler->data_num_rows()) {
	        $results = $indexation_concepts->handler->data_result();
	        foreach($results as $row) {
	            $objects_ids[] = intval(onto_common_uri::get_id($row->item));
	        }
	    }
	    return $objects_ids;
	}
	
	public static function index_from_interval($start, $count) {
	    $lot = static::get_lot($count);
	    if(static::$indexation_by_fields) {
	        $indexation_concepts = static::get_indexation_concepts();
	        $indexation_concepts->set_start($start);
	        $indexation_concepts->set_lot($lot);
	        $indexation_concepts->launch_indexation();
	        if (($start+$lot) < $count) {
	            return ($start+$lot);
	        }
	    } else {
	        $query = static::get_index_query($start, $lot);
	        $nb_indexed = static::index_from_query($query);
	        if($nb_indexed) {
	            return ($start + $lot);
	        }
	    }
	    return 0;
	}
	
	public static function index_from_interface($start, $count) {
	    $next = static::index_from_interval($start, $count);
	    if ($next) {
	        print netbase::get_display_progress($start, $count);
	    } else {
	        print netbase::get_display_final_progress();
	    }
	    return $next;
	}
	
	public static function index_sphinx_from_interval($start, $count) {
	    $lot = static::get_lot($count);
	    $si = new sphinx_concepts_indexer();
	    if(is_object($si)) {
	        $objects_ids = static::get_objects_ids_index($start, $lot);
	        $nb_objects_ids = count($objects_ids);
	        if($nb_objects_ids) {
	            $si->fillIndexes($objects_ids);
	            return ($start + $lot);
	        }
	    }
	    return 0;
	}
	
	public static function index_sphinx_from_interface($start, $count) {
	    if ( empty($count)) {
	        // mise à jour de l'affichage de la jauge
	        print netbase::get_display_final_progress();
	        return 0;
	    }
	    $next = static::index_sphinx_from_interval($start, $count);
	    if($next) {
	        print netbase::get_display_progress($start, $count);
	        return $next;
	    }
	    // mise à jour de l'affichage de la jauge
	    print netbase::get_display_final_progress();
	    return 0;
	}
	
	public static function index() {
	    if(static::$indexation_by_fields) {
    	    $indexation_concepts = static::get_indexation_concepts();
    	    $indexation_concepts->launch_indexation();
	    } else {
	        $query = static::get_index_query(0, 0);
	        static::index_from_query($query);
	    }
	}
	
	public static function enable_index() {
	    if(static::$indexation_by_fields) {
    	    $indexation_concepts = static::get_indexation_concepts();
    	    $indexation_concepts->enable_fields_table_keys();
    	    $indexation_concepts->enable_words_table_keys();
	    } else {
	        pmb_mysql_query("ALTER TABLE skos_words_global_index ENABLE KEYS");
	        pmb_mysql_query("ALTER TABLE skos_fields_global_index ENABLE KEYS");
	    }
	}
	
	public static function set_indexation_by_fields($indexation_by_fields) {
	    static::$indexation_by_fields = $indexation_by_fields;
	}
	
	public static function get_step_position() {
	    return static::$step_position;
	}
	
	public static function set_step_position($step_position) {
	    static::$step_position = intval($step_position);
	}
	
	public static function get_indexation_concepts() {
	    if(!isset(static::$indexation_concepts) || static::$indexation_concepts == null) {
	        if(static::$indexation_by_fields) {
                static::$indexation_concepts = new indexation_concepts('', 'skos');
	        } else {
                static::$indexation_concepts = static::get_onto_index();
	        }
	    }
	    return static::$indexation_concepts;
	}
	
	public static function unset_indexation_concepts() {
	    static::$indexation_concepts = null;
	}
}
