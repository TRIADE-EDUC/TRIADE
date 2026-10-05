<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_set.class.php,v 1.16 2023/09/07 13:31:20 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/search_universes/search_segment_set.tpl.php');
require_once($class_path."/search.class.php");

class search_segment_set {
	
	protected $num_segment;
	
	protected $human_query;
	
	protected $data_set;
	
	protected $type;
	
	protected $table_tempo;
	
	protected $search_instance;
		
	public function __construct($num_segment = 0){
		$this->num_segment = intval($num_segment);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
	    $this->type = '';
		if ($this->num_segment) {
			$query = '
			    SELECT search_segment_set, search_segment_type
			    FROM search_segments 
			    WHERE id_search_segment = "'.$this->num_segment.'"
			';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_assoc($result);

				$this->data_set = $row['search_segment_set'] ?? "";
				$this->type = $row['search_segment_type'];
			}
		}
	}

	public function get_data_set() {
	    return $this->data_set;
	}

	public function get_human_query() {
	    if (isset($this->human_query)) {
	        return $this->human_query;
	    }
	    if (empty($this->data_set)) {
	        return '';
	    }
	    $search = $this->get_search_instance();
	    $search->json_decode_search($this->data_set);
	    $this->human_query = $search->make_human_query();
	    return $this->human_query;
	}
	
	public function get_form() {
	    global $msg, $charset, $base_url;
	    global $search_segment_set_form;

	    $authperso_id = 0;
	    $class_id = 0;
	    $ontology_id = 0;
	    if (empty($search_segment_set_form))  {
	        return '';
	    }
	    
	    $search_segment_set_form = str_replace('!!segment_id!!', $this->num_segment, $search_segment_set_form);
	    $search_segment_set_form = str_replace('!!segment_type!!', $this->get_search_type_from_segment_type(), $search_segment_set_form);	    
	    $search_segment_set_form = str_replace('!!segment_set_human_query!!', $this->get_human_query(), $search_segment_set_form);	    
	    $search_segment_set_form = str_replace('!!segment_set_data_set!!', $this->get_data_set(), $search_segment_set_form);	

	    if ((int) $this->type > 10000) {
	        $class_id = intval($this->type) - 10000;
	        $uri = onto_common_uri::get_uri($class_id);
	        $ontology_id=ontologies::get_ontology_id_from_class_uri($uri);
	    }else if ((int) $this->type > 1000) {
	        $authperso_id = intval($this->type) - 1000;
	    }
	    
	    $search_segment_set_form = str_replace('!!authperso_id!!', $authperso_id, $search_segment_set_form); 
	    $search_segment_set_form = str_replace('!!class_id!!', $class_id, $search_segment_set_form); 
	    $search_segment_set_form = str_replace('!!ontology_id!!', $ontology_id, $search_segment_set_form);
	    return $search_segment_set_form;
	}
		
	public function set_properties_from_form(){
	    $search = $this->get_search_instance();
	    $this->data_set = $search->json_encode_search();
	    $this->human_query = $search->make_human_query();
	}
	
	public function update() {
	    if (!$this->num_segment) {
	        return false;
	    }
	    
		$query = '
		    UPDATE search_segments 
		    SET search_segment_set = "'.addslashes($this->data_set).'"
		    WHERE id_search_segment = "'.$this->num_segment.'"';
		pmb_mysql_query($query);
		
		return true;
	    
	}
	
	public function get_search_instance() {
	    global $ontology_id;
		if (isset($this->search_instance)) {
			return $this->search_instance;
		}
	    if (isset($this->type)) {
	        switch($this->type) {
	            case TYPE_NOTICE :
	                $this->search_instance = new search(false);
	                break;
	            case TYPE_EXTERNAL :
	                $this->search_instance = new search(false, "search_fields_unimarc");
	                break;
	            case TYPE_ANIMATION :
	                $this->search_instance = new search(false, "search_fields_animations");
	                break;
				case TYPE_CMS_EDITORIAL :
					$this->search_instance = new search(false, "search_fields_cms_editorial");
					break;
	            default :
	                if($this->type > 10000){    
	                    $ontology = new ontology(ontologies::get_ontology_id_from_class_uri(onto_common_uri::get_uri($this->type-10000)));
	                    $this->search_instance = new search_ontology(false, 'search_fields_ontology','',$ontology->get_handler()->get_ontology());
	                }else {
                        $this->search_instance = new search_authorities(false, 'search_fields_authorities');
	                }
                    break;
	        }
	        return $this->search_instance;
	    }
	    $this->search_instance = new search(false);
	    return $this->search_instance;
	}
	
	protected function get_search_type_from_segment_type() {
	    if (isset($this->type)) {
	        switch($this->type) {
	            case TYPE_AUTHOR :
	                return 'auteur';
	            case TYPE_CATEGORY :
	                return 'categorie';
	            case TYPE_COLLECTION :
	                return 'collection';
	            case TYPE_CONCEPT :
	                return 'ontology';
	            case TYPE_INDEXINT :
	                return 'indexint';
	            case TYPE_NOTICE :
	                return 'notice';
	            case TYPE_PUBLISHER :
	                return 'editeur';
	            case TYPE_SERIE :
	                return 'serie';
	            case TYPE_SUBCOLLECTION :
	                return 'subcollection';
	            case TYPE_TITRE_UNIFORME :
	                return 'titre_uniforme';
	            case TYPE_EXTERNAL :
	                return 'external_notice';
	            case TYPE_ANIMATION :
	                return 'animations';
	            case TYPE_CMS_EDITORIAL :
	                return 'cms_editorial';
	            default :
	                if ((int) $this->type > 10000) {
	                    $type = $this->type - 10000;
	                    $uri = onto_common_uri::get_uri($type);
	                    return 'ontologies&ontology_id='.ontologies::get_ontology_id_from_class_uri($uri);
	                }
	                if ((int) $this->type > 1000) {
	                    return 'authperso';
	                }
	                return 'notice'; 
	        }
	    }
	}
	
	public function make_search($prefix = '') {
		if (isset($this->table_tempo)) {
			return $this->table_tempo;
		}
		if (empty($this->data_set)) {
			$this->table_tempo = '';
		}
		$this->get_search_instance();
		$this->search_instance->json_decode_search($this->data_set);
		$this->table_tempo = $this->search_instance->make_search($prefix);
		
		return $this->table_tempo;
	}
	
	public function delete_data_set(){
	    $this->data_set = "";
	    $this->human_query = "";
	}

	public function set_data_set($dataset)
	{
		$this->data_set = $dataset;
	}
}