<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_searcher_explnums.class.php,v 1.4 2021/12/28 10:06:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/searcher/opac_searcher_generic.class.php');
//require_once($include_path.'/misc.inc.php');

// log::$log_now=true;
// log::$log_format='html';

// pmb_mysql_query("set global max_tmp_tables=100");
// pmb_mysql_query("set global tmp_table_size=2000000000");
// pmb_mysql_query("set global max_heap_table_size=2000000000");


class opac_searcher_explnums extends opac_searcher_generic {

//1	protected $original_aq;
//1	protected $has_literal=false;
	
//2	protected $aql;
	
	public function __construct($user_query){
		parent::__construct($user_query);
		$this->object_key = "explnum_id";
		$this->object_index_key= "num_obj";
		$this->object_words_table = "explnum_words_global_index";
		$this->object_fields_table = "explnum_fields_global_index_w_fragments";
		
		$this->stemming_active=0;
		$this->keep_empty=0;
	}
	

	
//2 Modification analyse_query et _get_search_query pour recherche des mots uniquement avant 2eme passe pour recherche des expressions literales
/*
	protected function _analyse(){
	
		if(!is_object($this->aq) && $this->user_query){
			$this->aq = new analyse_query($this->user_query,0,0,1,$this->keep_empty,$this->stemming_active);
			$this->aql = clone $this->aq;
				
			//highlight_string(print_r($this->aq->tree,true));
	
			//Séparation des analyses de mots et d'expressions régulières
			$w_to_del = array();
			$l_to_del = array();
			if(count($this->aq->tree)) {
				$last = array_pop($this->aq->tree);
				foreach($this->aq->tree as $k=>$term) {
					if($term->literal==1) {
						$l_to_del[]=$k;
					}
					if($term->literal==0) {
						$w_to_del[$k];
					}
				}

				//highlight_string(print_r($to_del,true));
				//highlight_string(print_r($to_add,true));

				if(count($l_to_del)) {
					foreach($l_to_del as $k=>$v) {
						unset($this->aq->tree[$v]);
					}
				}
				if(count($w_to_del)) {
					foreach($w_to_del as $k=>$v) {
						unset($this->aql->tree[$v]);
					}
				}
			}
		}
	}
*/	

//1 Modification analyse_query et _get_search_query pour recherche des mots dans les expressions litérales avant restriction avec index_wew
/*
	protected function _analyse(){
		
		if(!is_object($this->aq) && $this->user_query){
			$this->aq= new analyse_query($this->user_query,0,0,1,$this->keep_empty,$this->stemming_active);
			$this->original_aq = clone $this->aq;
			
//highlight_string(print_r($this->aq->tree,true));
				
			//Transformation des analyses literales en et
			$to_del = array();
			$to_add = array();
			if(count($this->aq->tree)) {
				$last = array_pop($this->aq->tree);
				foreach($this->aq->tree as $k=>$term) {
					if($term->literal==1) {
						$this->has_literal=true;
						$to_del[]=$k;
						$sub_user_query = '('.str_replace('+',' ',$term->word).')';
						$sub_user_query = '('.preg_replace("/(\s)+/", ' ',$term->word).')';
//echo $sub_user_query.'<br />';
						$sub_aq = new analyse_query($sub_user_query,0,0,1,$this->keep_empty,$this->stemming_active);
						if(count($sub_aq->tree)) {
							foreach($sub_aq->tree as $k1=>$term1) {
								if(!$term1->literal) {
									$to_add[] = $term1;
								} 
							}
						} 
					}
				}

//highlight_string(print_r($to_del,true));
//highlight_string(print_r($to_add,true));

				if(count($to_del)) {
					foreach($to_del as $k=>$v) {
						unset($this->aq->tree[$v]);				
					}
				}
				
				if(count($to_add)) {
					foreach($to_add as $k=>$v) {
						$this->aq->tree[] = $v;
					}
				}
				$this->aq->tree[] = $last;
			}
			
		}
	}
*/


	protected function _get_search_type(){
		return "explnums";
	}

	
//2 Modification analyse_query et _get_search_query pour recherche des mots uniquement avant 2eme passe pour recherche des expressions literales
	protected function _get_search_query() {
		$this->_calc_query_env();
		if($this->user_query !== "*"){
//			$query = $this->aq->get_query_mot($this->object_index_key,$this->object_words_table,$this->object_words_value,'','',$this->field_restrict);
//			$query2 = $this->get_query_frag_all($query);
			$query = $this->aq->get_query_mot_explnum($this->object_index_key,$this->object_words_table,$this->object_words_value,'','',$this->field_restrict);
		} else {
			
		}
		return $query;
	}
	
	
//2 Modification analyse_query et _get_search_query pour recherche des mots uniquement avant 2eme passe pour recherche des expressions literales
// 	protected function get_query_frag_all() {
		
// 		global $charset;
// 		if (count($this->aql)) {
// 			foreach ($this->aql->tree as $k=>$term) {
				
// 				if($term->literal==1) {
// 					$s = trim($term->word);
// 					$s = preg_replace("/\s+/u", ' ' , $s);
// // 					$s = mb_strtolower($s,$charset);
// // 					$t_frag = mb_split("\s", $s);
// // 					log::print_message($t_frag);
// // 					$this->get_query_frag($t_frag);
// // 				}
				
// // 			}
// // 		}
// // 	}
	
// 	protected function get_query_frag($t_frag=array()) {
// 		if(count($t_frag)) {
// 			$first=0;
// 			$last=count($t_frag)*1-1;
// 			$t=array();
			
// 			$t0=microtime(true);
			
// 			foreach($t_frag as $k=>$frag) {
			
// 				$t[$k]['md5'] = md5(microtime(true));
// 				$t[$k]['fragment'] = $frag;
// 				if($first==$last) {
// 					$qt = "create temporary table frag_searcher_".$t[$k]['md5']." (num_obj int,position int, unique using btree(num_obj,position)) engine=memory 
// 							(select num_obj,position*1+1 as position from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where fragment like ('".addslashes($t_frag[$k])."%'))
// 							union
// 							(select num_obj,position*1+1 as position from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where rfragment like reverse('%".addslashes($t_frag[$k])."'))";
// 				} else if($k==$first) {
// 					$qt = "create temporary table frag_searcher_".$t[$k]['md5']." (num_obj int, position int, unique using btree(num_obj,position)) engine=memory 
// 							select num_obj,position*1+1 as position from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where rfragment like reverse('%".addslashes($t_frag[$k])."')";
// 				} else if($k==$last) {
// 					$qt = "create temporary table frag_searcher_".$t[$k]['md5']." (num_obj int, position int,unique using btree(num_obj,position)) engine=memory
// 							select num_obj,position*1+1 as position from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where fragment like ('".addslashes($t_frag[$k])."%') and concat(num_obj,',',position) in (select concat(num_obj,',',position) from frag_searcher_".$t[$k-1]['md5'].")";
// 				} else {
// 					$qt = "create temporary table frag_searcher_".$t[$k]['md5']." (num_obj int, position int,unique using btree(num_obj,position)) engine=memory
// 							select num_obj,position*1+1 as position from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where fragment = '".addslashes($t_frag[$k])."' and concat(num_obj,',',position) in (select concat(num_obj,',',position) from frag_searcher_".$t[$k-1]['md5'].")";
// 				}

// 				/*
// 				if($first==$last) {
// 					$qt = "create temporary table frag_searcher_".$t[$k]['md5']." (op float, index using btree(op)) engine=memory
// 							(select concat(num_obj,',',position*1+1) as op from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where fragment like ('".addslashes($t_frag[$k])."%'))
// 							union
// 							(select concat(num_obj,',',position*1+1) as op from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where rfragment like reverse('%".addslashes($t_frag[$k])."'))";
// 				} else if($k==$first) {
// 					$qt = "create temporary table frag_searcher_".$t[$k]['md5']." (op float, index using btree(op)) engine=memory
// 							select concat(num_obj,',',position*1+1) as op from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where rfragment like reverse('%".addslashes($t_frag[$k])."')";
// 				} else if($k==$last) {
// 					$qt = "create temporary table frag_searcher_".$t[$k]['md5']." (op float, index using btree(op)) engine=memory
// 							select concat(num_obj,',',position*1+1) as op from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where fragment like ('".addslashes($t_frag[$k])."%') and concat(num_obj,',',position*1+1) in (select op from frag_searcher_".$t[$k-1]['md5'].")";
// 				} else {
// 					$qt = "create temporary table frag_searcher_".$t[$k]['md5']." (op float, index using btree(op)) engine=memory
// 							select concat(num_obj,',',position*1+1) as op from fragments join explnum_fields_global_index_w_fragments on num_fragment=id_fragment where fragment = '".addslashes($t_frag[$k])."' and concat(num_obj,',',position*1+1) in (select op from frag_searcher_".$t[$k-1]['md5'].")";
// 				}
// */				
				
// 				$t[$k]['qf'] = $qf;
			
// 				$t[$k]['qt'] = $qt;
// 				$rt = pmb_mysql_query($qt);
// 				log::print_message($qt);
// 				$t[$k]['time'] = microtime(true);
				
// 				if($k!=0) {
// 					log::print_message("Tps requete = ".(($t[$k]['time'])*1 - $t[$k-1]['time']))." s";
// 				} else {
// 					log::print_message("Tps requete = ".(($t[$k]['time'])*1 - $t0)." s");
// 				}
// 				if(pmb_mysql_error()) {
// 					log::print_message(pmb_mysql_error());
// 				}
// 				//$rd = pmb_mysql_query($qd);
			
// 			}
			
// 			$ql = "select distinct(num_obj) from frag_searcher_".$t[$k]['md5'];
			
// 			$rl = pmb_mysql_query($ql);
// 			$x = pmb_mysql_num_rows($rl);
// 			$object_ids='';
// 			if($x) {
// 				$i=0;
// 				while($row=pmb_mysql_fetch_object($rl)) {
// 					if($object_ids!="") $object_ids.=",";
// 					$object_ids.=$row->num_obj;
// 				}
// 			}
			
// 			$t1 = microtime(true);
// 			log::print_message("Nb de résultats= ".$x);
// 			log::print_message("Liste des résultats= ".$object_ids);
// 			log::print_message("Tps requetes = ".($t1-$t0)*1)." s";
// 			@pmb_mysql_free_result($res);
// 			log::print_message('*******************************');
			
// 		}
// 	}
	
	
//1 Modification analyse_query et _get_search_query pour recherche des mots dans les expressions litérales avant restriction avec index_wew
/*	
	protected function _get_search_query() {
		
		$this->_calc_query_env();
		if($this->user_query !== "*"){
$t0 = microtime(true);
			$q1 = $this->aq->get_query_mot($this->object_index_key,$this->object_words_table,$this->object_words_value,$this->object_fields_table,$this->object_fields_value,$this->field_restrict);
$t1 = microtime(true);
$r1 = pmb_mysql_query($q1);
echo 'duree recherche sur les mots ='.(($t1-$t0)*1).'<br />';

			if($this->has_literal) {

				$q2 = 'select '.$this->object_index_key.' from '.$this->object_fields_table;
				$q2.= ' where '.$this->object_index_key.' in ('.$q1.') '; 
				foreach($this->original_aq->tree as $k=>$term) {
					if($term->literal==1) {
						//$q2.= ' and '.$this->object_fields_value.' like "%'.addslashes($term->word).'%"';
						$q2.= " and match(".$this->object_fields_value.") against ('\"".addslashes($term->word)."\"' in boolean mode)"; 
					}
				}
				$query = $q2;
			}
$r2 = pmb_mysql_query($q2);
$t2 = microtime(true);			
echo 'duree recherche sur l\'index ='.(($t2-$t1)*1).'<br />';
		}else{
			$query = $this->get_full_results_query();
		}
		echo $query;
		return $query;
	}
*/	
	
// 	public function get_aq() {
// 		return $this->aq;
// 	}

/*	
	protected function _get_search_query(){
		$this->_calc_query_env();
		if($this->user_query === "*"){
			$query = $this->get_full_results_query();
		}else{
			$query = $this->aq->get_query_mot($this->object_index_key,$this->object_words_table,$this->object_words_value,'','',$this->field_restrict);
		}
		return $query;
	}
*/
}