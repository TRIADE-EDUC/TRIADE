<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_authors.class.php,v 1.2 2018-10-08 13:59:39 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_authors extends searcher_autorities {

	public function __construct($user_query){
		$this->authority_type = AUT_TABLE_AUTHORS;
		parent::__construct($user_query);
		$this->object_table = "authors";
		$this->object_table_key = "author_id";
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_authors";
	}
	
	protected function _get_authorities_filters(){
		global $type_autorite;
		
		$filters = parent::_get_authorities_filters();
		if ($type_autorite && ($type_autorite != '7')) {
			$filters[] = 'author_type = "'.$type_autorite.'"';
		}
		return $filters;
	}
	
	protected function _get_sign_elements($sorted=false) {
		global $type_autorite;
		$str_to_hash = parent::_get_sign_elements($sorted);
		$str_to_hash .= "&type_autorite=".$type_autorite;
		return $str_to_hash;
	}
	
	public function get_authority_tri() {
		return ' index_author';
	}

}