<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_authpersos.class.php,v 1.8 2018-03-08 16:06:08 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_authpersos extends searcher_autorities {

	public function __construct($user_query){
		$this->authority_type = AUT_TABLE_AUTHPERSO;
		parent::__construct($user_query);
		$this->object_table = "authperso_authorities";
		$this->object_table_key = "id_authperso_authority";
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_authpersos";
	}
	
	protected function get_full_results_query(){
		global $mode;
		
		$query = 'select id_authority from authorities join '.$this->object_table.' on authorities.num_object = '.$this->object_table_key;
		if($mode) {
			$query .= ' and authperso_authority_authperso_num ='.($mode-1000);
		}
		return $query;
	}
	
	protected function _get_authorities_filters(){
		global $id_authperso;
		$filters = parent::_get_authorities_filters();
		if($id_authperso*1){
			$filters[] = $this->object_table.'.authperso_authority_authperso_num='.($id_authperso*1);
		}
		return $filters;
	}
	
	public function get_authority_tri() {
		return 'authperso_index_infos_global';
	}
	
}