<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx.class.php,v 1.10 2019-05-27 12:55:59 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/sphinx/api/sphinxapi.php';
require_once $class_path.'/filter_results.class.php';
require_once $class_path.'/sort.class.php';

class searcher_sphinx {
	protected $user_query = '';
	protected $sphinx_query = '';
	protected $bypass = 10000;
	protected $maxmatches = 100000;
	//A REDEFINIR
	protected $index_name = 'records';
	protected $objects_ids;
	protected $tmp_table = "";
	protected $fields_restrict = array();
	protected $fields_ignore = array();
	//A REDEFINIR
	protected $id_key = 'notice_id';
	protected $champ_base_path = './includes/indexation/notices/champs_base.xml';
	
	/**
	 * 
	 * @var SphinxClient
	 */
	protected $sc = null;
	protected $sphinx_base;
	
	/* Nombre de résultats */
	protected $nb_result;
	
	/**
	 * requête de filtrage
	 * @var string $filter_query
	 */
	protected $filter_query;
	
	public function __construct($user_query){
	    global $sphinx_mysql_connect,$sphinx_pert_calc_method;
	    
	    $this->user_query = $user_query;
		$this->sphinx_query = analyse_query::get_sphinx_query($this->user_query);
		
		$this->sc = new SphinxClient();
		$connect_params = explode(',', $sphinx_mysql_connect);
		if ($connect_params[0]) {
		    $tmp = explode(":",$connect_params[0]);
		    $this->sc->_host = $tmp[0];
		}
		
		$this->sc->Open();
		$this->sc->SetLimits(0, $this->bypass,$this->maxmatches);
 		$this->sc->SetArrayResult(true);
		$this->sc->SetMatchMode(SPH_MATCH_EXTENDED2);
		$this->sc->SetSortMode(SPH_SORT_EXTENDED, '@weight DESC');
		if($sphinx_pert_calc_method == ""){
		    $prime_exact = "top(3*exact_hit*user_weight)";
		    $prime_start = "top(exact_order*user_weight/min_hit_pos)";
		    $proximity = "sum(lcs*user_weight)";
		    $sphinx_pert_calc_method = "($proximity+$prime_start+$prime_exact)*1000+bm25";
		}
		$this->sc->SetRankingMode(SPH_RANK_EXPR,$sphinx_pert_calc_method);
  		$this->sc->SetSelect("id");	
  		if($this->index_name != 'concepts'){
  		    $this->sphinx_base = new sphinx_base();
  		    $this->sphinx_base->setDefaultIndex($this->index_name);
  		    $this->sphinx_base->setChampBaseFilepath($this->champ_base_path);
  		}else{
  		    $this->sphinx_base = new sphinx_concepts_indexer();
  		}
		$this->sc->SetFieldWeights($this->sphinx_base->get_fields_pond());
	}
	
	protected function get_search_indexes(){
		global $lang;
		global $sphinx_indexes_prefix;
		
		return $sphinx_indexes_prefix.$this->index_name.'_'.$lang.','.$sphinx_indexes_prefix.$this->index_name;
	}

	protected function get_full_raw_query(){
		//A REDEFINIR
		return 'select notice_id as id, 100 as weight from notices';
	}
	
	protected function get_tempo_tablename(){
		return 'sphinx_'.md5(get_class($this).'_'.md5($this->sphinx_query));
	}
	
	public function get_objects_ids() {	    
        return $this->objects_ids;
    }
    
	protected function _get_objects_ids(){
		if(isset($this->objects_ids)){
			return $this->objects_ids;
		}
		$this->objects_ids = '';
 		$this->_build_tmp_table();
 		$query = '';
 		if ($this->sphinx_query != '*') {
 			$query = $this->get_fields_restrict().' ('.$this->sphinx_query.') ';
 		}
 		$filters = $this->get_filters();
 		if ($this->sphinx_query == '*' && count($filters) == 0){
 			$query = $this->get_full_raw_query();
			$result = pmb_mysql_query($query);
			$response = array();
			while($row = pmb_mysql_fetch_assoc($result)){
			 	$response[] = $row;
			 	if($this->objects_ids){
					$this->objects_ids.=',';
				}
				$this->objects_ids.= $row['id'];
			}
			$this->insert_in_tmp_table($response);
			return $this->objects_ids;
		}
		$this->sc->ResetFilters();
		for($i=0 ; $i<count($filters) ; $i++){
		    if(!is_array($filters[$i]['values'])){
		        $filters[$i]['values'] = array( $filters[$i]['values']);
		    }
		    array_walk($filters[$i]['values'], function(&$item,$key){
		        $item = crc32($item);
		    });
		    $this->sc->SetFilter($filters[$i]['name'], $filters[$i]['values']);
		}
		$nb = 0;
		do {
			$this->sc->SetLimits($nb, $this->bypass);
 			$result = $this->sc->Query($query,$this->get_search_indexes());
 			for($i = 0 ; $i<count($result['matches']) ; $i++){
 				if($this->objects_ids){
 					$this->objects_ids.=',';
 				}
 				$this->objects_ids.=$result['matches'][$i]['id'];
 			}
 			$nb+=count($result['matches']);
 			$this->insert_in_tmp_table($result['matches']);
 			if(!$this->nb_result){
 				$this->nb_result = $result['total_found'];
 			}
 		} while ($nb < $result['total_found']);
		return $this->objects_ids;
	}
	
	protected function _build_tmp_table(){
		$query = 'create temporary table IF NOT EXISTS '.$this->get_tempo_tablename().'('.$this->id_key.' int,pert int,index using btree('.$this->id_key.'))' ;
		pmb_mysql_query($query);
	}
	
	protected function insert_in_tmp_table($objects){
		if(count($objects)){
			$query = 'insert into '.$this->get_tempo_tablename().'('.$this->id_key.', pert) values ';
			for($i = 0 ; $i<count($objects) ; $i++){
				if($i>0){
					$query.=', ';
				}
				$query.= '('.$objects[$i]['id'].','.$objects[$i]['weight'].')';
			}
			pmb_mysql_query($query);
		}
	}
	
	public function get_result(){
		//$start = microtime(true);
		//print '<div>lancement de get_result</div>';
		$this->_get_objects_ids();
		//printtime('searcher_sphinx::_get_objects_ids');
		$this->_filter_results();
		//printtime('searcher_sphinx::_filter_results');
		//print '<p>FILTER RESULT : '.count(explode(',',$this->objects_ids)).'</p>';
		return $this->objects_ids;
	}
	
	protected function _filter_results(){
		//A REDEFINIR
	}
	
	public function get_raw_query(){
		$this->_get_objects_ids();
		$query =  'select '.$this->id_key.', pert from '.$this->get_tempo_tablename();
		return $query;
	}
	
	public function get_full_query(){
		$this->get_result();
		$query =  'select '.$this->id_key.', pert from '.$this->get_tempo_tablename();
		return $query;
	}
	
	public function get_nb_results(){
		if($this->nb_result){
			return $this->nb_result;
		}
		$this->get_result();
		if (!$this->nb_result && ($this->objects_ids != '')) {
			$this->nb_result = count(explode(',', $this->objects_ids));
		}
		return $this->nb_result;
	}

	public function get_sorted_result($tri = "default",$start=0,$number=20){
		//A REDEFINIR
		$this->tri = $tri;
		$this->get_result();
		$sort = new sort("notices","session");
		$query = $sort->appliquer_tri_from_tmp_table($this->tri,$this->get_tempo_tablename(),$this->id_key,$start,$number);
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$this->result=array();
			while($row = pmb_mysql_fetch_object($res)){
				$this->result[] = $row->notice_id;
			}
		}
		return $this->result;
	}	
		
	public function explain($display = "",$mode = "",$mini=false){
		//A DERIVER si on veut
	}	
		
	public function get_fields_restrict(){
		if(count($this->fields_restrict)){
			$this->fields_restrict = array_unique($this->fields_restrict);
			return '@('.implode(',',$this->fields_restrict).')';
		}
		if (count($this->fields_ignore)) {
			$this->fields_ignore = array_unique($this->fields_ignore);
			return '@!('.implode(',',$this->fields_ignore).')';
		}
		return '';
	}

	public function init_fields_restrict($mode){
		$this->fields_restrict = array();
		$this->fields_ignore = array();
	}

	public function add_fields_restrict($restrict){	
		$indexes = $this->sphinx_base->getIndexes();
		$indexes = $indexes[$this->index_name]['fields'];
		for($i=0 ; $i<count($restrict) ; $i++){
			if($restrict[$i]['field'] == 'code_champ'){
				if(count($restrict[$i]['sub'])>0){
					for($j=0 ; $j<count($restrict[$i]['sub']) ; $j++){
						foreach($restrict[$i]['values'] as $value){
							foreach ($restrict[$i]['sub'][$j]['values'] as $sub_value){
								$this->fields_restrict[]='f_'.str_pad($value, 3,"0",STR_PAD_LEFT).'_'.str_pad($sub_value, 2,"0",STR_PAD_LEFT);
							}
						}
					}
				}else{
					foreach($restrict[$i]['values'] as $value){
						for($j=0 ; $j<count($indexes) ; $j++){
							if(strpos($indexes[$j], 'f_'.str_pad($value, 3,"0",STR_PAD_LEFT).'_')===0){
								$this->fields_restrict[]=$indexes[$j];
							}
						}
					}
				}
			}
		}
	}
	
	public function get_results_list_from_search($label, $user_input, $list, $navbar) {
		$template = "
			<br />
			<br />
			<div class='row'>
				<h3>".$this->get_nb_results()." ".$label." ".$user_input."</h3>
			</div>
			<script type='text/javascript' src='./javascript/sorttable.js'></script>
			<table class='sortable'>
				".$list."
			</table>
			<div class='row'>
				".$navbar."
			</div>";
		return $template;
	}
	
	public function get_pert_result($query = false){
		if ($query) {
			return 'select '.$this->id_key.', pert from '.$this->get_tempo_tablename();
		}
		return $this->get_tempo_tablename();
	}
	
	protected function get_filters(){
		return array();
	}
	
	/**
	 * Retourne la liste des langues pour l'indexation
	 * TODO Aller lire un paramètre proprement
	 * @return array()
	 */
	public function get_available_languages() {
		//TODO A FAIRE PROPREMENT
		return array('','fr_FR','en_UK');
	}
}