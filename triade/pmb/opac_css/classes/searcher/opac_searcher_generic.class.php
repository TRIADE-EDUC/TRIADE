<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_searcher_generic.class.php,v 1.16 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/analyse_query.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/filter_results.class.php");
require_once($class_path."/sort.class.php");
global $opac_search_other_function;
if($opac_search_other_function){
	require_once($include_path."/".$opac_search_other_function);
}

//classe devant piloter les recherches de manière générique (quelle idée...)
class opac_searcher_generic {
	protected $searched = false;		// booléen pour éviter de tourner en rond...
	public $user_query = '';			// recherche saisie
	protected $aq;						// analyse query
	public $objects_ids = '';			// liste de notices sous forme de chaine
	protected $keep_empty = 0;			// flag pour les mots vides
	protected $field_restrict;			// ids des champs à utiliser (restriction)
	public $table_tempo;				// table temporaire contenant les résultats filtrés triés..;
	
	public $object_key;					// clé finale
	public $object_table;				// table des objets
	public $object_index_key;			// clé dans les tables d'index
	public $object_words_table; 		// table des mots
	public $object_words_value = "word"; // colonne de recherche pour words
	public $object_fields_table;		// table des champs
	public $object_fields_value = "value"; //colonne de recherche pour fields
	public $search_noise_limit_type;
	public $search_cache_duration;
	public $stemming_active;
	
	protected $tri="default";	// tri à utiliser
	
	public function __construct($user_query=''){
		global $opac_search_noise_limit_type;
		global $opac_search_cache_duration;
		global $opac_stemming_active;
		$this->searched=false;
		$this->user_query = $user_query;
		$this->search_noise_limit_type = $opac_search_noise_limit_type;
		$this->search_cache_duration = $opac_search_cache_duration;
		$this->stemming_active = $opac_stemming_active;
		$this->field_restrict = array();
	}

	protected function _analyse(){
		if(!is_object($this->aq) && $this->user_query){
			$this->aq= new analyse_query($this->user_query,0,0,1,$this->keep_empty,$this->stemming_active);
		}
	}


	protected function _calc_query_env(){
		//appeler avant ma génération de la requete de recherche...
	}

	protected function _get_search_query(){
		$this->_calc_query_env();
		if($this->user_query !== "*"){
			$query = $this->aq->get_query_mot($this->object_index_key,$this->object_words_table,$this->object_words_value,$this->object_fields_table,$this->object_fields_value,$this->field_restrict);
		}else{
			$query = $this->get_full_results_query();
		}
		
		if(method_exists($this, "_get_typdoc_filter")){
			if($this->user_query === "*"){
				$query.= static::_get_typdoc_filter(true);
			}else {
				$query.= static::_get_typdoc_filter();
			}
		}
		return $query;
	}

	protected function get_full_results_query(){
		$query = 'select '.$this->object_key.' as '.$this->object_index_key.' from '.$this->object_table;
		return $query;
	}
	
	
	protected function _get_pert($query=false){
	    if($query){
			return $this->aq->get_objects_pert($this->objects_ids,$this->object_index_key,$this->object_words_table,$this->object_words_value,$this->object_fields_table,$this->object_fields_value,$this->object_key,$this->field_restrict,false,false,$query);
		}else{
			$this->table_tempo = $this->aq->get_objects_pert($this->objects_ids,$this->object_index_key,$this->object_words_table,$this->object_words_value,$this->object_fields_table,$this->object_fields_value,$this->object_key,$this->field_restrict,false,false,$query);
		}
	}
	
	public function get_objects_ids() {
	    return $this->objects_ids;
	}
	
	protected function _get_objects_ids(){
		global $dbh;
		if(!$this->searched){
			$query = $this->_get_search_query();
			$this->objects_ids="";
			$res = pmb_mysql_query($query,$dbh);			
			if($res){
			
				if(pmb_mysql_num_rows($res)){
					while ($row = pmb_mysql_fetch_object($res)){
						if($this->objects_ids!="") $this->objects_ids.=",";
						$this->objects_ids.=$row->{$this->object_index_key};
					}
				}
				pmb_mysql_free_result($res);
			}
			$this->searched=true;
		}
		return $this->objects_ids;
	}

	protected function _delete_old_objects(){
		global $dbh;
		$delete= "delete from search_cache where delete_on_date < NOW()";
		pmb_mysql_query($delete,$dbh);
	}

	protected function _get_user_query(){
		return $this->user_query;
	}

	protected function _get_sign($sorted=false){
		$str_to_hash = $this->_get_sign_elements($sorted);
		return md5($str_to_hash);
	}
	
	protected function _get_sign_elements($sorted=false){
		global $opac_search_other_function;
		global $typdoc;
		global $page;
		global $lang;
		global $dont_check_opac_indexation_docnum_allfields;
		global $mutli_crit_indexation_docnum_allfields;
		global $nb_per_page;
		
		$str_to_hash = session_id();
		$str_to_hash.= "&opac_view=".(isset($_SESSION['opac_view']) ? $_SESSION['opac_view'] : '');
		$str_to_hash.= $_SESSION['user_code'];
		$str_to_hash.= "&lang=".$lang;
		$str_to_hash.= "&type_search=".$this->_get_search_type();
		$str_to_hash.= "&user_query=".$this->_get_user_query();
		$str_to_hash.= "&typdoc=".$typdoc;
		$str_to_hash.= "&dont_check_opac_indexation_docnum_allfields=".$dont_check_opac_indexation_docnum_allfields;
		$str_to_hash.= "&mutli_crit_indexation_docnum_allfields=".$mutli_crit_indexation_docnum_allfields;
		if($opac_search_other_function){
			$str_to_hash.= "&perso=".serialize(search_other_function_get_values());
		}
		if($sorted){
			$str_to_hash.= "&tri=".$this->tri;
			$str_to_hash.= "&page=$page";
		}
		if($nb_per_page) {
			$str_to_hash.= "&nb_per_page=".$nb_per_page;
		}
		return $str_to_hash;
	}

	protected function _get_in_cache($sorted=false){
		global $dbh;
		global $opac_search_cache_duration;
		//return false;
		
		if($opac_search_cache_duration){
		    $read = "select value from search_cache where object_id='".$this->_get_sign($sorted)."'";
		    $res = pmb_mysql_query($read,$dbh);
		    if(pmb_mysql_num_rows($res)>0){
		        $row = pmb_mysql_fetch_object($res);
		        if(!$sorted){
		            $cache = $row->value;
		        }else{
		            $cache = unserialize($row->value);
		        }
		        return $cache;
		    }
		}
		return false;
	}

	protected function _set_in_cache($sorted=false){
		global $dbh;
		global $opac_search_cache_duration;
		
		if($opac_search_cache_duration){
    		if($sorted == false){
    			$str_to_cache = $this->objects_ids;
    		}else{
    			$str_to_cache = serialize($this->result);
    		}
    		$insert = "insert into search_cache set object_id ='".addslashes($this->_get_sign($sorted))."', value ='".addslashes($str_to_cache)."', delete_on_date = now() + interval ".$this->search_cache_duration." second";
    		pmb_mysql_query($insert,$dbh);
		}
	}

	public function get_nb_results(){
		if(!$this->objects_ids) $this->get_result();
		if($this->objects_ids == ""){
			return 0 ;
		}else{
			return substr_count($this->objects_ids,",")+1;
		}
	}

	// à réécrire au besoin...
	protected function _sort($start,$number){
		global $dbh;
		if($this->table_tempo != ""){
			$query = "select * from ".$this->table_tempo." order by pert desc limit ".$start.",".$number;
			$res = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($res)){
				$this->result=array();
				while($row = pmb_mysql_fetch_object($res)){
					$this->result[] = $row->{$this->object_key};
				}
			}
		}
	}

	public function get_result(){
		global $dbh;
		$this->_delete_old_objects();
		$this->_analyse();
		$cache_result = $this->_get_in_cache();
		if($cache_result===false){
			$this->_get_objects_ids();
			$this->_filter_results();
			//Ecretage
			if($this->search_noise_limit_type && $this->user_query !== "*"){
				$limit = 0;
				//calcul pertinence
				$this->_get_pert();
				//calcul du seuil.
				switch(substr($this->search_noise_limit_type,0,1)){
					// moyenne - ecart_type
					case 1 :
						$query = "select (avg(pert)-stddev_pop(pert)) as seuil from ".$this->table_tempo;
						break;
					// moyenne - % ecart_type
					case 2 :
						$ratio = substr($this->search_noise_limit_type,2);
						$query = "select (avg(pert)-(stddev_pop(pert))*".$ratio.") as seuil from ".$this->table_tempo;
						break;
					// %max
					case 3 :
						$ratio = substr($this->search_noise_limit_type,2);
						$query = "select (max(pert)*".$ratio.") as seuil from ".$this->table_tempo;
						break;				
				}
				$result = pmb_mysql_query($query,$dbh);
				if(pmb_mysql_num_rows($result)){
					$limit = pmb_mysql_result($result,0,0);
				}
				if($limit){				
					$query = "delete from ".$this->table_tempo." where pert < ".$limit;
					pmb_mysql_query($query,$dbh);
					$query ="select distinct ".$this->object_key." from ".$this->table_tempo;
					$result = pmb_mysql_query($query,$dbh);
					
					if(pmb_mysql_num_rows($result)){
						$this->objects_ids = "";
						while($row = pmb_mysql_fetch_object($result)){
							if($this->objects_ids) $this->objects_ids.=",";
							$this->objects_ids.=$row->{$this->object_key};
						}
					}
				}
			}
			$this->_set_in_cache();
		}else{
			$this->objects_ids = $cache_result;
		}
		return $this->objects_ids;
	}

	public function get_sorted_result($tri = "default",$start=0,$number=20){
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
		return $this->result;
	}


	protected function _sort_result($start,$number){
		$this->_get_pert();
		$this->_sort($start,$number);
	}

	protected function _filter_results(){
		// à surcharger au besoin...
	}

	public function get_full_query(){
		if($this->get_result()){
			$query = $this->_get_pert(true);
		}else{
			$query = "select ".$this->object_index_key." from ".$this->object_words_table." where ".$this->object_index_key." = 0";
		}
		return $query;
	}

	/**
	 * Ajoute des restriction au tableau $field_restrict
	 * @param array $fields_restrict Tableau des restrictions à ajouter
	 */
	public function add_fields_restrict($fields_restrict = array()) {
		$this->field_restrict = array_merge($this->field_restrict, $fields_restrict);
	}
	
	public function init_fields_restrict($mode) {
		return false;
	}
	
	public function get_temporary_table_name($suffix='') {
	    return static::class.substr(md5(microtime(true)), 0, 16).$suffix;
	}

}