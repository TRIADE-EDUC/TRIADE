<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_concepts.class.php,v 1.6 2019-04-01 10:26:22 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_concepts extends searcher_autorities {
	
	/**
	 * 
	 * @var searcher_autorities_skos_concepts
	 */
	protected $searcher_authorities_skos_concept;

	public function __construct($user_query){
		parent::__construct($user_query);
		$this->authority_type = AUT_TABLE_CONCEPT;
		$this->searcher_authorities_skos_concept = new searcher_autorities_skos_concepts($user_query);
		$this->searcher_authorities_skos_concept->add_fields_restrict($this->field_restrict);
		$this->object_index_key= "id_item";
		$this->object_words_table = "skos_words_global_index";
		$this->object_fields_table = "skos_fields_global_index";
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_concepts";
	}

	public function get_raw_query()	{
		return 'select '.$this->object_key.' from ('.$this->searcher_authorities_skos_concept->get_raw_query().') as uni join authorities on uni.'.$this->searcher_authorities_skos_concept->object_key.' = authorities.num_object and type_object = '.$this->authority_type;
	}
	
	public function get_pert_result($query = false) {
		global $dbh;
		$this->table_tempo = '';
		if ($this->searcher_authorities_skos_concept->get_result() && ($this->user_query != '*')) {
			$pert_result = $this->searcher_authorities_skos_concept->get_pert_result($query);
			if ($query) {
				return 'select '.$this->object_key.', pert from ('.$pert_result.') as uni join authorities on uni.'.$this->searcher_authorities_skos_concept->object_key.' = authorities.num_object and type_object = '.$this->authority_type;
			}
			$this->table_tempo = 'search_result'.md5(microtime(true));
			$pert_result = 'select '.$this->object_key.', pert from '.$pert_result.' join authorities on '.$pert_result.'.'.$this->searcher_authorities_skos_concept->object_key.' = authorities.num_object and type_object = '.$this->authority_type;
			$rqt = 'create temporary table '.$this->table_tempo.' '.$pert_result;
			$res = pmb_mysql_query($rqt,$dbh);
			pmb_mysql_query('alter table '.$this->table_tempo.' add index i_id('.$this->object_key.')',$dbh);
		}
		return $this->table_tempo;
	}
	
	protected function _get_pert($return_query = false) {
		return $this->get_pert_result($return_query);
	}
		
	protected function get_full_results_query(){
	    global $concept_scheme;
	    
	    if ($this->object_table) {
	        return 'select id_authority from authorities join '.$this->object_table.' on authorities.num_object = '.$this->object_table_key;
	    }
	    $query = 'select id_authority from authorities';
	    
	    $filters = $this->_get_authorities_filters();
	    $filters[] = 'type_object = '.AUT_TABLE_CONCEPT;
	    $filters[] = 'num_object in ('.$query.')';
	    if(!is_array($concept_scheme)){
	        if($concept_scheme !== ''){
	            $concept_scheme = explode(',',$concept_scheme);
	        }else{
	            $concept_scheme = [];
	        }
	    }
	    $query = 'select id_authority  from authorities';
	    if (count($concept_scheme)> 0 && $concept_scheme[0] == 0) {
	        // On cherche dans les concepts sans schéma
	        $query.= ' left join skos_fields_global_index on authorities.num_object = skos_fields_global_index.id_item and code_champ = 4 ';
	        $filters[] = 'authority_num is null';
	    } else if (count($concept_scheme) && ($concept_scheme[0] != -1)) {
	        $query.= ' join skos_fields_global_index on authorities.num_object = skos_fields_global_index.id_item and code_champ = 4 ';
	        $filters[] = 'authority_num in ('.implode(",",$concept_scheme).')';
	    }
	    
	    if (count($filters)) {
	        $query.= ' where '.implode(' and ', $filters);
	    }
	    return $query;
	}
}