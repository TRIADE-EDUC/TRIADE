<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_authors.class.php,v 1.2 2016/06/27 10:30:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_records_authors extends searcher_records {
	
	public function __construct($user_query){
		parent::__construct($user_query);
		$this->field_restrict[]= array(
				'field' => "code_champ",
				'values' => array(27,28,29),
				'op' => "and",
				'not' => false
		);
	}
	
	protected function _get_search_type(){
		return parent::_get_search_type()."_authors";
	}
	
	public static function get_full_query_from_authority($id) {
		$query = parent::get_full_query_from_authority($id)." notice_id in (select distinct responsability_notice as notice_id from responsability join authors on responsability_author=author_id where responsability_author=".$id.")";
		return $query;
	}
}