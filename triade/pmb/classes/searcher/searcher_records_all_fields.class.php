<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_all_fields.class.php,v 1.7 2018-05-31 13:38:28 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once("$class_path/searcher/searcher_records.class.php");

class searcher_records_all_fields extends searcher_records {
	protected $aq_wew;
	protected $all_keep_empty_words = false;
	protected $with_docnums = false;
	
	public function __construct($user_query){
		global $pmb_search_all_keep_empty_words;

		parent::__construct($user_query);
		$this->field_restrict[]= array(
			'field' => "code_champ",
			'values' => array(18,19,20,21,23,24,25,26),
			'op' => "or",
			'not' => false
		);
		if($pmb_search_all_keep_empty_words){
 			$this->aq_wew= new analyse_query($this->user_query,0,0,1,1);
		}
	}
		
	protected function _get_search_type(){
		return parent::_get_search_type()."_all_fields";
	}
	
	protected function _get_all_fields_search_query(){
		$query = "";
		$queries = array();
 		$this->_calc_query_env();

 		if($this->user_query !== "*"){
 			if($this->aq_wew && count($this->aq->tree) !== count($this->aq_wew->tree)){
 				//debug::activeExplain();
 				$queries[] = $this->aq->get_query_mot("id_notice","notices_mots_global_index","word","notices_fields_global_index","value",$this->field_restrict,true,true);
 				$queries[] = $this->aq_wew->get_query_mot("id_notice","notices_mots_global_index","word","notices_fields_global_index","value",$this->field_restrict,false,true);
 				//debug::disableExplain();
 				$query = "select distinct id_notice from ((".implode(") union (",$queries).")) as uni"; 
 			}else{
 				$query = $this->aq->get_query_mot("id_notice","notices_mots_global_index","word","notices_fields_global_index","value",array(),true,true);
 			}
 		}else{
 			$query = "select distinct notice_id as id_notice from notices";
 		}
 		if($this->_get_typdoc_filter()!=""){
 			$query= "select distinct id_notice from ($query) as q1". $this->_get_typdoc_filter();
 		}
		return $query;
	}
	
	
	protected function _get_search_query(){
		global $docnum_query, $mutli_crit_indexation_docnum_allfields;

		if($docnum_query || $mutli_crit_indexation_docnum_allfields){
			$this->with_docnums =true;
		}
		
		$query = $this->_get_all_fields_search_query();
		if($this->user_query !== "*" && $this->with_docnums){
			$this->_get_explnum_members();
			$query_explnum_noti="select distinct explnum_notice as id_notice from explnum ".$this->_get_explnum_filter("notice","explnum_notice")." ".$this->_get_explnum_where()." and explnum_notice !=0 and explnum_bulletin=0 ";//.$this->_get_explnum_end("notice");
			$query_explnum_bull="select distinct num_notice as id_notice from explnum join bulletins on num_notice!= 0 and explnum_bulletin = bulletin_id ".$this->_get_explnum_filter("bulletin","num_notice")." ".$this->_get_explnum_where()." and explnum_bulletin !=0 and explnum_notice=0 ";//.$this->_get_explnum_end();
			if($this->_get_typdoc_filter()!=""){
				$query_explnum_noti= "select distinct id_notice from ($query_explnum_noti) as q2 ".$this->_get_typdoc_filter();
				$query_explnum_bull= "select distinct id_notice from ($query_explnum_bull) as q3 ". $this->_get_typdoc_filter();
			}
			$query = "select distinct id_notice from (($query) union ($query_explnum_noti) union ($query_explnum_bull))as uni ";
			
		}
		return $query;
	}
	
 	protected function _get_pert($with_explnum=false, $get_query=false){
 		global $dbh;
 		global $docnum_query, $mutli_crit_indexation_docnum_allfields;
 		
 		if($docnum_query || $mutli_crit_indexation_docnum_allfields){
 			$this->with_docnums =true;
 		}
 		if ($this->with_docnums){
 			$with_explnum = true;
 		}
 		if($this->user_query === "*"){
 			$query = "select notice_id, 100 as pert from notices";
 			if($get_query){
 				return $query;
 			}
 			$this->table_tempo = "gestion_result".md5(microtime(true));
 			$res = pmb_mysql_query("create temporary table ".$this->table_tempo." ".$query, $dbh);
 			pmb_mysql_query("alter table ".$this->table_tempo." add index i_id(notice_id)", $dbh);
 			return $this->table_tempo;
 		}

 		if($this->aq_wew && (count($this->aq->tree) != count($this->aq_wew->tree))){
 			$without_empty = $this->aq->get_pert($this->objects_ids,$this->field_restrict,true,$with_explnum,true,true);
 			$with_empty = $this->aq_wew->get_pert($this->objects_ids,$this->field_restrict,false,$with_explnum,true,true);
  			$query = "select notice_id, max(pert) as pert from (($without_empty) union all($with_empty))as q1 group by notice_id";
 			if($get_query){
 				return $query;
 			}else{
				$this->table_tempo = "gestion_result".md5(microtime(true));
 				$res = pmb_mysql_query("create temporary table ".$this->table_tempo." ".$query, $dbh);
 				pmb_mysql_query("alter table ".$this->table_tempo." add index i_id(notice_id)", $dbh);
 			}
 		}else{
 			if($get_query){
 				return $this->aq->get_pert($this->objects_ids,array(),false,$with_explnum,true,true);
 			}
 			$this->table_tempo = $this->aq->get_pert($this->objects_ids,array(),false,$with_explnum,false,true);
		}
 	}
 	
 	protected function _get_explnum_end($type){
 		if($type == "notice"){
 			return 	$this->members_explnum_noti['post'];
 		}else{
 			return 	$this->members_explnum_bull['post'];
 		}
 	}
 	
 	protected function _get_explnum_members(){
 		$this->members_explnum_noti = $this->aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_notice","",0,0,true);
 		$this->members_explnum_bull = $this->aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","id_notice","",0,0,true);
 	}
 	
 	protected function _get_explnum_where(){
		$where="where ((".$this->members_explnum_noti['where'].")) ";
		//if($this->view_restrict) $where.=" and ".$this->view_restrict;
		return $where;
	}
 	
 	protected function _get_explnum_pert(){
 		return $this->members_explnum_noti['select']." as pert";
 	}
 	
 	protected function _get_explnum_filter($type="notice",$field){
 		global $gestion_acces_active;
 		global $gestion_acces_user_notice;
 		global $PMBuserid;

 		$join = '';
 		if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
 			$ac= new acces();
 			$dom_1= $ac->setDomain(1);
 			$join = $dom_1->getJoin($PMBuserid,4,$field);
 		}
 		return $join;
 	}
}