<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_titres_uniformes.class.php,v 1.3 2016/06/27 10:30:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_records_titres_uniformes extends searcher_records {
	
	public function __construct($user_query){
		parent::__construct($user_query);
		$this->field_restrict[]= array(
				'field' => "code_champ",
				'values' => array(26,123,124,125,126,127,128),
				'op' => "and",
				'not' => false
		); ;
	}
	
	protected function _get_search_type(){
		return parent::_get_search_type()."_titres_uniformes";
	}
	
	public static function get_full_query_from_authority($id) {
		$query = parent::get_full_query_from_authority($id)." notice_id in (select distinct ntu_num_notice as notice_id from  notices_titres_uniformes join titres_uniformes on ntu_num_tu=tu_id where ntu_num_tu='".$id.")";
		return $query;
	}
}