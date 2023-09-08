<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_explnum.class.php,v 1.2 2018-04-05 14:01:29 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once("$class_path/searcher/searcher_records.class.php");

class searcher_records_explnum extends searcher_records {
	
	public function __construct($user_query){
		parent::__construct($user_query);
		$this->field_restrict=array();
	}
	
	protected function _get_search_type(){
		return parent::_get_search_type()."_explnum";
	}
	
	protected function _get_search_query(){
		$this->_calc_query_env();
		if($this->user_query !== "*"){
			$members = $this->aq->get_query_members('explnum', 'explnum_index_wew', 'explnum_index_sew', 'explnum_notice');
			$query = 'SELECT DISTINCT uni.notice_id as id_notice, pert FROM (
						SELECT notice_id, '.$members["select"].' AS pert 
						FROM notices 
						JOIN explnum ON explnum_notice = notice_id
						WHERE ('.$members["where"].')
						UNION
						SELECT notice_id, '.$members["select"].' AS pert 
						FROM notices
						JOIN bulletins ON bulletin_notice = notice_id
						JOIN explnum ON explnum_bulletin = bulletin_id
						WHERE ('.$members["where"].')
					)  AS uni JOIN notices n ON uni.notice_id = n.notice_id 
					ORDER BY pert DESC, index_serie, tnvol, index_sew';
		}else{
			$query = $this->get_full_results_query();
		}
		return $query;
	}
	
	protected function _get_pert($with_explnum = false, $query = false){
		$final_query = 'SELECT id_notice AS '.$this->object_key.', pert FROM ('.$this->_get_search_query().') as lorem';
		if($query){
			return $final_query;
		}
		$this->table_tempo = "gestion_result".md5(microtime(true));
		$res = pmb_mysql_query("create temporary table ".$this->table_tempo." ".$final_query);
		pmb_mysql_query("alter table ".$this->table_tempo." add index i_id('.$this->object_key.')");
		return $this->table_tempo;
	}
	
	protected function get_full_results_query(){
		global $lang;
		
		return 'select notice_id as id_notice from notices join explnum on explnum_notice = notice_id';
	}
	
	public function get_full_query(){
		global $lang;
		
		if($this->user_query === "*"){
			return 'select notice_id as id_notice, 100 as pert from notices join explnum on explnum_notice = notice_id';
		}
		return parent::get_full_query();
	}
}