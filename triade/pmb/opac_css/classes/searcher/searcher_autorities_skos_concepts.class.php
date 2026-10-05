<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_autorities_skos_concepts.class.php,v 1.7 2023/08/16 13:34:34 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_autorities_skos_concepts extends searcher_autorities {
    
    public function __construct($user_query){
        parent::__construct($user_query);
        $this->object_key = "id_item";
        $this->object_index_key= "id_item";
        $this->object_words_table = "skos_words_global_index";
        $this->object_fields_table = "skos_fields_global_index";
    }
    
    public function _get_search_type(){
        return parent::_get_search_type()."_concepts";
    }
    
    // à réécrire au besoin...
    protected function _sort($start,$number){
        if($this->table_tempo != ""){
            $query = "select ".$this->table_tempo.".".$this->object_key." from ".$this->table_tempo." join ".$this->object_fields_table." on ".$this->table_tempo.".".$this->object_key." = ".$this->object_fields_table.".".$this->object_index_key." where code_champ= 1 order by pert desc,".$this->object_fields_table.".".$this->object_fields_value." asc limit ".$start.",".$number;
        } else {
            $query = "select ".$this->object_key." from ".$this->object_fields_table." where code_champ= 1 and code_ss_champ = 1 and ".$this->object_fields_table.".".$this->object_index_key." in (".$this->get_result().") order by ".$this->object_fields_table.".".$this->object_fields_value." asc limit ".$start.",".$number;
        }
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $this->result=array();
            while($row = pmb_mysql_fetch_object($result)){
                $this->result[] = $row->{$this->object_key};
            }
        }
    }
    
    protected function _get_search_query(){
        global $concept_scheme;
        
        $query = parent::_get_search_query();
        
        $filters = $this->_get_authorities_filters();
        $filters[] = 'type_object = '.AUT_TABLE_CONCEPT;
        $filters[] = 'num_object in ('.$query.')';
        if(!is_array($concept_scheme)){
            if($concept_scheme !== ''){
                $concept_scheme = explode(',',$concept_scheme);
            }else{
                $concept_scheme = [];
            }
        } elseif (!isset($concept_scheme[0]) || $concept_scheme[0] === "") {
            $concept_scheme[0] = -1;
        }
        $query = 'select num_object as id_item from authorities';
        if (count($concept_scheme)> 0 && $concept_scheme[0] == 0) {
            // On cherche dans les concepts sans schéma
            $query.= ' left join skos_fields_global_index on authorities.num_object = skos_fields_global_index.id_item and code_champ = 4 ';
//            $filters[] = 'authority_num is null';
        } else if (count($concept_scheme) && ($concept_scheme[0] != -1)) {
            $query.= ' join skos_fields_global_index on authorities.num_object = skos_fields_global_index.id_item and code_champ = 4 ';
            $filters[] = 'authority_num in ('.implode(",",$concept_scheme).')';
        }
        
        if (count($filters)) {
            $query .= ' where '.implode(' and ', $filters);
        }
        return $query;
    } 
   
    public function get_sorted_result($tri = "default",$start=0,$number=20){
        global $lvl;
        
        $this->result = array();
        $this->tri = $tri;
        $this->_delete_old_objects();
        $this->_analyse();
        $cache_result = $this->_get_in_cache(true);
        if($cache_result===false){
            $this->get_result();
            $this->_sort_result($start,$number);
            $this->_set_in_cache(true);
        }else{
            $this->result = $cache_result;
        } 
        if (empty($this->result)) return array();
        if (!empty($lvl) && $lvl == 'more_results') {
            $query = "SELECT id_authority FROM authorities WHERE type_object= " . AUT_TABLE_CONCEPT . " and num_object in (" . implode(',', $this->result) . ") ";
            $result = pmb_mysql_query($query);
            $this->result = array();
            if($result && pmb_mysql_num_rows($result)){
                while($row = pmb_mysql_fetch_object($result)){
                    $this->result[] = $row->id_authority;
                }
            }
        }
        return $this->result;
    }
    
    protected function _filter_results(){
        global $concept_scheme;
        
        // en résultat de recherche simple, on ne doit pas filtrer
        if (empty($concept_scheme[0])) {
            return $this->objects_ids; 
        }        
        $query = "";
        if(!is_array($concept_scheme)){
            if($concept_scheme !== ''){
                $concept_scheme = explode(',',$concept_scheme);
            }else{
                $concept_scheme = [];
            }
        } elseif ($concept_scheme[0] === "") {
            $concept_scheme[0] = -1;
        }
        if (count($concept_scheme) > 0 && $concept_scheme[0] == 0) {
            // On cherche dans les concepts sans schéma
            $query = "select ".$this->object_key." from ".$this->object_fields_table." where ".$this->object_key." not in (select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 4) and code_champ = 1";
        } else if (count($concept_scheme) && ($concept_scheme[0] != -1)) {
            // On cherche dans un schema en particulier
            $query = "select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 4 and authority_num in (".implode(",",$concept_scheme).")";
        }
        // Pas de filtre si on cherche dans tous les schémas
        if ($query && $this->objects_ids) {
            $query.= " and ".$this->object_key." in (".$this->objects_ids.")";
            $result = pmb_mysql_query($query);
            $this->objects_ids ="";
            if($result && pmb_mysql_num_rows($result)){
                while($row = pmb_mysql_fetch_object($result)){
                    if($this->objects_ids) $this->objects_ids.= ",";
                    $this->objects_ids.= $row->{$this->object_key};
                }
            }
        }
    }
    
    protected function get_full_results_query(){
        global $concept_scheme;
        $query = "select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 1";
        if(!is_array($concept_scheme)){
            if (isset($concept_scheme) && $concept_scheme !== '') {
                $concept_scheme = explode(',',$concept_scheme);
            } else {
                $concept_scheme = [];
            }
        } elseif ($concept_scheme[0] === "") {
            $concept_scheme[0] = -1;
        }
        if (count($concept_scheme) > 0 && $concept_scheme[0] == 0) {
            // On cherche dans les concepts sans schéma
            $query.= " and ".$this->object_key." not in (select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 4)";
        } else if (count($concept_scheme) && ($concept_scheme[0] != -1)) {
            // On cherche dans un schema en particulier
            $query.= " and ".$this->object_key." in (select ".$this->object_key." from ".$this->object_fields_table." where code_champ = 4 and authority_num in (".implode(",",$concept_scheme)."))";
        }
        return $query;
    }
    
    protected function _get_sign_elements($sorted=false) {
        global $concept_scheme;
        if(!is_array($concept_scheme)){
            if($concept_scheme !== ''){
                $concept_scheme = explode(',',$concept_scheme);
            }else{
                $concept_scheme = [];
            }
        } elseif ($concept_scheme[0] === "") {
            $concept_scheme[0] = -1;
        }
        $str_to_hash = parent::_get_sign_elements($sorted);
        $str_to_hash .= "&concept_scheme=".implode(",",$concept_scheme);
        return $str_to_hash;
    }
    
    protected function _get_authorities_filters() {
        $filters = parent::_get_authorities_filters();
        return $filters;
    }
}