<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_publishers.class.php,v 1.2 2021/03/16 08:53:34 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "$class_path/searcher/searcher_records.class.php";

class searcher_records_publishers extends searcher_records {

	public function __construct($user_query) {
	    
		parent::__construct($user_query);
		
		$this->field_restrict[] = array(
				'field' => "code_champ",
				'values' => array(19),
				'op' => "and",
				'not' => false
		);
		
	}
	
	protected function _get_search_type() {
		return parent::_get_search_type() . "_publishers";
	}
}