<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_authorities.class.php,v 1.4 2019-05-27 12:55:59 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_sphinx_authorities extends searcher_sphinx {
	
	protected $authority_type;
	
	public function __construct($user_query){
		parent::__construct($user_query);
		$this->id_key = 'id_authority';
		$this->index_name = 'authors,categories,indexint,series,publishers,collections,subcollections,titres_uniformes,concepts';
 	}	
 	
 	protected function get_search_indexes(){
 		global $lang;
 		global $sphinx_indexes_prefix;
 		$indexes = explode(',',$this->index_name);
 		$index = "";
 		foreach($indexes as $index_name){
 			if($index) $index.= ',';
 			$index.= $sphinx_indexes_prefix.trim($index_name).'_'.$lang.','.$sphinx_indexes_prefix.trim($index_name);
 		}
 		if(!$this->authority_type){
	 		$result = pmb_mysql_query('select id_authperso from authperso');
	 		if (pmb_mysql_num_rows($result)) {
	 			while ($row = pmb_mysql_fetch_object($result)) {
	 				if($index) $index.= ',';
	 				$index.= $sphinx_indexes_prefix.'authperso_'.$row->id_authperso.'_'.$lang.','.$sphinx_indexes_prefix.'authperso_'.$row->id_authperso;
	 			}
	 		}
 		}
 		return $index;
 	}
	
	protected function get_full_raw_query(){
		if($this->authority_type){
			return 'select id_authority as id, 100 as weight from authorities where type_object = '.$this->authority_type;
		}
		return 'select id_authority as id, 100 as weight from authorities';
	}
	
	protected function _filter_results(){
		if($this->objects_ids!='') {
			$this->filter_authorities_forms();
// 			$fr = new filter_results($this->objects_ids);
// 			$this->objects_ids = $fr->get_results();
			$query = 'delete from '.$this->get_tempo_tablename();
			if($this->objects_ids != ''){
				$query.=' where id_authority not in ('.$this->objects_ids.')' ;
			}
			pmb_mysql_query($query) or die(pmb_mysql_error());
		}
	}
	
	protected function filter_authorities_forms(){
		//A DERIVER
	}
	
	public function get_full_query(){		
		$this->get_result();
		$query =  'select id_authority, pert from '.$this->get_tempo_tablename();	
		return $query;
	}
	
	public function get_sorted_result($tri = "default",$start=0,$number=20){
		$this->tri = $tri;
		$this->get_result();
		$this->result = array_slice(explode(',',$this->objects_ids), $start,$number);
		return $this->result;
	}
	
	public function explain($display = "",$mode = "",$mini=false){
		print '<div style="margin-left:10px;width:49%;overflow:hidden;float:left">';
		print '<h1>Recherche SPHINX</h1>';
		print '<p>QUERY : '.$this->sphinx_query.'</p>';
		$start = microtime(true);
 		print '<p>Nombre de resultats trouves: '.$this->get_nb_results().'</p>';
 		$result = $this->get_sorted_result();
 		if($this->get_nb_results()>0 && $result){
	 		$inter = microtime(true);
		 	print '<p>Temps de calcul (en seconde) : '.($inter - $start).'</p>';
		 	$elements_authorities_list_ui = new elements_authorities_list_ui($result, 20, $this->authority_type);
		 	$elements = $elements_authorities_list_ui->get_elements_list();
		 	print $begin_result_liste;
		 	print $elements;
		 	print $end_result_liste;
	 		print '<p>Temps de gen page (en seconde) : '.(microtime(true) - $inter).'</p>';
 		}
 		print '<p>Temps Total (en seconde) : '.(microtime(true) - $start).'</p></div>';
	}
		
	protected function get_filters(){
		$filters = parent::get_filters();
		global $authority_statut;
		if($authority_statut){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée! 
			$filters[] = array(
				'name'=> 'status',
				'values' => $authority_statut*1	
			);
		}
		return $filters;
	}
}