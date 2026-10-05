<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_categories.class.php,v 1.6 2018/06/21 11:26:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_records_categories extends searcher_records {
	
	public function __construct($user_query){
		global $lang;
		parent::__construct($user_query);
		$this->field_restrict=array();
		$this->field_restrict[]= array(
			'field' => "code_champ",
			'values' => 25,
			'op' => "and",
			'not' => false
		);
	}
	
	protected function _get_search_type(){
		return parent::_get_search_type()."_categories";
	}
	
	protected function get_full_results_query(){
		global $lang;
		
		return 'select notice_id as id_notice from notices join notices_categories on notcateg_notice = notice_id join categories on categories.num_noeud = notices_categories.num_noeud where langue=\''.$lang.'\' '.$this->_get_typdoc_filter(true);
	}
	
	public function get_full_query(){
		global $lang;
		
		if($this->user_query === "*"){
			return 'select notice_id as '.$this->object_key.', 100 as pert from notices join notices_categories on notcateg_notice = notice_id join categories on categories.num_noeud = notices_categories.num_noeud where langue=\''.$lang.'\' '.$this->_get_typdoc_filter(true);
		}
		return parent::get_full_query();
	}
	
	public static function get_full_query_from_authority($id) {
		$query = parent::get_full_query_from_authority($id)." notice_id in (select distinct notice_id from notices_categories join notices on notcateg_notice = notice_id where notices_categories.num_noeud=".$id.")";
		return $query;
	}
}