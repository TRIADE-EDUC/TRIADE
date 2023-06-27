<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_titres_uniformes.class.php,v 1.2 2018-10-08 13:59:39 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_titres_uniformes extends searcher_autorities {

	public function __construct($user_query){
		$this->authority_type = AUT_TABLE_TITRES_UNIFORMES;
		parent::__construct($user_query);
		$this->object_table = "titres_uniformes";
		$this->object_table_key = "tu_id";
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_titres_uniformes";
	}

	protected function _get_authorities_filters(){
		global $oeuvre_nature_selector, $oeuvre_type_selector;
		
		$filters = parent::_get_authorities_filters();
		if ($oeuvre_nature_selector) {
			$filters[] = 'tu_oeuvre_nature = "'.$oeuvre_nature_selector.'"';
		}
		if ($oeuvre_type_selector) {
			$filters[] = 'tu_oeuvre_type = "'.$oeuvre_type_selector.'"';
		}
		return $filters;
	}
	
	protected function _get_sign_elements($sorted=false) {
		global $oeuvre_nature_selector, $oeuvre_type_selector;
		$str_to_hash = parent::_get_sign_elements($sorted);
		$str_to_hash .= "&oeuvre_nature_selector=".$oeuvre_nature_selector."&oeuvre_type_selector=".$oeuvre_type_selector;
		return $str_to_hash;
	}
	
	public function get_authority_tri() {
		return ' index_tu ';
	}
}